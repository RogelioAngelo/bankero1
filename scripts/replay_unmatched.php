<?php
// Simple replayer: POST the latest unmatched payload to local webhook endpoint
$dir = __DIR__ . '/../storage/app/paymongo_unmatched';
$files = glob($dir . '/unmatched_*.json');
if (empty($files)) { echo "No unmatched payloads\n"; exit(0); }
$latest = array_pop($files);
$payload = file_get_contents($latest);

$endpoint = $argv[1] ?? 'http://127.0.0.1:8001/paymongo/webhook';
$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if (curl_errno($ch)) echo 'Curl error: ' . curl_error($ch) . "\n";
curl_close($ch);

echo "POSTed $latest -> HTTP $http\n";
echo "Response: $res\n";
