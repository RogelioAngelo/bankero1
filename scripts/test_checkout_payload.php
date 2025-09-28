<?php

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::create('/place-an-order', 'POST', ['mode' => 'e-wallet']);

// Set up a session and auth - this is minimal and may fail in complex apps.
// We'll just run the kernel to let the app handle the request through routing.
$response = $kernel->handle($request);

echo $response->getStatusCode() . "\n";
echo $response->getContent() . "\n";

$kernel->terminate($request, $response);
