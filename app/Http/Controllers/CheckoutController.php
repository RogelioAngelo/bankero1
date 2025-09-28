namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Transaction;

class CheckoutController extends Controller
{
    public function createCheckout(Request $request)
    {
        $cartTotal = \Cart::subtotal(0, '', ''); // if using Shoppingcart
        $amount = intval($cartTotal * 100); // PayMongo expects cents

        $user = Auth::user();

        // For PayMongo e-wallet flows we create a Transaction and defer Order creation to the webhook
        // Call PayMongo API
        $response = Http::withToken(env('PAYMONGO_SECRET_KEY'))
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [

                        'payment_method_types' => ['gcash', 'paymaya', 'card'],

                        'billing' => [
                            'name' => $user->name,
                            'email' => $user->email,
                            'phone' => $user->phone ?? '09123456789',
                            'address' => [
                                'line1' => $user->address ?? 'N/A',
                                'city' => $user->city ?? 'N/A',
                                'postal_code' => $user->postal_code ?? '0000',
                                'country' => 'PH',
                            ]
                        ]
                    ]
                ]
            ]);

        $checkout = $response->json();

        // Normalize amounts: ensure decimal string without thousands-separators
        $subtotal = (string) \Cart::subtotal(0, '', '');
        $normalize = function ($v) {
            $clean = preg_replace('/[^0-9.\-]/', '', (string) $v);
            if ($clean === '') return '0.00';
            return number_format((float) $clean, 2, '.', '');
        };
        $normSubtotal = $normalize($subtotal);
        $normTotal = $normalize($cartTotal);

        Transaction::create([
            'user_id' => $user->id,
            'checkout_id' => $checkout['data']['id'] ?? null,
            'paymongo_session_id' => $checkout['data']['id'] ?? null,
            'amount' => $normTotal,
            'mode' => 'e-wallet',
            'status' => 'pending',
            'meta' => json_encode([
                'totals' => [
                    'subtotal' => $normSubtotal,
                    'total' => $normTotal,
                ],
                'items' => \Cart::content()->map(function ($item) {
                    return [
                        'product_id' => $item->id,
                        'name'       => $item->name,
                        'price'      => (string) $item->price,
                        'quantity'   => $item->qty,
                    ];
                })->toArray(),
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ]
            ]),
        ]);


        // Redirect user to PayMongo checkout
        return redirect($checkout['data']['attributes']['checkout_url']);
    }

    // Settle payment for an existing unpaid order
    public function settleOrder(Request $request, $orderId)
    {
        $order = \App\Models\Order::where('id', $orderId)->where('user_id', auth()->id())->firstOrFail();
        if ($order->payment_status === 'paid') {
            return back()->with('status', 'Order already paid.');
        }

        // Try to reuse existing transaction for this order, or create new
        $transaction = Transaction::where('order_id', $order->id)->first();
        $cartTotal = $order->total;

        // Call PayMongo API to create a checkout session
        $response = Http::withToken(env('PAYMONGO_SECRET_KEY'))
            ->post('https://api.paymongo.com/v1/checkout_sessions', [
                'data' => [
                    'attributes' => [
                        'line_items' => [[
                            'currency' => 'PHP',
                            'amount' => intval($cartTotal * 100),
                            'name' => 'Order #' . $order->id,
                            'quantity' => 1,
                        ]],
                        'payment_method_types' => ['gcash', 'paymaya', 'card'],
                        'success_url' => route('user.order.details', $order->id),
                        'cancel_url' => route('user.order.details', $order->id),
                    ]
                ]
            ]);

        $checkout = $response->json();

        if (! $transaction) {
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'order_id' => $order->id,
                'checkout_id' => $checkout['data']['id'] ?? null,
                'paymongo_session_id' => $checkout['data']['id'] ?? null,
                'amount' => number_format($cartTotal, 2, '.', ''),
                'mode' => 'e-wallet',
                'status' => 'pending',
                'meta' => json_encode([
                    'totals' => ['total' => number_format($cartTotal, 2, '.', '')],
                ])
            ]);
        } else {
            $transaction->checkout_id = $checkout['data']['id'] ?? $transaction->checkout_id;
            $transaction->paymongo_session_id = $checkout['data']['id'] ?? $transaction->paymongo_session_id;
            $transaction->status = 'pending';
            $transaction->save();
        }

        return redirect($checkout['data']['attributes']['checkout_url']);
    }
}
