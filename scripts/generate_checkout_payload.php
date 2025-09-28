<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrapping enough Laravel to use Session and Cart facades
Illuminate\Support\Facades\Auth::shouldReceive('check')->andReturn(true);

use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

// This script assumes there is an active session and cart; instead we'll recreate values
$cartItems = [
    (object)['id' => 22, 'name' => 'try', 'qty' => 1, 'price' => 900],
];

// simulate session checkout values
Session::put('checkout', [
    'discount' => 0,
    'subtotal' => '900.00',
    'tax' => '189.00',
    'shipping' => '0',
    'total' => '1089.00',
]);

$originalSubtotal = 900.00;
$sessionSubtotal = 900.00;
$scale = ($originalSubtotal > 0) ? ($sessionSubtotal / $originalSubtotal) : 1.0;

$lineItems = [];
$productLineCents = [];
foreach ($cartItems as $item) {
    $effectivePrice = (float) $item->price * $scale;
    $cents = (int) round($effectivePrice * 100);
    $productLineCents[] = [
        'name' => (string) $item->name,
        'quantity' => (int) $item->qty,
        'amount' => $cents,
        'currency' => 'PHP',
        'product_id' => $item->id,
    ];
}

foreach ($productLineCents as $pl) {
    $lineItems[] = [
        'name' => $pl['name'],
        'quantity' => $pl['quantity'],
        'amount' => $pl['amount'],
        'currency' => 'PHP',
    ];
}

$sessionTax = (float) Session::get('checkout')['tax'];
if ($sessionTax > 0) {
    $lineItems[] = [
        'name' => 'Tax',
        'quantity' => 1,
        'amount' => (int) round($sessionTax * 100),
        'currency' => 'PHP',
    ];
}

$sessionShipping = (float) Session::get('checkout')['shipping'];
if ($sessionShipping > 0) {
    $lineItems[] = [
        'name' => 'Shipping',
        'quantity' => 1,
        'amount' => (int) round($sessionShipping * 100),
        'currency' => 'PHP',
    ];
}

$totalCents = (int) round((float) Session::get('checkout')['total'] * 100);
$sumCents = 0;
foreach ($lineItems as $li) {
    $sumCents += (int) $li['amount'] * (int) $li['quantity'];
}
$diff = $totalCents - $sumCents;
if ($diff !== 0 && count($lineItems) > 0) {
    $lastIndex = count($lineItems) - 1;
    $lineItems[$lastIndex]['amount'] = $lineItems[$lastIndex]['amount'] + $diff;
}

$payload = [
    'data' => [
        'attributes' => [
            'line_items' => $lineItems,
            'payment_method_types' => ['gcash', 'paymaya'],
            'success_url' => 'http://example.test/success',
            'cancel_url' => 'http://example.test/cancel',
            'billing' => [
                'name' => 'Test',
                'email' => 'test@example.com',
                'phone' => '123',
            ],
        ],
    ],
];

file_put_contents(__DIR__ . '/../storage/logs/checkout_payload_debug.json', json_encode($payload, JSON_PRETTY_PRINT));
echo "Payload written to storage/logs/checkout_payload_debug.json\n";
