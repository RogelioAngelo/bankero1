<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\PaymongoWebhookController();

$payload = [
    'data' => [
        'attributes' => [
            'type' => 'checkout_session.payment.paid',
            'data' => [
                'id' => 'cs_TEST123',
                'attributes' => [
                    'status' => 'paid',
                    'id' => 'pay_TESTPAY',
                    'amount' => 108900,
                    'payment_intent_id' => 'pi_test',
                    'source' => ['id' => 'src_test'],
                    'metadata' => ['transaction_id' => '1']
                ]
            ]
        ]
    ]
];

$request = new Illuminate\Http\Request();
$request->replace($payload);

try {
    $resp = $controller->handlePaymongoWebhook($request);
    if (method_exists($resp, 'getStatusCode')) {
        echo "Handler returned status: " . $resp->getStatusCode() . "\n";
        if (method_exists($resp, 'getContent')) echo $resp->getContent() . "\n";
    } else {
        echo "Handler returned: " . json_encode($resp) . "\n";
    }
} catch (Throwable $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
