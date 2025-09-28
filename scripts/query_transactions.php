<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$res = App\Models\Transaction::where('amount', 108900)
    ->orWhere('status', 'pending')
    ->orderBy('id', 'desc')
    ->limit(20)
    ->get();
echo json_encode($res->toArray(), JSON_PRETTY_PRINT);
