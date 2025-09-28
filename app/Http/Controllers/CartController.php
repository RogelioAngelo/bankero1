<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Faker\Guesser\Name;
use Faker\Provider\bg_BG\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Session;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        $row = Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\\Models\\Product');

        // persist to DB
        $dbCart = $this->getOrCreateDbCart();
        DbCartItem::updateOrCreate(
            [
                'cart_id' => $dbCart->id,
                'row_id' => $row->rowId,
            ],
            [
                'product_id' => $request->id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'options' => null,
            ],
        );

        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        $this->syncRowToDb($rowId);
        return redirect()->back();
    }

    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        $this->syncRowToDb($rowId);
        return redirect()->back();
    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        // remove from DB
        DbCartItem::where('row_id', $rowId)->delete();
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        // remove db cart for this session/user
        $dbCart = $this->getOrCreateDbCart(false);
        if ($dbCart) {
            DbCartItem::where('cart_id', $dbCart->id)->delete();
            $dbCart->delete();
        }
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
                ->first();
            if (!$coupon) {
                return redirect()->back()->with('error', 'Coupon code does not exist!');
            } else {
                Session::put('coupon', [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'cart_value' => $coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success', 'Coupon has been applied!');
            }
        }
    }

    public function calculateDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * Session::get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval($subtotalAfterDiscount), 2, '.', ''),
                'tax' => number_format(floatval($taxAfterDiscount), 2, '.', ''),
                'total' => number_format(floatval($totalAfterDiscount), 2, '.', ''),
            ]);
        }
    }

    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon has been remove!');
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $address = Address::where('user_id', Auth::user()->id)
            ->where('isdefault', 1)
            ->first();
        return view('checkout', compact('address'));
    }

    public function place_an_order(Request $request)
{
    $user_id = Auth::id();
    $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();

    // If no default address, validate and create one
    if (!$address) {
        $request->validate([
            'name' => 'required|max:100',
            'phone' => 'required|numeric|digits:11',
            'zip' => 'required|numeric|digits:4',
            'state' => 'required',
            'city' => 'required',
            'address' => 'required',
            'locality' => 'required',
            'landmark' => 'required',
        ]);

        $address = new Address();
        $address->fill($request->only([
            'name', 'phone', 'zip', 'state', 'city', 'address', 'locality', 'landmark'
        ]));
        $address->country = 'Philippines';
        $address->user_id = $user_id;
        $address->isdefault = true;
        $address->save();
    }

    // Calculate totals
    $this->setAmountforCheckout();

    // For e-wallet (PayMongo) we will not create the Order yet.
    // We'll create a Transaction and store a snapshot of the cart and address in the transaction meta
    // so the webhook can create the order when payment is confirmed.

    // Handle payment mode
    if ($request->mode == 'cod') {
        // ✅ Create Order immediately for Cash on Delivery
        $order = new Order();
        $order->user_id   = $user_id;
        $order->subtotal  = $this->parseMoney(Session::get('checkout')['subtotal'] ?? 0);
        $order->discount  = $this->parseMoney(Session::get('checkout')['discount'] ?? 0);
        $order->tax       = $this->parseMoney(Session::get('checkout')['tax'] ?? 0);
        $order->total     = $this->parseMoney(Session::get('checkout')['total'] ?? 0);
        $order->fill($address->only([
            'name', 'phone', 'locality', 'address', 'city', 'state', 'country', 'landmark', 'zip'
        ]));
        $order->qr_token = Str::uuid()->toString(); // unique QR code reference
    // `orders.status` is an enum('ordered','delivered','canceled') in the DB migration.
    // Use 'ordered' as the initial state (not 'pending') to avoid enum truncation errors.
    $order->status = 'ordered';
    // COD orders are unpaid until cash is collected
    $order->payment_status = 'unpaid';
        $order->save();

        // Attach items
        foreach (Cart::instance('cart')->content() as $item) {
            OrderItem::create([
                'product_id' => $item->id,
                'order_id'   => $order->id,
                'price'      => $item->price,
                'quantity'   => $item->qty,
            ]);
        }

        // Create transaction linked to the order
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->order_id = $order->id;
        $transaction->mode = 'cod';
        $transaction->status = 'pending';
    $transaction->amount = number_format((float) $order->total, 2, '.', '');
        $transaction->save();

        // ✅ Clear cart immediately for COD
        Cart::instance('cart')->destroy();
        Session::forget(['checkout', 'coupon', 'discounts']);
        Session::put('order_id', $order->id);

        return redirect()->route('cart.order.confirmation');

    } elseif ($request->mode == 'e-wallet') {
        // ✅ PayMongo Checkout session
    // Log checkout session for debugging (dev)
    Log::info('Checkout session before PayMongo payload: ' . json_encode(Session::get('checkout')));

    // Build line items so their sum equals the session checkout total.
        // If a coupon/discount was applied, Session::get('checkout')['subtotal'] will reflect the discounted subtotal.
        $lineItems = [];
        $cartItems = Cart::instance('cart')->content();
        $originalSubtotal = (float) Cart::instance('cart')->subtotal();
        $sessionSubtotal = (float) (Session::get('checkout')['subtotal'] ?? $originalSubtotal);
        $scale = ($originalSubtotal > 0) ? ($sessionSubtotal / $originalSubtotal) : 1.0;

        $productLineCents = [];
        foreach ($cartItems as $item) {
            $effectivePrice = (float) $item->price * $scale; // apply proportional discount across items
            $cents = (int) round($effectivePrice * 100);
            $productLineCents[] = [
                'name' => (string) $item->name,
                'quantity' => (int) $item->qty,
                'amount' => $cents,
                'currency' => 'PHP',
                'product_id' => $item->id,
            ];
        }

        // Add product line items
        foreach ($productLineCents as $pl) {
            $lineItems[] = [
                'name' => $pl['name'],
                'quantity' => $pl['quantity'],
                'amount' => $pl['amount'],
                'currency' => 'PHP',
            ];
        }

        // Add tax as its own line item so PayMongo charges include VAT/tax
        $sessionTax = (float) $this->parseMoney(Session::get('checkout')['tax'] ?? 0);
        if ($sessionTax > 0) {
            $lineItems[] = [
                'name' => 'Tax',
                'quantity' => 1,
                'amount' => (int) round($sessionTax * 100),
                'currency' => 'PHP',
            ];
        }

        // If shipping exists in checkout session, include it as well
        $sessionShipping = (float) $this->parseMoney(Session::get('checkout')['shipping'] ?? 0);
        if ($sessionShipping > 0) {
            $lineItems[] = [
                'name' => 'Shipping',
                'quantity' => 1,
                'amount' => (int) round($sessionShipping * 100),
                'currency' => 'PHP',
            ];
        }

        // Ensure line items sum to the total (adjust for rounding differences)
        $totalCents = (int) round((float) $this->parseMoney(Session::get('checkout')['total'] ?? Cart::instance('cart')->total()) * 100);
        $sumCents = 0;
        foreach ($lineItems as $li) {
            $sumCents += (int) $li['amount'] * (int) $li['quantity'];
        }

        $diff = $totalCents - $sumCents;
        if ($diff !== 0 && count($lineItems) > 0) {
            // Adjust the last line item to account for rounding differences
            $lastIndex = count($lineItems) - 1;
            $lineItems[$lastIndex]['amount'] = $lineItems[$lastIndex]['amount'] + $diff;
            // If adjustment makes last item negative, clamp to zero and try to distribute (rare)
            if ($lineItems[$lastIndex]['amount'] < 0) {
                $lineItems[$lastIndex]['amount'] = 0;
            }
        }

        // Prepare a snapshot of cart items for transaction meta (so we can create transaction before redirect)
        $itemsSnapshot = [];
        foreach (Cart::instance('cart')->content() as $item) {
            $itemsSnapshot[] = [
                'product_id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->qty,
            ];
        }

        // Create pending transaction BEFORE calling PayMongo so we can embed the tx id in success_url
        $transaction = new Transaction();
        $transaction->user_id = $user_id;
        $transaction->mode = 'e-wallet';
        $transaction->status = 'pending';
        $transaction->amount = number_format((float) $this->parseMoney(Session::get('checkout')['total'] ?? 0), 2, '.', '');
        $transaction->meta = json_encode([
            'address' => $address->only(['name','phone','locality','address','city','state','country','landmark','zip']),
            'totals' => Session::get('checkout'),
            'items' => $itemsSnapshot,
        ]);
        $transaction->save();

        // Put the transaction id in session so order_confirmation can look up the transaction
        Session::put('transaction_id', $transaction->id);

        $payload = [
            'data' => [
                'attributes' => [
                    'line_items' => $lineItems,
                    'payment_method_types' => ['gcash', 'paymaya'],
                    // include transaction_id as query param so the redirect can be matched statelessly
                    'success_url' => route('cart.order.confirmation') . '?transaction_id=' . $transaction->id,
                    'cancel_url'  => route('cart.index'),
                    'billing' => [
                        'name'  => $address->name ?? Auth::user()->name,
                        'email' => Auth::user()->email,
                        'phone' => $address->phone ?? null,
                    ],
                    // also include metadata for provider-side traceability
                    'metadata' => ['transaction_id' => $transaction->id],
                ],
            ],
        ];

        $client = new \GuzzleHttp\Client();
        try {
            // DEV: log payload to confirm line items and amounts being sent to PayMongo
            Log::info('PayMongo checkout payload: ' . json_encode($payload));
            $response = $client->post('https://api.paymongo.com/v1/checkout_sessions', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
                ],
                'body' => json_encode($payload),
            ]);

            $result = json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Try to decode the response body for a clearer message
            $body = (string) $e->getResponse()?->getBody() ?? '';
            Log::error('PayMongo checkout_sessions error: ' . $body);
            return back()->with('error', 'Unable to initiate e-wallet payment. Please try again or contact support.');
        }

        if (!empty($result['data']['attributes']['checkout_url'])) {
            // Update transaction with provider checkout id for better matching later
            try {
                if (isset($result['data']['id']) && isset($transaction) && $transaction instanceof Transaction) {
                    $transaction->checkout_id = $result['data']['id'];
                    $transaction->save();
                }
            } catch (\Throwable $t) {
                Log::warning('Failed to persist checkout_id to transaction: ' . $t->getMessage());
            }

            // ✅ Do not clear cart yet — wait for webhook to confirm payment and create the order
            return redirect($result['data']['attributes']['checkout_url']);
        }

        return back()->with('error', 'Unable to initiate PayMongo checkout.');
    }

    return back()->with('error', 'Invalid payment mode selected.');
}


    protected function parseMoney($value)
    {
        if (is_null($value)) {
            return 0;
        }
        // remove thousand separators and non-numeric characters except dot and minus
        $clean = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($clean === '') {
            return 0;
        }
        return number_format((float) $clean, 2, '.', '');
    }

    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        }

        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
                // shipping may be set elsewhere; default to 0
                'shipping' => Session::get('checkout.shipping', 0),
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => Cart::instance('cart')->subtotal(),
                'tax' => Cart::instance('cart')->tax(),
                'total' => Cart::instance('cart')->total(),
                'shipping' => 0,
            ]);
        }
    }

    public function order_confirmation()
    {
        // Accept transaction_id from query param (success_url contains it) so we can match stateless redirects
        if (request()->query('transaction_id')) {
            Session::put('transaction_id', request()->query('transaction_id'));
        }

        // First, check if we have an explicit order_id in session (COD flow)
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }

        // Next, check if we have a transaction_id stored (e-wallet flow). The webhook will create the order.
        if (Session::has('transaction_id')) {
            $transaction = Transaction::find(Session::get('transaction_id'));
            if ($transaction && $transaction->order_id) {
                $order = Order::find($transaction->order_id);
                // Clear the session cart so items the user just paid for are removed from their visible cart
                try {
                    Cart::instance('cart')->destroy();
                } catch (\Exception $e) {
                    // non-fatal: log if session cart couldn't be destroyed
                    Log::warning('Unable to clear session cart after successful e-wallet order: ' . $e->getMessage());
                }

                // clear session keys and redirect to confirmation
                Session::forget(['transaction_id', 'checkout', 'coupon', 'discounts']);
                return view('order-confirmation', compact('order'));
            }

            // If transaction exists but order not yet created, try server-side verification with PayMongo
            if ($transaction && ! $transaction->order_id) {
                try {
                    $checkoutId = $transaction->checkout_id ?? $transaction->paymongo_session_id ?? null;
                    $paid = false;
                    $providerData = null;

                    if ($checkoutId) {
                        $resp = Http::withToken(env('PAYMONGO_SECRET_KEY'))
                            ->get('https://api.paymongo.com/v1/checkout_sessions/' . $checkoutId);
                        $providerData = $resp->json();

                        $attrs = $providerData['data']['attributes'] ?? [];
                        if (isset($attrs['status']) && strcasecmp($attrs['status'], 'paid') === 0) {
                            $paid = true;
                        }

                        // payments array or nested data
                        if (! $paid && isset($attrs['payments']) && is_array($attrs['payments'])) {
                            foreach ($attrs['payments'] as $p) {
                                $pstatus = $p['attributes']['status'] ?? $p['status'] ?? null;
                                if (is_string($pstatus) && stripos($pstatus, 'paid') !== false) { $paid = true; break; }
                            }
                        }

                        if (! $paid) {
                            // try some nested fields commonly used in webhook payloads
                            $bodyStr = json_encode($providerData);
                            if (stripos($bodyStr, '"paid"') !== false) $paid = true;
                        }
                    }

                    if ($paid) {
                        // Idempotent creation: only if transaction has no order
                        $meta = [];
                        if ($transaction->meta) {
                            $meta = is_array($transaction->meta) ? $transaction->meta : (array) json_decode((string) $transaction->meta, true);
                        }

                        if (! empty($meta)) {
                            $order = new Order();
                            $order->user_id = $transaction->user_id;
                            $order->subtotal = (float) $this->parseMoney($meta['totals']['subtotal'] ?? 0);
                            $order->discount = (float) $this->parseMoney($meta['totals']['discount'] ?? 0);
                            $order->tax = (float) $this->parseMoney($meta['totals']['tax'] ?? 0);
                            $order->total = (float) $this->parseMoney($meta['totals']['total'] ?? ($transaction->amount ?? 0));
                            $order->fill($meta['address'] ?? []);
                            $order->qr_token = Str::uuid()->toString();
                            $order->status = 'ordered';
                            try {
                                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status')) $order->payment_status = 'paid';
                            } catch (\Throwable $t) {
                                Log::warning('payment_status column absent: ' . $t->getMessage());
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

                            // persist provider ids if available
                            try {
                                if ($providerData) {
                                    $payment = $providerData['data']['attributes']['data']['attributes'] ?? [];
                                    $paymentId = $payment['id'] ?? ($providerData['data']['id'] ?? null);
                                    $paymentIntentId = $payment['payment_intent_id'] ?? null;
                                    $sourceId = $payment['source']['id'] ?? ($payment['source_id'] ?? null);
                                    if ($paymentId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'paymongo_payment_id')) $transaction->paymongo_payment_id = $paymentId;
                                    if ($paymentIntentId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'payment_intent_id')) $transaction->payment_intent_id = $paymentIntentId;
                                    if ($sourceId && \Illuminate\Support\Facades\Schema::hasColumn('transactions', 'paymongo_source_id')) $transaction->paymongo_source_id = $sourceId;
                                }
                            } catch (\Throwable $t) {
                                Log::warning('Could not persist provider ids during confirmation: ' . $t->getMessage());
                            }

                            $transaction->order_id = $order->id;
                        }

                        $transaction->status = 'approved';
                        $transaction->save();

                        // Clear session cart and session keys
                        try { Cart::instance('cart')->destroy(); } catch (\Exception $e) { Log::warning('Unable to clear session cart after confirm: ' . $e->getMessage()); }
                        Session::forget(['transaction_id', 'checkout', 'coupon', 'discounts']);

                        return view('order-confirmation', compact('order'));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to verify PayMongo checkout session: ' . $e->getMessage());
                }
            }

            // If still not paid, show processing page
            return view('order-processing', ['transaction' => $transaction]);
        }

        return redirect()->route('cart.index');
    }

    // DEV: build PayMongo payload for a given user id using DB cart snapshot
    public function debugPaymongoPayload($userId)
    {
        // load user's DB cart items
        $dbCart = DbCart::where('user_id', $userId)->first();
        $cartItems = [];
        if ($dbCart) {
            $dbItems = DbCartItem::where('cart_id', $dbCart->id)->get();
            foreach ($dbItems as $it) {
                $cartItems[] = (object)[
                    'id' => $it->product_id,
                    'name' => optional($it->product)->name ?? 'product',
                    'qty' => $it->quantity,
                    'price' => $it->price,
                ];
            }
        }

        // simulate checkout session values for this user
        $checkout = [
            'discount' => Session::get('discounts.discount', 0),
            'subtotal' => Session::get('discounts.subtotal', Cart::instance('cart')->subtotal()),
            'tax' => Session::get('discounts.tax', Cart::instance('cart')->tax()),
            'shipping' => Session::get('checkout.shipping', 0),
            'total' => Session::get('discounts.total', Cart::instance('cart')->total()),
        ];

        // build payload like place_an_order
        $originalSubtotal = (float) collect($cartItems)->sum(function ($i) { return (float) $i->price * $i->qty; });
        $sessionSubtotal = (float) $checkout['subtotal'];
        $scale = ($originalSubtotal > 0) ? ($sessionSubtotal / $originalSubtotal) : 1.0;

        $lineItems = [];
        foreach ($cartItems as $item) {
            $effectivePrice = (float) $item->price * $scale;
            $cents = (int) round($effectivePrice * 100);
            $lineItems[] = [
                'name' => (string) $item->name,
                'quantity' => (int) $item->qty,
                'amount' => $cents,
                'currency' => 'PHP',
            ];
        }

        $tax = (float) $this->parseMoney($checkout['tax']);
        if ($tax > 0) {
            $lineItems[] = ['name' => 'Tax', 'quantity' => 1, 'amount' => (int) round($tax * 100), 'currency' => 'PHP'];
        }

        $shipping = (float) $this->parseMoney($checkout['shipping']);
        if ($shipping > 0) {
            $lineItems[] = ['name' => 'Shipping', 'quantity' => 1, 'amount' => (int) round($shipping * 100), 'currency' => 'PHP'];
        }

        // adjust rounding
        $totalCents = (int) round((float) $this->parseMoney($checkout['total']) * 100);
        $sumCents = 0;
        foreach ($lineItems as $li) $sumCents += $li['amount'] * (int)$li['quantity'];
        $diff = $totalCents - $sumCents;
        if ($diff !== 0 && count($lineItems) > 0) {
            $last = count($lineItems) - 1;
            $lineItems[$last]['amount'] += $diff;
        }

        $payload = ['data' => ['attributes' => ['line_items' => $lineItems, 'payment_method_types' => ['gcash','paymaya']]]];
        return response()->json($payload);
    }

    /**
     * Public QR scan endpoint. Marks the order as received when a valid token is presented.
     */
    public function scanQr($token)
    {
        if (!$token) {
            abort(404);
        }

        $order = Order::where('qr_token', $token)->first();
        if (!$order) {
            return view('order-qr-confirmation', ['status' => 'invalid']);
        }

        if ($order->qr_scanned_at) {
            return view('order-qr-confirmation', ['status' => 'already_scanned', 'order' => $order]);
        }

        $order->qr_scanned_at = now();
        $order->status = 'delivered';
        $order->save();

        return view('order-qr-confirmation', ['status' => 'ok', 'order' => $order]);
    }

    /**
     * Return JSON status for a transaction (used by order-processing page polling)
     */
    public function transactionStatus($transactionId)
    {
        $tx = Transaction::find($transactionId);
        if (! $tx) {
            return response()->json(['error' => 'not_found'], 404);
        }

        $data = [
            'id' => $tx->id,
            'status' => $tx->status,
            'order_id' => $tx->order_id,
        ];

        if ($tx->order_id) {
            $order = Order::find($tx->order_id);
            if ($order) {
                $data['order'] = [
                    'id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status ?? null,
                ];
            }
        }

        return response()->json($data);
    }

    public function update(Request $request, $rowId)
    {
        $item = Cart::instance('cart')->get($rowId);

        // Always start from current cart qty
        $quantity = $item->qty;

        if ($request->action === 'increase') {
            $quantity++;
        } elseif ($request->action === 'decrease' && $quantity > 1) {
            $quantity--;
        } else {
            // If user typed a number manually in the input
            $quantity = (int) $request->input('quantity', $quantity);
        }

        Cart::instance('cart')->update($rowId, $quantity);

        // persist updated qty to DB
        $this->syncRowToDb($rowId);

        return back()->with('success', '');
    }
    protected function getOrCreateDbCart($createIfNotExists = true)
    {
        $sessionId = session()->getId();
        $userId = Auth::check() ? Auth::user()->id : null;

        $query = DbCart::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $cart = $query->first();
        if (!$cart && $createIfNotExists) {
            $cart = DbCart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
            ]);
        }

        return $cart;
    }

    protected function syncRowToDb($rowId)
    {
        $row = Cart::instance('cart')->get($rowId);
        if (!$row) {
            return;
        }

        $dbCart = $this->getOrCreateDbCart();
        DbCartItem::updateOrCreate(
            [
                'cart_id' => $dbCart->id,
                'row_id' => $rowId,
            ],
            [
                'product_id' => $row->id,
                'quantity' => $row->qty,
                'price' => $row->price,
                'options' => $row->options ?? null,
            ],
        );
    }
}
