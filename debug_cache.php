<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = Illuminate\Support\Facades\Cache::get('dashboard_recent_orders');
echo gettype($orders) . PHP_EOL;
if (is_object($orders)) {
    echo get_class($orders) . PHP_EOL;
} elseif (is_iterable($orders)) {
    foreach ($orders as $order) {
        echo gettype($order) . PHP_EOL;
        if (is_object($order)) {
            echo get_class($order) . PHP_EOL;
        } else {
            echo var_export($order, true) . PHP_EOL;
        }
    }
} else {
    echo var_export($orders, true) . PHP_EOL;
}
