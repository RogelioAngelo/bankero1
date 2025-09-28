<?php
// Quick script to print order details (timestamps) by id
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if ($argc < 2) {
    echo "Usage: php print_order.php {order_id}\n";
    exit(1);
}

$orderId = (int) $argv[1];
$order = App\Models\Order::find($orderId);
if (! $order) {
    echo "Order not found: $orderId\n";
    exit(1);
}

echo "Order ID: {$order->id}\n";
echo "Created at (stored): {$order->getRawOriginal('created_at')}\n"; // raw DB value
echo "Created at (Carbon): {$order->created_at->toDateTimeString()}\n";
echo "Created at (ISO): {$order->created_at->toIso8601String()}\n";
echo "App timezone: " . config('app.timezone') . "\n";

// Also show now() and utc now
echo "Now: " . now()->toDateTimeString() . "\n";
echo "UTC now: " . now()->setTimezone('UTC')->toDateTimeString() . "\n";

echo "Order JSON:\n";
echo json_encode($order->toArray(), JSON_PRETTY_PRINT) . "\n";
