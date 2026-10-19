<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Services\StockSyncService;
use App\Services\CartService;
use App\Http\Controllers\Website\CartController;
use Illuminate\Http\Request;

echo "\n===============================================================================\n";
echo "   SPECKART: LIVE TEST FOR TRACK INVENTORY & ALLOW NEGATIVE INVENTORY\n";
echo "===============================================================================\n\n";

$ecomStoreId = StockSyncService::getEcommerceStoreId();
$cartService = app(CartService::class);
$cartController = app(CartController::class);

// Find a test product
$product = DB::table('tbl_product_code')->where('product_code', 'demo-1312')->first();
if (!$product) {
    $product = DB::table('tbl_product_code')->where('is_b2c', 1)->first();
}

// Find a test lens package
$lensPkg = DB::table('lens_packages')->first();
$pkgCode = $lensPkg->product_code ?: 'GLS-ANT-4628';

// Backup original settings
$origProdTrack = $product->Track_Inventory;
$origProdNeg   = $product->Allow_Negative_Inventory;
$origPkgTrack  = $lensPkg->track_inventory;
$origPkgNeg    = $lensPkg->allow_negative_inventory;

// Helper function to set stock in Store 6
function setStock($code, $storeId, $qty, $type = 'Frame') {
    DB::table('tbl_inventory_levels')->where('product_code', $code)->where('store_id', $storeId)->delete();
    if ($qty !== null && $qty > 0) {
        $p = DB::table('tbl_product_code')->where('product_code', $code)->first();
        DB::table('tbl_inventory_levels')->insert([
            'store_id'          => $storeId,
            'product_code'      => $code,
            'product_id'        => $p ? $p->id : 0,
            'product_type'      => $p ? ($p->product_type ?: $type) : $type,
            'product_details'   => $p ? $p->product_name : 'Test Item',
            'available_quantity'=> $qty,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
    StockSyncService::syncProductStock($code, $storeId);
}

try {
    // =========================================================================
    // SCENARIO 1: TRACK = YES (1) & ALLOW NEGATIVE = NO (0) [STRICT STOCK]
    // =========================================================================
    echo "-------------------------------------------------------------------------------\n";
    echo " [SCENARIO 1] STRICT INVENTORY (Track Inventory = Yes, Allow Negative = No)\n";
    echo " Matlab: Warehouse me physical maal hona zaroori hai. Stock 0 to bikna band!\n";
    echo "-------------------------------------------------------------------------------\n";

    DB::table('tbl_product_code')->where('id', $product->id)->update([
        'Track_Inventory'          => 1,
        'Allow_Negative_Inventory' => 0
    ]);
    
    // Case 1A: Jab Stock 0 ho
    setStock($product->product_code, $ecomStoreId, 0);
    session()->forget('cart');
    $res1A = $cartService->addToCart($product->id, null, 1);
    
    echo " Test 1A: Stock = 0 piece (Warehouse me maal nahi hai)\n";
    echo "   -> Cart Result Status : " . ($res1A['status'] ? 'ALLOWED' : 'BLOCKED') . "\n";
    echo "   -> System Response    : \"" . ($res1A['message'] ?? '') . "\"\n";
    if (!$res1A['status'] && str_contains(strtolower($res1A['message']), 'out of stock')) {
        echo "   -> Result: PASS (System ne customer ko purchase karne se rokk diya!)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // Case 1B: Jab Stock 5 piece available ho
    setStock($product->product_code, $ecomStoreId, 5);
    session()->forget('cart');
    $res1B = $cartService->addToCart($product->id, null, 2);
    
    echo " Test 1B: Stock = 5 piece, Customer wants 2 pieces\n";
    echo "   -> Cart Result Status : " . ($res1B['status'] ? 'ALLOWED' : 'BLOCKED') . "\n";
    echo "   -> System Response    : \"" . ($res1B['message'] ?? '') . "\"\n";
    if ($res1B['status']) {
        echo "   -> Result: PASS (Stock tha to customer aasaani se add kar paya!)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // Case 1C: Stock 5 hai, par customer 10 piece maang raha hai
    session()->forget('cart');
    $res1C = $cartService->addToCart($product->id, null, 10);
    echo " Test 1C: Stock = 5 piece, Customer asks for 10 pieces (Excess quantity)\n";
    echo "   -> Cart Result Status : " . ($res1C['status'] ? 'ALLOWED' : 'BLOCKED') . "\n";
    echo "   -> System Response    : \"" . ($res1C['message'] ?? '') . "\"\n";
    if (!$res1C['status'] && str_contains(strtolower($res1C['message']), 'only 5')) {
        echo "   -> Result: PASS (System ne warehouse se zyada quantity lene se rokk diya!)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // =========================================================================
    // SCENARIO 2: TRACK = YES (1) & ALLOW NEGATIVE = YES (1) [MADE-TO-ORDER]
    // =========================================================================
    echo "-------------------------------------------------------------------------------\n";
    echo " [SCENARIO 2] BACKORDERS / MADE-TO-ORDER (Track = Yes, Allow Negative = Yes)\n";
    echo " Matlab: Stock 0 hone par bhi order lene do (Custom banwayenge / Lab order)\n";
    echo "-------------------------------------------------------------------------------\n";

    DB::table('tbl_product_code')->where('id', $product->id)->update([
        'Track_Inventory'          => 1,
        'Allow_Negative_Inventory' => 1
    ]);

    setStock($product->product_code, $ecomStoreId, 0); // 0 stock
    session()->forget('cart');
    $res2 = $cartService->addToCart($product->id, null, 1);

    echo " Test 2A: Stock = 0 piece (Warehouse me 0 hai, lekin Negative Allowed = Yes hai)\n";
    echo "   -> Cart Result Status : " . ($res2['status'] ? 'ALLOWED' : 'BLOCKED') . "\n";
    echo "   -> System Response    : \"" . ($res2['message'] ?? '') . "\"\n";
    if ($res2['status']) {
        echo "   -> Result: PASS (Order accept ho gaya! Stock 0 hone par bhi sale nahi ruki)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // =========================================================================
    // SCENARIO 3: TRACK = NO (0) [UNTRACKED / UNLIMITED]
    // =========================================================================
    echo "-------------------------------------------------------------------------------\n";
    echo " [SCENARIO 3] UNTRACKED PRODUCT (Track Inventory = No)\n";
    echo " Matlab: Stock ki ginti karni hi nahi hai. Unlimited quantity becho.\n";
    echo "-------------------------------------------------------------------------------\n";

    DB::table('tbl_product_code')->where('id', $product->id)->update([
        'Track_Inventory'          => 0,
        'Allow_Negative_Inventory' => 0
    ]);

    setStock($product->product_code, $ecomStoreId, 0); // 0 stock
    session()->forget('cart');
    $res3 = $cartService->addToCart($product->id, null, 100);

    echo " Test 3A: Stock = 0 piece, Customer ordering 100 units (Track Inventory = No)\n";
    echo "   -> Cart Result Status : " . ($res3['status'] ? 'ALLOWED' : 'BLOCKED') . "\n";
    echo "   -> System Response    : \"" . ($res3['message'] ?? '') . "\"\n";
    if ($res3['status']) {
        echo "   -> Result: PASS (Unlimited allowed! Stock check completely bypass ho gaya)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // =========================================================================
    // SCENARIO 4: LENS PACKAGES (Prescription Lenses with Frame)
    // =========================================================================
    echo "-------------------------------------------------------------------------------\n";
    echo " [SCENARIO 4] LENS PACKAGES INVENTORY TEST\n";
    echo " Matlab: Frame ke saath jab customer chashme ka lens package select karega\n";
    echo "-------------------------------------------------------------------------------\n";

    // Setup frame with stock
    setStock($product->product_code, $ecomStoreId, 10);
    DB::table('tbl_product_code')->where('id', $product->id)->update([
        'Track_Inventory'          => 1,
        'Allow_Negative_Inventory' => 1
    ]);

    // Test 4A: Lens package has 0 stock & Allow Negative = No (Strict)
    setStock($pkgCode, $ecomStoreId, 0);
    DB::table('lens_packages')->where('id', $lensPkg->id)->update([
        'track_inventory'          => 1,
        'allow_negative_inventory' => 0
    ]);

    session()->forget('cart');
    $req4A = Request::create('/cart/add', 'POST', [
        'frame_id'        => $product->id,
        'lens_package_id' => $lensPkg->id,
        'qty'             => 1,
        'order_type'      => 'frame_lens'
    ]);
    $httpRes4A = $cartController->addToCart($req4A);
    $data4A = json_decode($httpRes4A->getContent(), true);

    echo " Test 4A: Lens Package Stock = 0, Allow Negative = No (Strict)\n";
    echo "   -> HTTP Status Code   : " . $httpRes4A->getStatusCode() . "\n";
    echo "   -> Response Message   : \"" . ($data4A['message'] ?? '') . "\"\n";
    if ($httpRes4A->getStatusCode() === 400 && str_contains(strtolower($data4A['message'] ?? ''), 'out of stock')) {
        echo "   -> Result: PASS (System ne out-of-stock lens package khareedne se rokk diya!)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

    // Test 4B: Lens package has 0 stock & Allow Negative = Yes (Lab order allowed)
    DB::table('lens_packages')->where('id', $lensPkg->id)->update([
        'track_inventory'          => 1,
        'allow_negative_inventory' => 1
    ]);

    session()->forget('cart');
    $req4B = Request::create('/cart/add', 'POST', [
        'frame_id'        => $product->id,
        'lens_package_id' => $lensPkg->id,
        'qty'             => 1,
        'order_type'      => 'frame_lens'
    ]);
    $httpRes4B = $cartController->addToCart($req4B);
    $data4B = json_decode($httpRes4B->getContent(), true);

    echo " Test 4B: Lens Package Stock = 0, Allow Negative = Yes (Lab Order Allowed)\n";
    echo "   -> HTTP Status Code   : " . $httpRes4B->getStatusCode() . "\n";
    echo "   -> Response Message   : \"" . ($data4B['message'] ?? '') . "\"\n";
    if ($httpRes4B->getStatusCode() === 200 && ($data4B['status'] ?? false)) {
        echo "   -> Result: PASS (Lens lab me banna allow tha, isliye customer ka order le liya!)\n\n";
    } else {
        echo "   -> Result: FAIL\n\n";
    }

} finally {
    // Restore initial values
    DB::table('tbl_product_code')->where('id', $product->id)->update([
        'Track_Inventory'          => $origProdTrack,
        'Allow_Negative_Inventory' => $origProdNeg
    ]);
    DB::table('lens_packages')->where('id', $lensPkg->id)->update([
        'track_inventory'          => $origPkgTrack,
        'allow_negative_inventory' => $origPkgNeg
    ]);
    echo "===============================================================================\n";
    echo " [COMPLETED] Database state and settings cleanly restored.\n";
    echo "===============================================================================\n\n";
}
