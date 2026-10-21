<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MasterController;
use App\Services\CartService;
use App\Models\User;

echo "=== STARTING BRAND LOYALTY TESTS ===\n\n";

function assertTest($description, $condition) {
    if ($condition) {
        echo " [PASS] $description\n";
    } else {
        echo " [FAIL] $description\n";
        exit(1);
    }
}

// 1. Check Schema
assertTest("tbl_brand has loyalty_eligible column", Schema::hasColumn('tbl_brand', 'loyalty_eligible'));
assertTest("tbl_brand has loyalty_percent column", Schema::hasColumn('tbl_brand', 'loyalty_percent'));

// Set up mock admin user for controller calls
$adminUser = User::first() ?? new User(['id' => 1, 'name' => 'Admin']);
auth()->login($adminUser);

$controller = new MasterController();

// 2. Test brand creation with loyalty settings
$brandNameEligible = 'TEST_BRAND_ELIGIBLE_' . time();
$brandNameIneligible = 'TEST_BRAND_INELIGIBLE_' . time();
$brandNameCustomRate = 'TEST_BRAND_CUSTOM_' . time();

// Create Brand 1: Eligible (default)
$req1 = new Request([
    'brand_name'       => $brandNameEligible,
    'loyalty_eligible' => '1',
    'loyalty_percent'  => '',
]);
$res1 = $controller->brandstored($req1);
$brand1 = DB::table('tbl_brand')->where('brand_name', $brandNameEligible)->first();
assertTest("Brand 1 created with loyalty_eligible = 1", $brand1 && (int)$brand1->loyalty_eligible === 1);
assertTest("Brand 1 has null loyalty_percent", $brand1 && $brand1->loyalty_percent === null);

// Create Brand 2: Ineligible
$req2 = new Request([
    'brand_name'       => $brandNameIneligible,
    'loyalty_eligible' => '0',
    'loyalty_percent'  => '',
]);
$res2 = $controller->brandstored($req2);
$brand2 = DB::table('tbl_brand')->where('brand_name', $brandNameIneligible)->first();
assertTest("Brand 2 created with loyalty_eligible = 0", $brand2 && (int)$brand2->loyalty_eligible === 0);

// Create Brand 3: Custom rate 15%
$req3 = new Request([
    'brand_name'       => $brandNameCustomRate,
    'loyalty_eligible' => '1',
    'loyalty_percent'  => '15',
]);
$res3 = $controller->brandstored($req3);
$brand3 = DB::table('tbl_brand')->where('brand_name', $brandNameCustomRate)->first();
assertTest("Brand 3 created with custom rate 15%", $brand3 && (float)$brand3->loyalty_percent === 15.0);

// 3. Test Brand Datatable output
$dtReq = new Request([
    'draw'   => 1,
    'start'  => 0,
    'length' => 10,
    'search1'=> $brandNameIneligible,
]);
$dtRes = $controller->brandDatatable($dtReq)->getData(true);
assertTest("Datatable returns records", !empty($dtRes['data']));
$foundRow = $dtRes['data'][0] ?? [];
assertTest("Datatable output contains loyalty_badge", !empty($foundRow['loyalty_badge']) && str_contains($foundRow['loyalty_badge'], 'Ineligible'));
assertTest("Datatable output contains loyalty_eligible = 0", isset($foundRow['loyalty_eligible']) && $foundRow['loyalty_eligible'] === 0);

// 4. Test Update brand
$updateReq = new Request([
    'uid'              => $brand2->brand_id,
    'brand_name'       => $brandNameIneligible,
    'loyalty_eligible' => '1',
    'loyalty_percent'  => '8.5',
]);
$controller->brandstored($updateReq);
$brand2Updated = DB::table('tbl_brand')->where('brand_id', $brand2->brand_id)->first();
assertTest("Brand 2 updated to loyalty_eligible = 1", (int)$brand2Updated->loyalty_eligible === 1);
assertTest("Brand 2 updated with loyalty_percent = 8.5", (float)$brand2Updated->loyalty_percent === 8.5);

// Set back Brand 2 to Ineligible for Cart test
DB::table('tbl_brand')->where('brand_id', $brand2->brand_id)->update(['loyalty_eligible' => 0, 'loyalty_percent' => null]);

// 5. Test CartService Earning Calculation
$cartService = app(CartService::class);

// Find an existing product in tbl_product_code to build mock cart
$frame1 = DB::table('tbl_product_code')->first();
if ($frame1) {
    // Clear cart session
    session()->forget(['cart', 'applied_coupon', 'applied_voucher', 'use_loyalty_points']);

    // Mock cart with 2 items:
    // Item 1: Brand 1 (Eligible, 5% rate), Frame Price = ₹1000
    // Item 2: Brand 2 (Ineligible, 0 pts), Frame Price = ₹1000
    $mockCart = [
        'item_1' => [
            'key' => 'item_1',
            'frame_id' => $frame1->id,
            'frame_code' => $frame1->product_code,
            'frame_name' => 'Frame 1',
            'brand' => $brandNameEligible,
            'frame_price' => 1000.0,
            'lens_name' => 'Single Vision',
            'lens_price' => 0.0,
            'quantity' => 1,
            'product_type' => 'frame',
        ],
        'item_2' => [
            'key' => 'item_2',
            'frame_id' => $frame1->id,
            'frame_code' => $frame1->product_code,
            'frame_name' => 'Frame 2',
            'brand' => $brandNameIneligible,
            'frame_price' => 1000.0,
            'lens_name' => 'Single Vision',
            'lens_price' => 0.0,
            'quantity' => 1,
            'product_type' => 'frame',
        ],
    ];
    // --- Test A: Single item in cart (no BOGO, full price paid) ---
    // Item 1 only (Brand 1 Eligible, 5% of ₹1000 = 50 pts)
    session()->put('cart', ['item_1' => $mockCart['item_1']]);
    $cartSingleEligible = $cartService->getCartCalculations();
    echo "  Single Item Eligible (5% on ₹1000): " . $cartSingleEligible['order_reward_pts'] . " pts\n";
    assertTest("Single eligible brand earns standard 5% points (50 pts)", (int)$cartSingleEligible['order_reward_pts'] === 50);

    // Item 2 only (Brand 2 Ineligible, 0 pts on ₹1000)
    session()->put('cart', ['item_2' => $mockCart['item_2']]);
    $cartSingleIneligible = $cartService->getCartCalculations();
    echo "  Single Item Ineligible (0 pts on ₹1000): " . $cartSingleIneligible['order_reward_pts'] . " pts\n";
    assertTest("Single ineligible brand earns 0 loyalty points", (int)$cartSingleIneligible['order_reward_pts'] === 0);

    // Item 3 only (Brand 3 Custom 15% rate, 15% on ₹1000 = 150 pts)
    $mockCartItem3 = $mockCart['item_1'];
    $mockCartItem3['brand'] = $brandNameCustomRate;
    session()->put('cart', ['item_3' => $mockCartItem3]);
    $cartSingleCustom = $cartService->getCartCalculations();
    echo "  Single Item Custom (15% on ₹1000): " . $cartSingleCustom['order_reward_pts'] . " pts\n";
    assertTest("Single brand with custom 15% rate earns 150 points", (int)$cartSingleCustom['order_reward_pts'] === 150);

    // --- Test B: 2 items in cart with BOGO (Total Payable = ₹1000) ---
    // Item 1 (Eligible 5%) + Item 2 (Ineligible 0 pts):
    // Share is 50% eligible, so 5% on ₹500 = 25 pts.
    session()->put('cart', $mockCart);
    $cartData = $cartService->getCartCalculations();
    echo "  BOGO Cart with 1 Eligible + 1 Ineligible (5% on ₹500): " . $cartData['order_reward_pts'] . " pts\n";
    assertTest("Ineligible brand items are excluded from BOGO cart loyalty points (got 25 pts)", (int)$cartData['order_reward_pts'] === 25);

    // Item 1 (Eligible 5%) + Item 3 (Custom 15% rate):
    // Both eligible: 5% on ₹500 (25 pts) + 15% on ₹500 (75 pts) = 100 pts.
    $mockCart['item_2']['brand'] = $brandNameCustomRate;
    session()->put('cart', $mockCart);
    $cartDataCustom = $cartService->getCartCalculations();
    echo "  BOGO Cart with 5% rate + 15% rate (25 + 75 = 100 pts): " . $cartDataCustom['order_reward_pts'] . " pts\n";
    assertTest("Custom brand rate in BOGO cart is correctly applied (100 pts)", (int)$cartDataCustom['order_reward_pts'] === 100);
}

// 6. Cleanup test brands
DB::table('tbl_brand')->whereIn('brand_name', [$brandNameEligible, $brandNameIneligible, $brandNameCustomRate])->delete();
session()->forget(['cart', 'applied_coupon', 'applied_voucher', 'use_loyalty_points']);
echo " [PASS] Cleaned up temporary test brands\n";

echo "\n ALL TESTS PASSED SUCCESSFULLY! \n";
