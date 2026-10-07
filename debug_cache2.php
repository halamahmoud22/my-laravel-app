<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = Illuminate\Support\Facades\Cache::get('dashboard_recent_orders');

if ($orders instanceof __PHP_Incomplete_Class) {
    echo "It's an incomplete class!\n";
    print_r($orders);
} else {
    echo "It's not just incomplete, let's dump it:\n";
    var_dump($orders);
}
