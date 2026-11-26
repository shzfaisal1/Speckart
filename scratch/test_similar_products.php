<?php
chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Website\ProductController;
use Illuminate\Http\Request;

$testId = 3591;
echo "\nTesting getSimilarProducts for ID: $testId ...\n";
$controller = app(ProductController::class);
$req = Request::create("/product/{$testId}/similar", 'GET');
$req->headers->set('X-Requested-With', 'XMLHttpRequest');

try {
    $res = $controller->getSimilarProducts($req, $testId);
    echo "Status Code: " . $res->getStatusCode() . "\n";
    $data = json_decode($res->getContent(), true);
    echo "Response Status: " . ($data['status'] ?? 'null') . "\n";
    echo "Product Name: " . ($data['product_name'] ?? 'null') . "\n";
    echo "Count: " . ($data['count'] ?? 0) . "\n";
    echo "HTML snippet: " . substr($data['html'] ?? '', 0, 200) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
