<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

class PaymongoWebhookController extends Controller
{
    public function handlePaymongoWebhook(Request $request)
    {
        // Raw body is needed for reliable signature verification
        $rawContent = (string) $request->getContent();

        // If a webhook secret is configured, verify the incoming request's signature.
        // This is tolerant to several common header names and formats (v1=..., plain sig, etc.).
        $webhookSecret = env('PAYMONGO_WEBHOOK_SECRET');
        if ($webhookSecret) {
            $ok = $this->verifyPaymongoSignature($request, $rawContent, $webhookSecret);
            if (! $ok) {
                Log::warning('PayMongo Webhook signature verification failed. Headers: ' . json_encode($request->headers->all()));
                return response()->json(['error' => 'Invalid webhook signature'], 400);
            }
        }

        $payload = json_decode($rawContent, true) ?? $request->all();
        Log::info('PayMongo Webhook received: ' . json_encode($payload));

        // Lightweight validation and early return for non-paid events
        if (!isset($payload['data'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $eventType = $payload['data']['attributes']['type'] ?? null;
        $isPaidEvent = false;
        if (is_string($eventType) && strcasecmp($eventType, 'checkout_session.payment.paid') === 0) {
            $isPaidEvent = true;
        } elseif (is_string($eventType) && stripos($eventType, 'paid') !== false) {
            $isPaidEvent = true;
        } else {
            $nestedStatus = $payload['data']['attributes']['data']['attributes']['status'] ??
                            $payload['data']['attributes']['data']['status'] ?? null;
            if (is_string($nestedStatus) && stripos($nestedStatus, 'paid') !== false) {
                $isPaidEvent = true;
                Log::info('PayMongo Webhook: detected paid via nested status field: ' . $nestedStatus);
            }
        }

        if (! $isPaidEvent) return response()->json(['ok' => true], 200);

        // Extract checkout id
        $checkoutId = null;
        $maybe = $payload['data']['attributes']['data']['id'] ?? null;
        if (is_string($maybe) && str_starts_with($maybe, 'cs_')) $checkoutId = $maybe;
        if (! $checkoutId) {
            $maybe = $payload['data']['id'] ?? null;
            if (is_string($maybe) && str_starts_with($maybe, 'cs_')) $checkoutId = $maybe;
        }
        if (! $checkoutId) $checkoutId = $this->searchForCheckoutId($payload);

        if (! $checkoutId) {
            // If the payload contains our metadata.transaction_id (we include this at checkout), try that first.
            try {
                $metaTx = $payload['data']['attributes']['data']['attributes']['metadata']['transaction_id'] ?? null;
                if ($metaTx) {
                    // sometimes metadata may be numeric string; attempt to find Transaction by id
                    $maybeId = is_string($metaTx) && preg_match('/^\d+$/', $metaTx) ? intval($metaTx) : null;
                    if ($maybeId) {
                        $tx = Transaction::find($maybeId);
                        if ($tx) {
                            Log::info('PayMongo Webhook: matched transaction via metadata.transaction_id=' . $maybeId);
                            $transaction = $tx;
                        }
                    }
                }
            } catch (\Throwable $t) {
                Log::warning('PayMongo Webhook: error reading metadata.transaction_id: ' . $t->getMessage());
            }

            // If metadata didn't match, continue with other matching strategies
            // Try to match by payment / payment_intent / source ids if checkout_id is absent
            $payment = [];
            if (isset($payload['data']['attributes']['data']['attributes']) && is_array($payload['data']['attributes']['data']['attributes'])) {
                $payment = $payload['data']['attributes']['data']['attributes'];
            }
            $paymentId = null;
            $paymentIntentId = null;
            $sourceId = null;
            if (! empty($payment)) {
                $paymentId = $payment['id'] ?? ($payload['data']['id'] ?? null);
                $paymentIntentId = $payment['payment_intent_id'] ?? null;
                if (isset($payment['source']) && is_array($payment['source'])) {
                    $sourceId = $payment['source']['id'] ?? ($payment['source_id'] ?? null);
                } else {
                    $sourceId = $payment['source_id'] ?? null;
                }
            } else {
                $paymentId = $payload['data']['id'] ?? null;
            }

            $transaction = null;
            // Helper to try column then meta
            $tryFind = function ($colNames, $value) {
                foreach ($colNames as $col) {
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasColumn('transactions', $col)) {
                            $q = Transaction::where($col, $value)->first();
                            if ($q) return $q;
                        }
                    } catch (\Throwable $t) {
                        // ignore and continue
                    }
                }
                // fallback: search JSON meta as string
                try {
                    return Transaction::where('meta', 'like', '%' . addslashes($value) . '%')->first();
                } catch (\Throwable $t) {
                    return null;
                }
            };

            if ($paymentId) {
                $transaction = $tryFind(['paymongo_payment_id', 'payment_id', 'paymongo_payment'], $paymentId);
            }

            if (! $transaction && $paymentIntentId) {
                $transaction = $tryFind(['payment_intent_id', 'paymongo_payment_intent'], $paymentIntentId);
            }

            if (! $transaction && $sourceId) {
                $transaction = $tryFind(['paymongo_source_id', 'paymongo_source', 'source_id'], $sourceId);
            }

            // If still not found, persist and return (for manual replay)
            if (! $transaction) {
                $this->persistUnmatchedPayload($payload, 'paid_without_checkout_id');
                return response()->json(['ok' => true], 200);
            }
        } else {
            $transaction = Transaction::where('checkout_id', $checkoutId)->first();
            if (! $transaction) {
                $this->persistUnmatchedPayload($payload, 'no_matching_transaction');
                return response()->json(['ok' => true], 200);
            }
        }

        // Idempotent: if order already created, exit
        if ($transaction->order_id) return response()->json(['ok' => true], 200);

        $meta = [];
        if ($transaction->meta) {
            $meta = is_array($transaction->meta) ? $transaction->meta : (array) json_decode((string) $transaction->meta, true);
        }

        try {
            if (! empty($meta)) {
                $order = new Order();
                $order->user_id = $transaction->user_id;
                $order->subtotal = (float) $this->parseMoney($meta['totals']['subtotal'] ?? 0);
                $order->discount = (float) $this->parseMoney($meta['totals']['discount'] ?? 0);
                $order->tax = (float) $this->parseMoney($meta['totals']['tax'] ?? 0);
                $order->total = (float) $this->parseMoney($meta['totals']['total'] ?? ($transaction->amount ?? 0));
                $order->fill($meta['address'] ?? []);
                $order->qr_token = method_exists(\Illuminate\Support\Str::class, 'uuid') ? \Illuminate\Support\Str::uuid()->toString() : uniqid();
                $order->status = 'ordered';

                try {
                    if (Schema::hasColumn('orders', 'payment_status')) $order->payment_status = 'paid';
                } catch (\Throwable $t) {
                    Log::warning('payment_status column absent or inaccessible: ' . $t->getMessage());
                }

                $order->save();

                if (! empty($meta['items']) && is_array($meta['items'])) {
                    foreach ($meta['items'] as $item) {
                        $price = isset($item['price']) ? (float) $this->parseMoney($item['price']) : 0;
                        $qty = isset($item['quantity']) ? intval(preg_replace('/[^0-9]/', '', (string) $item['quantity'])) : 0;
                        OrderItem::create([
                            'product_id' => $item['product_id'] ?? null,
                            'order_id' => $order->id,
                            'price' => $price,
                            'quantity' => $qty,
                        ]);
                    }
                }

                $transaction->order_id = $order->id;
            }

            // Persist provider IDs to the transaction for future matching
            try {
                $payment = $payload['data']['attributes']['data']['attributes'] ?? [];
                $paymentId = $payment['id'] ?? ($payload['data']['id'] ?? null);
                $paymentIntentId = $payment['payment_intent_id'] ?? null;
                $sourceId = null;
                if (isset($payment['source']) && is_array($payment['source'])) {
                    $sourceId = $payment['source']['id'] ?? null;
                } elseif (isset($payment['source_id'])) {
                    $sourceId = $payment['source_id'];
                }
                if ($paymentId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'paymongo_payment_id')) {
                    $transaction->paymongo_payment_id = $paymentId;
                }
                if ($paymentIntentId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'payment_intent_id')) {
                    $transaction->payment_intent_id = $paymentIntentId;
                }
                if ($sourceId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'paymongo_source_id')) {
                    $transaction->paymongo_source_id = $sourceId;
                }
            } catch (\Throwable $t) {
                Log::warning('Could not persist provider ids to transaction: ' . $t->getMessage());
            }

            // Normalize amount matching: provider sends minor units (e.g., 108900 for 1089.00)
            try {
                $provAmount = $payload['data']['attributes']['data']['attributes']['amount'] ?? null;
                if (! is_null($provAmount) && is_int($provAmount)) {
                    // convert minor units to decimal string
                    $converted = number_format($provAmount / 100, 2, '.', '');
                    // If transaction.amount exists, compare and log if mismatch
                    if ($transaction->amount && (float) $transaction->amount !== (float) $converted) {
                        Log::info('PayMongo webhook amount differs from transaction: payload=' . $converted . ' tx=' . $transaction->amount);
                    }
                }
            } catch (\Throwable $t) {
                Log::warning('Could not normalize provider amount: ' . $t->getMessage());
            }

            $transaction->status = 'approved';
            $transaction->save();

            // Remove items from DB cart only after successful order create
            try {
                $dbCart = DbCart::where('user_id', $transaction->user_id)->first();
                if ($dbCart && ! empty($meta['items']) && is_array($meta['items'])) {
                    foreach ($meta['items'] as $item) {
                        $productId = $item['product_id'] ?? null;
                        $orderedQty = isset($item['quantity']) ? intval(preg_replace('/[^0-9]/', '', (string) $item['quantity'])) : 0;
                        if (! $productId || $orderedQty <= 0) continue;

                        $cartItems = DbCartItem::where('cart_id', $dbCart->id)
                            ->where('product_id', $productId)
                            ->orderBy('id')
                            ->get();

                        foreach ($cartItems as $ci) {
                            if ($orderedQty <= 0) break;
                            $ciQty = intval($ci->quantity ?? 0);
                            if ($ciQty <= $orderedQty) {
                                $orderedQty -= $ciQty;
                                $ci->delete();
                            } else {
                                $ci->quantity = $ciQty - $orderedQty;
                                $ci->save();
                                $orderedQty = 0;
                            }
                        }
                    }

                    if ($dbCart->items()->count() == 0) $dbCart->delete();
                }
            } catch (\Throwable $t) {
                Log::warning('Failed to trim db cart after webhook: ' . $t->getMessage());
            }

            return response()->json(['ok' => true], 200);
        } catch (\Throwable $e) {
            Log::error('PayMongo Webhook error: ' . $e->getMessage());
            $this->persistUnmatchedPayload(['payload' => $payload, 'error' => $e->getMessage()], 'exception_during_processing');
            return response()->json(['error' => 'Webhook handling failed'], 500);
        }
    }

    /**
     * Verify PayMongo webhook signature if webhook secret is configured.
     * Tries several header names and common formats (e.g., 'v1=' prefixed values).
     * Returns true when a signature header matches the HMAC-SHA256 of the raw payload.
     */
    protected function verifyPaymongoSignature(Request $request, string $rawContent, string $secret): bool
    {
        $possibleHeaders = [
            'paymongo-signature',
            'paymongo-sign',
            'x-paymongo-signature',
            'webhook-signature',
            'signature',
            'x-signature',
            'paymongo-signature-256'
        ];

        // Compute expected HMAC (hex)
        try {
            $expected = hash_hmac('sha256', $rawContent, $secret);
        } catch (\Throwable $t) {
            Log::warning('Failed computing webhook HMAC: ' . $t->getMessage());
            return false;
        }

        foreach ($possibleHeaders as $h) {
            $header = $request->header($h);
            if (! $header) continue;

            // header may be array or string
            $values = is_array($header) ? $header : explode(',', (string) $header);
            foreach ($values as $val) {
                $val = trim($val);
                // common formats: 'v1=abcdef', 't=123,v1=abcdef' or raw hex
                if (stripos($val, 'v1=') !== false) {
                    // extract v1 token
                    if (preg_match('/v1=([0-9a-fA-F]+)/', $val, $m)) {
                        if (hash_equals($expected, $m[1])) return true;
                    }
                }

                // if header contains 'sig=' style
                if (stripos($val, 'sig=') !== false) {
                    if (preg_match('/sig=([0-9a-fA-F]+)/', $val, $m)) {
                        if (hash_equals($expected, $m[1])) return true;
                    }
                }

                // otherwise compare raw hex
                if (preg_match('/^[0-9a-fA-F]{64}$/', $val)) {
                    if (hash_equals($expected, $val)) return true;
                }
            }
        }

        return false;
    }

    protected function parseMoney($value)
    {
        if (is_null($value)) return 0;
        $clean = preg_replace('/[^0-9.\\-]/', '', (string) $value);
        if ($clean === '') return 0;
        return number_format((float) $clean, 2, '.', '');
    }

    protected function searchForCheckoutId($data)
    {
        if (is_null($data)) return null;
        if (is_string($data) && preg_match('/\\bcs_[A-Za-z0-9_\\-]+\\b/', $data, $m)) return $m[0];
        if (is_array($data)) {
            foreach ($data as $v) {
                $found = $this->searchForCheckoutId($v);
                if ($found) return $found;
            }
        }
        if (is_object($data)) {
            foreach (get_object_vars($data) as $v) {
                $found = $this->searchForCheckoutId($v);
                if ($found) return $found;
            }
        }
        return null;
    }

    protected function persistUnmatchedPayload($payload, $reason = 'unmatched')
    {
        try {
            $dir = storage_path('app/paymongo_unmatched');
            if (! file_exists($dir)) mkdir($dir, 0755, true);
            $fn = $dir . '/' . $reason . '_' . date('Ymd_His') . '_' . uniqid() . '.json';
            file_put_contents($fn, json_encode($payload, JSON_PRETTY_PRINT));
        } catch (\Throwable $t) {
            Log::warning('Failed to persist unmatched webhook payload: ' . $t->getMessage());
        }
    }
}
