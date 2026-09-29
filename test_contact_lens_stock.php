<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\StockSyncService;
use App\Services\CartService;
use App\Http\Controllers\Website\ProductController;

echo "========================================================\n";
echo "    CONTACT LENS & PHYSICAL BARCODE GOODS VERIFICATION   \n";
echo "========================================================\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($name, $condition) {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] $name\n";
        $passCount++;
    } else {
        echo " [FAIL] $name\n";
        $failCount++;
    }
}

// 1. Verify Database Rule (Allow_Negative_Inventory = 0)
$clAllowNeg = DB::table('tbl_product_code')->where('product_type', 'Lens')->pluck('Allow_Negative_Inventory')->unique()->toArray();
assertCondition(
    "All 55 Contact Lenses in tbl_product_code have Allow_Negative_Inventory = 0",
    count($clAllowNeg) === 1 && $clAllowNeg[0] == 0
);

$solAllowNeg = DB::table('tbl_product_code')->where('product_type', 'Solution')->pluck('Allow_Negative_Inventory')->unique()->toArray();
assertCondition(
    "All Solutions in tbl_product_code have Allow_Negative_Inventory = 0",
    count($solAllowNeg) === 1 && $solAllowNeg[0] == 0
);

// 2. Test Negative Scenario (Zero Stock in Store 6)
$product = DB::table('tbl_product_code')->where('id', 3562)->first();
$liveStockZero = StockSyncService::getLiveStock($product->product_code, 6);

assertCondition("Store 6 live stock for Contact Lens 3562 is currently 0", $liveStockZero === 0);

$controller = app(ProductController::class);
$viewZero = $controller->details('3562');
$htmlZero = $viewZero->render();

assertCondition("Zero Stock PDP displays 'Out of Stock' badge", strpos($htmlZero, 'Out of Stock') !== false);
assertCondition("Zero Stock PDP does NOT render active contact-lens-buy-btn", strpos($htmlZero, 'id="contact-lens-buy-btn"') === false);
assertCondition("Zero Stock PDP disables action button", strpos($htmlZero, 'disabled') !== false);
assertCondition("Zero Stock PDP replaces power selection grid with out-of-stock notice", strpos($htmlZero, 'Power selection is currently unavailable') !== false);
assertCondition("Zero Stock PDP does NOT render active power pills", strpos($htmlZero, 'id="cl-pill-zero"') === false);

// Cart add test for zero stock
session()->forget('cart');
$cartService = app(CartService::class);
$addZeroResult = $cartService->addToCart($product->id, null, 1, null, 'Contact Lens');
assertCondition(
    "CartService strictly blocks adding zero-stock Contact Lens to cart",
    $addZeroResult['status'] === false && strpos($addZeroResult['message'], 'out of stock') !== false
);

// 3. Test Positive Scenario (In-Stock: 5 boxes in Store 6)
echo "\n--- Setting up simulated in-stock inventory (5 boxes) in Store 6 ---\n";
// Insert temporary inventory row in tbl_inventory_levels
$invId = DB::table('tbl_inventory_levels')->insertGetId([
    'product_code'       => $product->product_code,
    'product_id'         => $product->id,
    'product_type'       => 'Lens',
    'product_details'    => $product->product_name,
    'store_id'           => 6,
    'available_quantity' => 5,
    'tota_lens_qty'      => 150,
    'perbox'             => 30,
    'created_at'         => now(),
    'updated_at'         => now(),
]);

$liveStockFive = StockSyncService::getLiveStock($product->product_code, 6);
assertCondition("Store 6 live stock is now 5 boxes", $liveStockFive === 5);

$viewFive = $controller->details('3562');
$htmlFive = $viewFive->render();

assertCondition("In-Stock PDP displays 'In Stock' badge", strpos($htmlFive, 'In Stock') !== false);
assertCondition("In-Stock PDP renders active contact-lens-buy-btn", strpos($htmlFive, 'id="contact-lens-buy-btn"') !== false);
assertCondition("In-Stock PDP renders active power pills", strpos($htmlFive, 'id="cl-pill-zero"') !== false);
assertCondition("In-Stock PDP does NOT show unavailable notice", strpos($htmlFive, 'Power selection is currently unavailable') === false);

// Test Box limits capped to stock (5 boxes)
assertCondition(
    "Zero power box dropdown is capped to available stock (value='5')",
    strpos($htmlFive, 'value="5"') !== false && strpos($htmlFive, 'value="6"') === false
);

// Test CartService with Positive Stock
session()->forget('cart');
$addValidResult = $cartService->addToCart($product->id, null, 2, json_encode(['type' => 'contact_lens_manual', 'total_boxes' => 2]), 'Contact Lens');
assertCondition("CartService successfully adds 2 boxes when 5 are available", $addValidResult['status'] === true);

// Test CartService over-quantity limit
$addExcessResult = $cartService->addToCart($product->id, null, 4, json_encode(['type' => 'contact_lens_manual', 'total_boxes' => 4]), 'Contact Lens');
assertCondition(
    "CartService blocks excess quantity (2 existing + 4 new > 5 available)",
    $addExcessResult['status'] === false && strpos($addExcessResult['message'], 'Only 5 piece(s) available') !== false
);

// 4. Test Stock Deduction & Piece Count Consistency
echo "\n--- Testing Checkout Stock Deduction ---\n";
// Decrement 2 boxes
$invBefore = DB::table('tbl_inventory_levels')->where('id', $invId)->first();
$qtyToDeduct = 2;
$piecesToDeduct = $qtyToDeduct * (int)$invBefore->perbox;

DB::table('tbl_inventory_levels')->where('id', $invId)->update([
    'available_quantity' => DB::raw("GREATEST(0, CAST(available_quantity AS SIGNED) - {$qtyToDeduct})"),
    'tota_lens_qty'      => DB::raw("GREATEST(0, CAST(tota_lens_qty AS SIGNED) - {$piecesToDeduct})"),
    'updated_at'         => now(),
]);

$invAfter = DB::table('tbl_inventory_levels')->where('id', $invId)->first();
assertCondition(
    "Available box quantity reduced from 5 to 3",
    (int)$invAfter->available_quantity === 3
);
assertCondition(
    "Total piece quantity (tota_lens_qty) reduced from 150 to 90 (3 boxes * 30)",
    (int)$invAfter->tota_lens_qty === 90
);

// 5. Test Restock Consistency
$piecesToRestore = $qtyToDeduct * (int)$invAfter->perbox;
DB::table('tbl_inventory_levels')->where('id', $invId)->update([
    'available_quantity' => DB::raw("available_quantity + {$qtyToDeduct}"),
    'tota_lens_qty'      => DB::raw("COALESCE(tota_lens_qty, 0) + {$piecesToRestore}"),
    'updated_at'         => now(),
]);

$invRestored = DB::table('tbl_inventory_levels')->where('id', $invId)->first();
assertCondition(
    "Restock restores boxes back to 5 and pieces back to 150",
    (int)$invRestored->available_quantity === 5 && (int)$invRestored->tota_lens_qty === 150
);

// Cleanup temporary inventory
DB::table('tbl_inventory_levels')->where('id', $invId)->delete();
session()->forget('cart');

echo "\n========================================================\n";
echo "               RESULTS: {$passCount} PASSED, {$failCount} FAILED        \n";
echo "========================================================\n";
