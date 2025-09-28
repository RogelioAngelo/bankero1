<?php
// Simple script to check transactions for checkout_id found in storage/app/paymongo_unmatched
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dir = __DIR__ . '/../storage/app/paymongo_unmatched';
$files = glob($dir . '/unmatched_*.json');
if (empty($files)) {
    echo "No unmatched files found.\n";
    exit(0);
}
$latest = array_pop($files);
echo "Inspecting: $latest\n";
$payload = json_decode(file_get_contents($latest), true);
$checkoutId = null;
if (isset($payload['data']['attributes']['data']['id'])) {
    $checkoutId = $payload['data']['attributes']['data']['id'];
} elseif (isset($payload['data']['id'])) {
    $checkoutId = $payload['data']['id'];
}
if (!$checkoutId) {
    // search recursively
    function searchForCs($data) {
        if (is_string($data) && preg_match('/\bcs_[A-Za-z0-9_\-]+\b/', $data, $m)) return $m[0];
        if (is_array($data)) { foreach ($data as $v) { $f = searchForCs($v); if ($f) return $f; }}
        if (is_object($data)) { foreach (get_object_vars($data) as $v) { $f = searchForCs($v); if ($f) return $f; }}
        return null;
    }
    $checkoutId = searchForCs($payload);
}
if (!$checkoutId) {
    echo "No checkout id found in payload\n";
    exit(0);
}

echo "checkout id: $checkoutId\n";

$t = App\Models\Transaction::where('checkout_id', $checkoutId)->first();
if ($t) {
    echo "Transaction FOUND:\n";
    echo "id={$t->id} user_id={$t->user_id} order_id={$t->order_id} status={$t->status} checkout_id={$t->checkout_id}\n";
    echo "meta: \n" . print_r($t->meta, true) . "\n";
} else {
    echo "Transaction NOT FOUND for checkout_id $checkoutId\n";
}
