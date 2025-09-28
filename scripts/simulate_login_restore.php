<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Listeners\RestoreCartFromDb;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;
use Surfsidemedia\Shoppingcart\Facades\Cart;

// assume scripts/smoke_cart.php has run and cart_items has a row with quantity 2
$sessionId = session()->getId();
// pick the most recent cart in DB for simulation
$dbCart = DbCart::latest('id')->first();
if (!$dbCart) {
    echo "No dbCart found for session\n";
    exit;
}
$dbItem = DbCartItem::where('cart_id', $dbCart->id)->first();
if (!$dbItem) {
    echo "No dbItem found\n";
    exit;
}

// clear session cart and add the same product with qty 1 to simulate mismatch
Cart::instance('cart')->destroy();
$row = Cart::instance('cart')->add($dbItem->product_id, 'try', 1, $dbItem->price)->associate('App\\Models\\Product');
// intentionally different row_id to simulate mismatch

// run listener
// Directly perform restore logic for the found dbCart
foreach ($dbCart->items as $dbItem) {
    $existing = Cart::instance('cart')->content()->where('rowId', $dbItem->row_id)->first();
    if ($existing) {
        if ($existing->qty != $dbItem->quantity) {
            Cart::instance('cart')->update($existing->rowId, $dbItem->quantity);
        }
    } else {
        $row = Cart::instance('cart')->add($dbItem->product_id, optional($dbItem->product)->name ?? 'Item', $dbItem->quantity, $dbItem->price)->associate('App\\Models\\Product');
        $dbItem->row_id = $row->rowId;
        $dbItem->save();
    }
}

// print session cart contents
print_r(Cart::instance('cart')->content()->toArray());

// show cart_items DB row
print_r(DB::table('cart_items')->where('id', $dbItem->id)->first());
