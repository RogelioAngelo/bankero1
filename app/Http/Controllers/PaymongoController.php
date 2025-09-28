<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymongoController extends Controller
{
    public function index()
    {
        // Equivalent to your index.php / create_payment.php page
        return view('paymongo.index');
    }

    public function createPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        $amount = intval($request->amount * 100); // convert PHP to centavos

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode(env('PAYMONGO_SECRET_KEY') . ':'),
            'Content-Type'  => 'application/json',
        ])->post('https://api.paymongo.com/v1/links', [
            'data' => [
                'attributes' => [
                    'amount'      => $amount,
                    'currency'    => 'PHP',
                    'description' => 'Payment for order',
                    'remarks'     => 'From Laravel site',
                ]
            ]
        ]);

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Error creating PayMongo link.']);
        }

        $data = $response->json();

        if (!empty($data['data']['attributes']['checkout_url'])) {
            return redirect()->away($data['data']['attributes']['checkout_url']);
        }

        return back()->withErrors(['msg' => 'Unable to create payment link.']);
    }
}
