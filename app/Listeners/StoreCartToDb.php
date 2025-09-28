<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

class StoreCartToDb
{
    public function handle(Logout $event)
    {
        $user = $event->user;
        $sessionId = session()->getId();

        $cartInstance = Cart::instance('cart')->content();
        if ($cartInstance->count() === 0) {
            return;
        }

        $dbCart = DbCart::firstOrCreate([
            'user_id' => $user ? $user->id : null,
            'session_id' => $sessionId,
        ]);

        foreach ($cartInstance as $row) {
            $dbItem = DbCartItem::updateOrCreate([
                'cart_id' => $dbCart->id,
                'row_id' => $row->rowId,
            ],[
                'product_id' => $row->id,
                'quantity' => $row->qty,
                'price' => $row->price,
                'options' => $row->options ?? null,
            ]);
        }
    }
}
