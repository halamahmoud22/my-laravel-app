<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = App\Models\Order::with('user')->latest()->take(5)->get();
echo gettype($orders) . PHP_EOL;
echo get_class($orders) . PHP_EOL;

foreach ($orders as $order) {
    echo gettype($order) . PHP_EOL;
    if (is_object($order)) {
        echo get_class($order) . PHP_EOL;
    } else {
        echo var_export($order, true) . PHP_EOL;
    }
}
