<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Transaction;

// Find latest pending e-wallet transaction
$tx = Transaction::where('mode','e-wallet')->where('status','pending')->latest()->first();
if (!$tx) {
    echo "No pending transaction found\n";
    exit(1);
}

$payload = [
    'data' => [
        'id' => 'evt_test_'.uniqid(),
        'attributes' => [
            'type' => 'checkout_session.payment.paid',
            'data' => [
                'id' => $tx->checkout_id,
            ],
        ],
    ],
];

$url = 'http://127.0.0.1:8001/paymongo/webhook';
$ch = curl_init($url);
$json = json_encode($payload);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: '.strlen($json),
]);

$response = curl_exec($ch);
$info = curl_getinfo($ch);
$err = curl_error($ch);
curl_close($ch);

echo "Sent webhook for checkout_id: {$tx->checkout_id}\n";
if ($err) {
    echo "Curl error: $err\n";
}
echo "HTTP status: {$info['http_code']}\n";
echo "Response: $response\n";

// Reload transaction from DB and print status and order_id
$tx = $tx->fresh();
print_r(['transaction_id' => $tx->id, 'status' => $tx->status, 'order_id' => $tx->order_id]);

// If order_id exists, print the order id
if ($tx->order_id) {
    $order = \App\Models\Order::find($tx->order_id);
    if ($order) {
        print_r(['order_id' => $order->id, 'order_status' => $order->status, 'total' => $order->total]);
    }
}
