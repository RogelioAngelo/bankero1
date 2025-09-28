<?php
$file = 'app/Http/Controllers/CartController.php';
$lines = file($file);
foreach ($lines as $i => $line) {
    $num = $i + 1;
    printf("%4d: %s", $num, $line);
}
