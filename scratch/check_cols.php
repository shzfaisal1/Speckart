<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$hasOverrides = Schema::hasTable('product_type_overrides');
echo "product_type_overrides table exists: " . ($hasOverrides ? "YES" : "NO") . "\n";
if ($hasOverrides) {
    echo "Columns: " . implode(', ', Schema::getColumnListing('product_type_overrides')) . "\n";
}
