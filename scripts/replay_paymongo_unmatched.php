<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dir = storage_path('app/paymongo_unmatched');
if (! is_dir($dir)) {
    echo "No unmatched payloads dir: {$dir}\n";
    exit(0);
}

$files = array_values(array_filter(scandir($dir), function($f) use ($dir) {
    return is_file($dir . '/' . $f) && preg_match('/\.json$/i', $f);
}));

if (empty($files)) {
    echo "No unmatched payload files to process.\n";
    exit(0);
}

$controller = new App\Http\Controllers\PaymongoWebhookController();

foreach ($files as $f) {
    $path = $dir . '/' . $f;
    echo "Processing $f... ";
    $json = file_get_contents($path);
    $payload = json_decode($json, true);
    if (! is_array($payload)) {
        echo "invalid json\n";
        continue;
    }

    // Create a fake Request with the payload
    $request = new Illuminate\Http\Request();
    $request->replace($payload);

    try {
        $response = $controller->handlePaymongoWebhook($request);
        // Expecting a JsonResponse or Response
        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        echo "status={$status}\n";

        // Move processed file to processed/ subdir
        $processedDir = $dir . '/processed';
        if (! is_dir($processedDir)) mkdir($processedDir, 0755, true);
        rename($path, $processedDir . '/' . $f);
    } catch (\Throwable $e) {
        echo "failed: " . $e->getMessage() . "\n";
    }
}

echo "Done.\n";
