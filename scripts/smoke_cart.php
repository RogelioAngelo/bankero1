<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

// clear session cart
Cart::instance('cart')->destroy();

// add item (simulate request)
$row = Cart::instance('cart')->add(22, 'try', 1, '900.00')->associate('App\\Models\\Product');
$sessionId = session()->getId();

// ensure DB cart
$dbCart = DbCart::firstOrCreate(['session_id' => $sessionId]);
DbCartItem::updateOrCreate(['cart_id' => $dbCart->id, 'row_id' => $row->rowId], [
    'product_id' => 22,
    'quantity' => 1,
    'price' => '900.00',
]);

echo "Added rowId: {$row->rowId}\n";
print_r(DB::table('cart_items')->where('row_id', $row->rowId)->first());

// update qty to 2
Cart::instance('cart')->update($row->rowId, 2);
// call sync function similar to controller
$dbCart = DbCart::where('session_id', $sessionId)->first();
DbCartItem::updateOrCreate(['cart_id' => $dbCart->id, 'row_id' => $row->rowId], [
    'product_id' => 22,
    'quantity' => 2,
    'price' => '900.00',
]);

echo "After update:\n";
print_r(DB::table('cart_items')->where('row_id', $row->rowId)->first());
