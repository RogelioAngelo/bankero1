<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// run in framework context
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

$userid = $argv[1] ?? 10;
$cart = DbCart::where('user_id', $userid)->first();
if (!$cart) {
    echo "No cart found for user {$userid}\n";
    exit;
}
$items = DbCartItem::where('cart_id', $cart->id)->get();
if ($items->isEmpty()) {
    echo "No cart items for user {$userid} (cart id {$cart->id})\n";
    exit;
}
echo "Cart id: {$cart->id}\n";
foreach ($items as $it) {
    echo "product_id={$it->product_id}, price={$it->price}, qty={$it->quantity}\n";
}
