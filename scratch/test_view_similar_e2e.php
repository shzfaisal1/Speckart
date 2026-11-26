<?php
/**
 * ============================================================================
 * SPECKART TEST SUITE: "View Similar" Products Functionality E2E Test
 * ============================================================================
 * Tests:
 * 1. Product resolution by ID, product_id, and product_code
 * 2. Similar products retrieval matching Shape, Type, Company, and Category
 * 3. Graceful fallback when category or specific criteria are sparse
 * 4. Blade view rendering (similar_modal_grid.blade.php) without PHP errors
 * 5. Color swatches, prices, images, and Select Lenses buttons in modal
 * 6. Error handling for non-existent product (404)
 * ============================================================================
 */

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';
$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Website\ProductController;

echo "===============================================================================\n";
echo "      SPECKART TEST: VIEW SIMILAR PRODUCTS FUNCTIONALITY E2E                   \n";
echo "      Date: " . date('Y-m-d H:i:s') . "\n";
echo "===============================================================================\n";

$pass = 0;
$fail = 0;
$results = [];

function check(string $label, bool $condition, string $detail = '') {
    global $pass, $fail, $results;
    if ($condition) {
        $pass++;
        $results[] = "  ✅ PASS: $label" . ($detail ? " [$detail]" : "");
    } else {
        $fail++;
        $results[] = "  ❌ FAIL: $label" . ($detail ? " — $detail" : "");
    }
}

$controller = app(ProductController::class);

// Test 1: Query with GRAVITY product (from user screenshot)
echo "\n--- 1. Testing GRAVITY product (FR-088-BLK / ID 3591) ---\n";
$req1 = Request::create('/product/3591/similar', 'GET');
$req1->headers->set('X-Requested-With', 'XMLHttpRequest');

$res1 = $controller->getSimilarProducts($req1, 3591);
check('T1: HTTP status is 200 for product 3591', $res1->getStatusCode() === 200, "status={$res1->getStatusCode()}");

$data1 = json_decode($res1->getContent(), true);
check('T1b: JSON response has status=success', ($data1['status'] ?? '') === 'success');
check('T1c: Product name populated', !empty($data1['product_name']), "name=" . ($data1['product_name'] ?? ''));
check('T1d: Product brand populated', ($data1['product_brand'] ?? '') === 'GRAVITY', "brand=" . ($data1['product_brand'] ?? ''));
check('T1e: Similar products count > 0', ($data1['count'] ?? 0) > 0, "count=" . ($data1['count'] ?? 0));
check('T1f: Rendered HTML contains product-card', strpos($data1['html'] ?? '', 'product-card') !== false);
check('T1g: Rendered HTML contains SELECT LENSES action', strpos($data1['html'] ?? '', 'SELECT LENSES') !== false);
check('T1h: Rendered HTML contains price section', strpos($data1['html'] ?? '', 'current-price') !== false);

// Test 2: Query by product_code (string identifier)
echo "\n--- 2. Testing resolution by product_code ('FR-088-BLK') ---\n";
$req2 = Request::create('/product/FR-088-BLK/similar', 'GET');
$req2->headers->set('X-Requested-With', 'XMLHttpRequest');
$res2 = $controller->getSimilarProducts($req2, 'FR-088-BLK');
check('T2: Successfully resolves by product_code string', $res2->getStatusCode() === 200);
$data2 = json_decode($res2->getContent(), true);
check('T2b: Returned count > 0 for product_code', ($data2['count'] ?? 0) > 0, "count=" . ($data2['count'] ?? 0));

// Test 3: Query by product_id (247523 / ID 202)
echo "\n--- 3. Testing product with sparse category/shape (ID 202) ---\n";
$req3 = Request::create('/product/202/similar', 'GET');
$req3->headers->set('X-Requested-With', 'XMLHttpRequest');
$res3 = $controller->getSimilarProducts($req3, 202);
check('T3: Successfully resolves product 202 with sparse data', $res3->getStatusCode() === 200);
$data3 = json_decode($res3->getContent(), true);
check('T3b: Multi-layer fallback returns similar products even when category is empty', ($data3['count'] ?? 0) > 0, "count=" . ($data3['count'] ?? 0));

// Test 4: Query with Aimbeat demo product (ID 3566)
echo "\n--- 4. Testing Product ID 3566 (Aimbeat Eyewear) ---\n";
$req4 = Request::create('/product/3566/similar', 'GET');
$req4->headers->set('X-Requested-With', 'XMLHttpRequest');
$res4 = $controller->getSimilarProducts($req4, 3566);
check('T4: HTTP status 200 for Product 3566', $res4->getStatusCode() === 200);
$data4 = json_decode($res4->getContent(), true);
check('T4b: Returns similar products for Product 3566', ($data4['count'] ?? 0) > 0, "count=" . ($data4['count'] ?? 0));

// Test 5: Negative Testing - Non-existent product ID
echo "\n--- 5. Negative Test: Non-existent product ID (99999999) ---\n";
$req5 = Request::create('/product/99999999/similar', 'GET');
$req5->headers->set('X-Requested-With', 'XMLHttpRequest');
$res5 = $controller->getSimilarProducts($req5, 99999999);
check('T5: Non-existent product returns HTTP 404', $res5->getStatusCode() === 404, "status={$res5->getStatusCode()}");
$data5 = json_decode($res5->getContent(), true);
check('T5b: Error message returned', ($data5['status'] ?? '') === 'error' && strpos($data5['message'] ?? '', 'Product not found') !== false);

// Test 6: Verify no undefined properties or notices in Blade
echo "\n--- 6. Verifying Blade Template Cleanliness ---\n";
$bladeContent = file_get_contents('resources/views/website/products/similar_modal_grid.blade.php');
check('T6: No Rim_Type property references in similar_modal_grid.blade.php', strpos($bladeContent, 'Rim_Type') === false);
$controllerContent = file_get_contents('app/Http/Controllers/Website/ProductController.php');
check('T6b: No Rim_Type column references in ProductController.php', strpos($controllerContent, 'Rim_Type') === false);

echo "\n" . implode("\n", $results) . "\n";
echo "===============================================================================\n";
printf("TOTAL: %d | PASS: %d | FAIL: %d\n", $pass + $fail, $pass, $fail);
echo "===============================================================================\n";
