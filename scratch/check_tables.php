<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

// Find payment table
$tables = DB::select("SHOW TABLES LIKE '%payment%'");
foreach ($tables as $t) {
    $vals = (array)$t;
    echo 'PAY TABLE: ' . implode('', $vals) . PHP_EOL;
}
// Find all sales tables
$st = DB::select("SHOW TABLES LIKE '%sales%'");
foreach ($st as $t) {
    $vals = (array)$t;
    echo 'SALES TABLE: ' . implode('', $vals) . PHP_EOL;
}
// Check SalePayment model
$model = new App\Models\sale\SalePayment();
echo 'SalePayment table: ' . $model->getTable() . PHP_EOL;
