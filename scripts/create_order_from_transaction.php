<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$txId = $argv[1] ?? null;
if (! $txId) {
    echo "Usage: php create_order_from_transaction.php <transaction_id>\n";
    exit(1);
}

$tx = App\Models\Transaction::find($txId);
if (! $tx) {
    echo "Transaction {$txId} not found\n";
    exit(1);
}

if ($tx->order_id) {
    echo "Transaction {$txId} already has order_id={$tx->order_id}\n";
    exit(0);
}

$meta = [];
if ($tx->meta) {
    $meta = is_array($tx->meta) ? $tx->meta : (array) json_decode((string) $tx->meta, true);
}

if (empty($meta)) {
    echo "Transaction {$txId} has no meta snapshot; aborting\n";
    exit(1);
}

function parseMoney($value) {
    if (is_null($value)) return 0;
    $clean = preg_replace('/[^0-9.\\-]/', '', (string) $value);
    if ($clean === '') return 0;
    return number_format((float) $clean, 2, '.', '');
}

\Illuminate\Support\Facades\DB::beginTransaction();
try {
    $order = new App\Models\Order();
    $order->user_id = $tx->user_id;
    $order->subtotal = (float) parseMoney($meta['totals']['subtotal'] ?? 0);
    $order->discount = (float) parseMoney($meta['totals']['discount'] ?? 0);
    $order->tax = (float) parseMoney($meta['totals']['tax'] ?? 0);
    $order->total = (float) parseMoney($meta['totals']['total'] ?? ($tx->amount ?? 0));
    $order->fill($meta['address'] ?? []);
    $order->qr_token = \Illuminate\Support\Str::uuid()->toString();
    $order->status = 'ordered';
    if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status')) $order->payment_status = 'paid';
    $order->save();

    if (! empty($meta['items']) && is_array($meta['items'])) {
        foreach ($meta['items'] as $item) {
            $price = isset($item['price']) ? (float) parseMoney($item['price']) : 0;
            $qty = isset($item['quantity']) ? intval(preg_replace('/[^0-9]/', '', (string) $item['quantity'])) : 0;
            App\Models\OrderItem::create([
                'product_id' => $item['product_id'] ?? null,
                'order_id' => $order->id,
                'price' => $price,
                'quantity' => $qty,
            ]);
        }
    }

    $tx->order_id = $order->id;
    $tx->status = 'approved';
    $tx->save();

    // Trim user's DB cart
    $dbCart = App\Models\Cart::where('user_id', $tx->user_id)->first();
    if ($dbCart && ! empty($meta['items']) && is_array($meta['items'])) {
        foreach ($meta['items'] as $item) {
            $productId = $item['product_id'] ?? null;
            $orderedQty = isset($item['quantity']) ? intval(preg_replace('/[^0-9]/', '', (string) $item['quantity'])) : 0;
            if (! $productId || $orderedQty <= 0) continue;
            $cartItems = App\Models\CartItem::where('cart_id', $dbCart->id)
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
    \Illuminate\Support\Facades\DB::commit();
    echo "Created order {$order->id} from transaction {$txId}\n";
} catch (\Throwable $e) {
    \Illuminate\Support\Facades\DB::rollBack();
    echo "Failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
