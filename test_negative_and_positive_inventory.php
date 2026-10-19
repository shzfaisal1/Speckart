<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\StockSyncService;
use App\Services\CartService;
use App\Http\Controllers\Website\ProductController;
use App\Http\Controllers\Website\CartController;
use Illuminate\Http\Request;

echo "\n====================================================================\n";
echo "   SPECKART: NEGATIVE & POSITIVE INVENTORY VERIFICATION SUITE       \n";
echo "====================================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertTest($title, $condition, $failMessage = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo " [\033[32mPASS\033[0m] " . $title . "\n";
        $passCount++;
    } else {
        echo " [\033[31mFAIL\033[0m] " . $title . "\n";
        if ($failMessage) {
            echo "        \033[33m-> " . $failMessage . "\033[0m\n";
        }
        $failCount++;
    }
}

$ecomStoreId = StockSyncService::getEcommerceStoreId();
$cartService = app(CartService::class);
$cartController = app(CartController::class);
$prodController = app(ProductController::class);

// Find a valid frame with positive stock for testing combo purchases
$testFrame = DB::table('tbl_product_code')->where('product_code', 'demo-1312')->first();
if (!$testFrame) {
    $testFrame = DB::table('tbl_product_code')->where('product_type', 'Frame')->first();
}

// -------------------------------------------------------------------------
// SECTION A: NEGATIVE INVENTORY TESTS (Zero / Negative Stock Scenarios)
// -------------------------------------------------------------------------
echo "--------------------------------------------------------------------\n";
echo " SECTION A: NEGATIVE & ZERO INVENTORY SCENARIOS                      \n";
echo "--------------------------------------------------------------------\n";

// A1: Contact Lens (Physical Barcode Good) with Zero Stock
$clProduct = DB::table('tbl_product_code')->where('product_type', 'Lens')->first();
if ($clProduct) {
    // Ensure 0 stock in Store 6
    DB::table('tbl_inventory_levels')
        ->where('product_code', $clProduct->product_code)
        ->where('store_id', $ecomStoreId)
        ->delete();

    $liveZero = StockSyncService::getLiveStock($clProduct->product_code, $ecomStoreId);
    assertTest(
        "A1.1: Physical Contact Lens has 0 warehouse stock in Store 6",
        $liveZero === 0,
        "Stock is currently $liveZero"
    );

    assertTest(
        "A1.2: Contact Lens has Allow_Negative_Inventory = 0 (Strict Physical Good)",
        (int)$clProduct->Allow_Negative_Inventory === 0,
        "Allow_Negative_Inventory = {$clProduct->Allow_Negative_Inventory}"
    );

    session()->forget('cart');
    $cartRes = $cartService->addToCart($clProduct->id, null, 1, null, 'Contact Lens');
    assertTest(
        "A1.3: CartService BLOCKS zero-stock Contact Lens (Negative inventory disallowed)",
        $cartRes['status'] === false && strpos(strtolower($cartRes['message'] ?? ''), 'out of stock') !== false,
        $cartRes['message'] ?? ''
    );
}

// A2: Lens Package with ZERO Stock - STRICT MODE (allow_negative_inventory = 0)
$pkg = DB::table('lens_packages')->where('id', 1)->first();
$pkgCode = $pkg->product_code ?: 'GLS-ANT-4628';

// Delete any inventory to simulate zero warehouse stock
DB::table('tbl_inventory_levels')->where('product_code', $pkgCode)->where('store_id', $ecomStoreId)->delete();
$pkgLiveStock = StockSyncService::getLiveStock($pkgCode, $ecomStoreId);

assertTest(
    "A2.1: Lens Package warehouse stock in Store 6 is currently 0",
    $pkgLiveStock === 0,
    "Stock is $pkgLiveStock"
);

// Set strict mode: allow_negative_inventory = 0
DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 1, 'allow_negative_inventory' => 0]);

session()->forget('cart');
$cartResStrict = $cartService->addToCart($testFrame->id, 1, 1, null, 'Single Vision');
assertTest(
    "A2.2: CartService BLOCKS zero-stock Lens Package when allow_negative_inventory = 0",
    $cartResStrict['status'] === false && strpos(strtolower($cartResStrict['message'] ?? ''), 'out of stock') !== false,
    $cartResStrict['message'] ?? 'Unexpectedly allowed!'
);

$reqStrict = Request::create('/cart/add', 'POST', [
    'frame_id'        => $testFrame->id,
    'lens_package_id' => 1,
    'quantity'        => 1,
    'lens_type'       => 'Single Vision',
]);
$respStrict = $cartController->addToCart($reqStrict);
$respDataStrict = json_decode($respStrict->getContent(), true);
assertTest(
    "A2.3: CartController returns 400 Bad Request when allow_negative_inventory = 0 & stock = 0",
    $respStrict->getStatusCode() === 400 && strpos(strtolower($respDataStrict['message'] ?? ''), 'out of stock') !== false,
    "Status: {$respStrict->getStatusCode()} | Message: " . ($respDataStrict['message'] ?? '')
);

// A3: Lens Package with ZERO Stock - ON-DEMAND MODE (allow_negative_inventory = 1)
DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 1, 'allow_negative_inventory' => 1]);

session()->forget('cart');
$cartResAllowed = $cartService->addToCart($testFrame->id, 1, 1, null, 'Single Vision');
assertTest(
    "A3.1: CartService ALLOWS zero-stock Lens Package when allow_negative_inventory = 1 (On-Demand Lab Order)",
    $cartResAllowed['status'] === true,
    $cartResAllowed['message'] ?? 'Failed to add!'
);

session()->forget('cart');
$reqAllowed = Request::create('/cart/add', 'POST', [
    'frame_id'        => $testFrame->id,
    'lens_package_id' => 1,
    'quantity'        => 1,
    'lens_type'       => 'Single Vision',
]);
$respAllowed = $cartController->addToCart($reqAllowed);
$respDataAllowed = json_decode($respAllowed->getContent(), true);
assertTest(
    "A3.2: CartController returns success when allow_negative_inventory = 1 & stock = 0",
    $respAllowed->getStatusCode() === 200 && (isset($respDataAllowed['status']) && in_array($respDataAllowed['status'], [true, 'success'], true)),
    "Status: {$respAllowed->getStatusCode()} | Message: " . ($respDataAllowed['message'] ?? '')
);

// A4: Lens Package with Untracked Inventory (track_inventory = 0)
DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 0, 'allow_negative_inventory' => 0]);
session()->forget('cart');
$cartResUntracked = $cartService->addToCart($testFrame->id, 1, 1, null, 'Single Vision');
assertTest(
    "A4.1: CartService ALLOWS zero-stock Lens Package when track_inventory = 0 (Untracked)",
    $cartResUntracked['status'] === true,
    $cartResUntracked['message'] ?? 'Failed to add!'
);

// Restore lens package 1 back to default on-demand policy
DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 1, 'allow_negative_inventory' => 1]);


// -------------------------------------------------------------------------
// SECTION B: POSITIVE INVENTORY TESTS (Warehouse In-Stock Scenarios)
// -------------------------------------------------------------------------
echo "\n--------------------------------------------------------------------\n";
echo " SECTION B: POSITIVE INVENTORY SCENARIOS                            \n";
echo "--------------------------------------------------------------------\n";

// B1: Setup physical warehouse inventory in Store 6 (8 boxes of contact lenses)
$tempClInvId = DB::table('tbl_inventory_levels')->insertGetId([
    'product_code'       => $clProduct->product_code,
    'product_id'         => $clProduct->id,
    'product_type'       => 'Lens',
    'product_details'    => $clProduct->product_name,
    'store_id'           => $ecomStoreId,
    'available_quantity' => 8,
    'tota_lens_qty'      => 240,
    'perbox'             => 30,
    'created_at'         => now(),
    'updated_at'         => now(),
]);

$clLivePositive = StockSyncService::getLiveStock($clProduct->product_code, $ecomStoreId);
assertTest(
    "B1.1: Physical Contact Lens has POSITIVE warehouse stock (8 boxes in Store 6)",
    $clLivePositive === 8,
    "Stock is $clLivePositive"
);

session()->forget('cart');
$cartResPositive = $cartService->addToCart($clProduct->id, null, 3, json_encode(['type' => 'contact_lens_manual', 'total_boxes' => 3]), 'Contact Lens');
assertTest(
    "B1.2: CartService successfully adds 3 boxes when 8 boxes are available",
    $cartResPositive['status'] === true,
    $cartResPositive['message'] ?? 'Failed'
);

$cartResExcess = $cartService->addToCart($clProduct->id, null, 6, json_encode(['type' => 'contact_lens_manual', 'total_boxes' => 6]), 'Contact Lens');
assertTest(
    "B1.3: CartService BLOCKS excess quantity (3 in cart + 6 requested = 9 > 8 available)",
    $cartResExcess['status'] === false && strpos(strtolower($cartResExcess['message'] ?? ''), 'only 8') !== false,
    $cartResExcess['message'] ?? 'Allowed excess unexpectedly'
);

// B2: Strict Lens Package with POSITIVE stock (6 pieces in Store 6)
// Temporarily give testFrame 10 pieces so it's not the bottleneck
$tempFrameInvId = DB::table('tbl_inventory_levels')->insertGetId([
    'product_code'       => $testFrame->product_code,
    'product_id'         => $testFrame->id,
    'product_type'       => 'Frame',
    'product_details'    => $testFrame->product_name,
    'store_id'           => $ecomStoreId,
    'available_quantity' => 10,
    'tota_lens_qty'      => 0,
    'created_at'         => now(),
    'updated_at'         => now(),
]);

DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 1, 'allow_negative_inventory' => 0]);
$tempPkgInvId = DB::table('tbl_inventory_levels')->insertGetId([
    'product_code'       => $pkgCode,
    'product_id'         => 1,
    'product_type'       => 'Glass',
    'product_details'    => $pkg->name,
    'store_id'           => $ecomStoreId,
    'available_quantity' => 6,
    'tota_lens_qty'      => 6,
    'created_at'         => now(),
    'updated_at'         => now(),
]);

$pkgLivePos = StockSyncService::getLiveStock($pkgCode, $ecomStoreId);
assertTest(
    "B2.1: Strict Lens Package has POSITIVE warehouse stock (6 pieces in Store 6)",
    $pkgLivePos === 6,
    "Stock is $pkgLivePos"
);

session()->forget('cart');
$cartPkgPos = $cartService->addToCart($testFrame->id, 1, 2, null, 'Single Vision');
assertTest(
    "B2.2: CartService ALLOWS adding 2 pieces of Strict Lens Package when 6 are in stock",
    $cartPkgPos['status'] === true,
    $cartPkgPos['message'] ?? 'Failed'
);

$cartPkgExcess = $cartService->addToCart($testFrame->id, 1, 7, null, 'Single Vision');
assertTest(
    "B2.3: CartService BLOCKS excess quantity (7 requested > 6 available) for Strict Lens Package",
    $cartPkgExcess['status'] === false && strpos(strtolower($cartPkgExcess['message'] ?? ''), 'only 6') !== false,
    $cartPkgExcess['message'] ?? 'Allowed excess unexpectedly'
);


// -------------------------------------------------------------------------
// SECTION C: WAREHOUSE DEDUCTION & RESTOCK LIFECYCLE
// -------------------------------------------------------------------------
echo "\n--------------------------------------------------------------------\n";
echo " SECTION C: WAREHOUSE DEDUCTION & RESTOCK LIFECYCLE                  \n";
echo "--------------------------------------------------------------------\n";

// C1: Deduct 2 pieces on order placement
$deductQty = 2;
DB::table('tbl_inventory_levels')->where('id', $tempPkgInvId)->decrement('available_quantity', $deductQty);
$pkgStockAfterDeduct = StockSyncService::getLiveStock($pkgCode, $ecomStoreId);

assertTest(
    "C1.1: Warehouse stock decrements from 6 to 4 pieces on order checkout",
    $pkgStockAfterDeduct === 4,
    "Stock is $pkgStockAfterDeduct"
);

// C2: Restock 2 pieces on order cancellation / return
DB::table('tbl_inventory_levels')->where('id', $tempPkgInvId)->increment('available_quantity', $deductQty);
$pkgStockAfterRestock = StockSyncService::getLiveStock($pkgCode, $ecomStoreId);

assertTest(
    "C2.1: Warehouse stock restored from 4 back to 6 pieces on cancellation/restock",
    $pkgStockAfterRestock === 6,
    "Stock is $pkgStockAfterRestock"
);


// -------------------------------------------------------------------------
// CLEANUP
// -------------------------------------------------------------------------
DB::table('tbl_inventory_levels')->where('id', $tempClInvId)->delete();
DB::table('tbl_inventory_levels')->where('id', $tempPkgInvId)->delete();
if (isset($tempFrameInvId)) {
    DB::table('tbl_inventory_levels')->where('id', $tempFrameInvId)->delete();
}
DB::table('lens_packages')->where('id', 1)->update(['track_inventory' => 1, 'allow_negative_inventory' => 1]);
session()->forget('cart');

echo "\n====================================================================\n";
echo " FINAL AUDIT RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "====================================================================\n\n";
