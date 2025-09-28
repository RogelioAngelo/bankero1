<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

class RestoreCartFromDb
{
    public function handle(Login $event)
    {
        $user = $event->user;

        // Find cart by user_id or session_id
        $sessionId = session()->getId();
        $dbCart = DbCart::where('user_id', $user->id)->orWhere('session_id', $sessionId)->first();

        if (!$dbCart) {
            return;
        }

        foreach ($dbCart->items as $dbItem) {
            // try to find existing session item by stored row_id
            $existing = Cart::instance('cart')->content()->where('rowId', $dbItem->row_id)->first();
            if ($existing) {
                // if quantities differ, update the session cart to the DB quantity
                if ($existing->qty != $dbItem->quantity) {
                    Cart::instance('cart')->update($existing->rowId, $dbItem->quantity);
                }
            } else {
                // add to session cart
                $row = Cart::instance('cart')->add($dbItem->product_id, optional($dbItem->product)->name ?? 'Item', $dbItem->quantity, $dbItem->price)->associate('App\\Models\\Product');
                // ensure mapping
                $dbItem->row_id = $row->rowId;
                $dbItem->save();
            }
        }
    }
}
