<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart as DbCart;
use App\Models\CartItem as DbCartItem;

class PaymongoWebhookControllerClean extends Controller
{
    public function handlePaymongoWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayMongo Webhook received: ' . json_encode($payload));
        return response()->json(['ok' => true], 200);
    }
}
