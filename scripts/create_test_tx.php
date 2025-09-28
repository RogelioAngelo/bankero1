<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Order;
use App\Models\Transaction;

// Create test order
$order = new Order();
$order->user_id = 1;
$order->subtotal = 10;
$order->discount = 0;
$order->tax = 0;
$order->total = 10;
$order->name = 'Test';
$order->phone = '000';
$order->locality = 'x';
$order->address = 'x';
$order->city = 'x';
$order->state = 'x';
$order->country = 'x';
$order->landmark = 'x';
$order->zip = '000';
$order->save();

echo "Created order id={$order->id}\n";

$tx = new Transaction();
$tx->user_id = 1;
$tx->order_id = $order->id;
$tx->checkout_id = 'test_cs_'.uniqid();
$tx->mode = 'cod';
$tx->status = 'pending';
$tx->amount = 100.00;
$tx->save();

echo "Created tx id={$tx->id}\n";
