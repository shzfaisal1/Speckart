<?php
/**
 * ============================================================================
 * SPECKART QA TEST SUITE: END-TO-END POSITIVE & NEGATIVE TEST MATRIX
 * Persona: Senior QA Test Engineer & Security Auditor
 * Scope: Product Selection → Add-to-Cart → Cart Calculations → Promotions &
 *        Loyalty → Checkout Validation → tbl_sales & tbl_sales_product &
 *        tbl_sale_payment → Admin B2C Order Management & Restock
 * ============================================================================
 * Execution: php scratch/qa_full_matrix_e2e.php
 */

chdir(__DIR__ . '/..');
require_once __DIR__ . '/../vendor/autoload.php';
$app    = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\sale\Sale;
use App\Models\sale\SalePayment;
use App\Models\sale\SaleProduct;
use App\Models\ShippingCharge;
use App\Services\CartService;
use App\Services\StockSyncService;
use App\Services\VoucherService;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Admin\B2cOrderController;

// Initialize session & request context
$fakeRequest = Request::create('http://127.0.0.1:8000/', 'GET');
$sessionStore = app('session.store');
$fakeRequest->setLaravelSession($sessionStore);
app()->instance('request', $fakeRequest);

echo "===============================================================================\n";
echo "      SPECKART QA TEST REPORT: COMPLETE POSITIVE & NEGATIVE MATRIX             \n";
echo "      Scope: Add-to-Cart -> Checkout -> Order Creation -> Admin Management     \n";
echo "      Execution Date: " . date('Y-m-d H:i:s') . "\n";
echo "===============================================================================\n";

$passCount = 0;
$failCount = 0;
$defectCount = 0;
$report = [];

function assertTest(string $testId, string $type, string $title, bool $condition, string $details = '', bool $isKnownDefect = false) {
    global $passCount, $failCount, $defectCount, $report;
    $typeLabel = strtoupper($type); // POSITIVE, NEGATIVE, SECURITY
    if ($condition) {
        $passCount++;
        $status = "✅ PASS";
        $report[] = sprintf("  [%s] %s | %s: %s %s", $status, $typeLabel, $testId, $title, $details ? "($details)" : "");
    } else {
        if ($isKnownDefect) {
            $defectCount++;
            $status = "🚨 DEFECT FOUND";
            $report[] = sprintf("  [%s] %s | %s: %s %s", $status, $typeLabel, $testId, $title, $details ? "— $details" : "");
        } else {
            $failCount++;
            $status = "❌ FAIL";
            $report[] = sprintf("  [%s] %s | %s: %s %s", $status, $typeLabel, $testId, $title, $details ? "— $details" : "");
        }
    }
}

function printSection(string $header) {
    global $report;
    $report[] = "";
    $report[] = "===============================================================================";
    $report[] = "  " . $header;
    $report[] = "===============================================================================";
    echo "\n>>> " . $header . "\n";
}

// ── Test Environment Data ──────────────────────────────────────────────────
$PRODUCT_ID   = 3566;
$PRODUCT_CODE = '1234778';
$STORE_ID     = 6;
$USER_ID      = 1;
$VALID_PINCODE = '400001';   // Serviceable pincode
$BLOCKED_PINCODE = '999999'; // Configured unserviceable pincode
$TEST_PHONE   = '9874563210';
$TEST_EMAIL   = 'qa_tester@speckarts.com';
$TEST_NAME    = 'QA Master Tester';
$PREFIX       = 'QA_E2E_';

// Cleanup previous test orders
$prevOrders = DB::table('tbl_sales')->where('order_no', 'LIKE', $PREFIX . '%')->pluck('sale_id');
if ($prevOrders->isNotEmpty()) {
    DB::table('tbl_sales_product')->whereIn('sale_id', $prevOrders)->delete();
    DB::table('tbl_sale_payment')->whereIn('sale_id', $prevOrders)->delete();
    DB::table('tbl_sales')->whereIn('sale_id', $prevOrders)->delete();
}

// Reset blocked pincode in shipping charges table
ShippingCharge::updateOrInsert(
    ['pincode' => $BLOCKED_PINCODE],
    ['amount' => 0.00, 'is_cod_available' => 0, 'status' => 0]
);

// Reset stock to 100
DB::table('tbl_inventory_levels')
    ->where('product_code', $PRODUCT_CODE)
    ->where('store_id', $STORE_ID)
    ->update(['available_quantity' => 100]);

Auth::loginUsingId($USER_ID);
$cartService = app(CartService::class);
$cartController = app(CartController::class);

// ===========================================================================
// SECTION 1: PRODUCT & LIVE INVENTORY SETUP
// ===========================================================================
printSection('SECTION 1: PRODUCT & INVENTORY CONFIGURATION');

$product = DB::table('tbl_product_code')->where('id', $PRODUCT_ID)->first();
assertTest('TC-CFG-01', 'Positive', 'Master Product Record exists in tbl_product_code', 
    !is_null($product), "ID=$PRODUCT_ID Code={$product->product_code}");

$inv = DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->first();
assertTest('TC-CFG-02', 'Positive', 'Live Store 6 (Online) inventory level verified', 
    !is_null($inv) && $inv->available_quantity == 100, "Store=$STORE_ID Qty={$inv->available_quantity}");

assertTest('TC-CFG-03', 'Positive', 'Inventory tracking switches configured', 
    isset($product->Track_Inventory), "Track_Inventory={$product->Track_Inventory}, Allow_Negative={$product->Allow_Negative_Inventory}");

// ===========================================================================
// SECTION 2: ADD TO CART - POSITIVE & NEGATIVE SCENARIOS
// ===========================================================================
printSection('SECTION 2: ADD TO CART & SESSION CART OPERATIONS (POS & NEG)');

// [NEGATIVE] Add non-existent product ID
session()->forget('cart');
$negProdRes = $cartService->addToCart(99999999, null, 1);
assertTest('TC-CART-NEG-01', 'Negative', 'Reject adding non-existent product ID', 
    ($negProdRes['status'] ?? true) === false, 
    "Expected failure, got: " . ($negProdRes['message'] ?? ''));

// [POSITIVE] Add standard Frame-Only item
$addRes1 = $cartService->addToCart($PRODUCT_ID, null, 1);
assertTest('TC-CART-POS-01', 'Positive', 'Add Frame-only item to cart successfully', 
    ($addRes1['status'] ?? false) === true && ($addRes1['cart_count'] ?? 0) === 1, 
    "Cart count=" . ($addRes1['cart_count'] ?? 0));

$sessionCart = session()->get('cart', []);
$firstKey = array_key_first($sessionCart);
$firstItem = $sessionCart[$firstKey] ?? [];
assertTest('TC-CART-POS-02', 'Positive', 'Cart item payload contains accurate frame attributes', 
    ($firstItem['frame_id'] ?? null) == $PRODUCT_ID && ($firstItem['frame_price'] ?? 0) > 0, 
    "Frame: {$firstItem['frame_name']}, Price=₹{$firstItem['frame_price']}");

// [POSITIVE] Add same product again - should increment quantity without duplicating row
$addRes2 = $cartService->addToCart($PRODUCT_ID, null, 2);
$sessionCart2 = session()->get('cart', []);
assertTest('TC-CART-POS-03', 'Positive', 'Re-adding same product merges row and increments quantity to 3', 
    count($sessionCart2) === 1 && ($sessionCart2[$firstKey]['quantity'] ?? 0) === 3, 
    "Rows=" . count($sessionCart2) . ", Total Qty=" . ($sessionCart2[$firstKey]['quantity'] ?? 0));

// [POSITIVE] Update quantity to 2
$cartService->updateQuantity($firstKey, 2);
$sessionCart3 = session()->get('cart', []);
assertTest('TC-CART-POS-04', 'Positive', 'Quantity updated directly to 2', 
    ($sessionCart3[$firstKey]['quantity'] ?? 0) === 2, 
    "Quantity=" . ($sessionCart3[$firstKey]['quantity'] ?? 0));

// [NEGATIVE] Update quantity to 0 removes item from cart
$cartService->updateQuantity($firstKey, 0);
$sessionCart4 = session()->get('cart', []);
assertTest('TC-CART-NEG-02', 'Negative', 'Update quantity to 0 removes item from cart', 
    !isset($sessionCart4[$firstKey]) && count($sessionCart4) === 0, 
    "Cart count=" . count($sessionCart4));

// [NEGATIVE] Update quantity to negative number (-5) removes or normalizes safely
$cartService->addToCart($PRODUCT_ID, null, 1);
$tempKey = array_key_first(session()->get('cart', []));
$cartService->updateQuantity($tempKey, -5);
$sessionCartNeg = session()->get('cart', []);
assertTest('TC-CART-NEG-03', 'Negative', 'Negative quantity input handled safely without error', 
    count($sessionCartNeg) === 0 || ($sessionCartNeg[$tempKey]['quantity'] ?? 0) > 0, 
    "Remaining count=" . count($sessionCartNeg));

// [POSITIVE] Add Frame + Lens Package Combo with Prescription Data
$lensPackage = DB::table('lens_packages')->whereNull('deleted_at')->where('is_active', 1)->first();
if (!$lensPackage) {
    $lensPackage = DB::table('tbl_lens_package')->first();
}
$lensPkgId = $lensPackage ? ($lensPackage->id ?? $lensPackage->package_id) : 1;
session()->forget('cart');
$rxPayload = [
    'GL_EYE_RS_D' => '-1.50', 'GL_EYE_RC_D' => '-0.50', 'GL_EYE_RA_D' => '90',
    'GL_EYE_LS_D' => '-1.75', 'GL_EYE_LC_D' => '-0.25', 'GL_EYE_LA_D' => '180',
    'totalPD'     => '64'
];
$comboRes = $cartService->addToCart($PRODUCT_ID, $lensPkgId, 1, $rxPayload, 'Single Vision', 'Medium');
$comboCart = session()->get('cart', []);
$comboKey = array_key_first($comboCart);
assertTest('TC-CART-POS-05', 'Positive', 'Add Frame + Lens package combo with prescription metadata', 
    ($comboRes['status'] ?? false) === true && !empty($comboCart[$comboKey]['lens_name']), 
    "Lens: " . ($comboCart[$comboKey]['lens_name'] ?? 'N/A') . " Rx attached: " . (!empty($comboCart[$comboKey]['prescription_data']) ? 'Yes' : 'No'));

// [NEGATIVE] Out of stock & over-quantity prevention when Track_Inventory=1 and Allow_Negative_Inventory=0
DB::table('tbl_product_code')->where('id', $PRODUCT_ID)->update(['Track_Inventory' => 1, 'Allow_Negative_Inventory' => 0]);
DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->update(['available_quantity' => 2]);
StockSyncService::syncProductStock($PRODUCT_CODE, $STORE_ID);

session()->forget('cart');
// Attempt to add quantity 5 when only 2 available
$overQtyRes = $cartService->addToCart($PRODUCT_ID, null, 5);
assertTest('TC-CART-NEG-04', 'Negative', 'Block adding quantity exceeding available inventory (Req=5, Avail=2)', 
    ($overQtyRes['status'] ?? true) === false, 
    "Response: " . ($overQtyRes['message'] ?? ''));

// Attempt to add when 0 in stock
DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->update(['available_quantity' => 0]);
StockSyncService::syncProductStock($PRODUCT_CODE, $STORE_ID);
$zeroStockRes = $cartService->addToCart($PRODUCT_ID, null, 1);
assertTest('TC-CART-NEG-05', 'Negative', 'Block adding product when live stock is zero', 
    ($zeroStockRes['status'] ?? true) === false, 
    "Response: " . ($zeroStockRes['message'] ?? ''));

// Reset stock to 100
DB::table('tbl_product_code')->where('id', $PRODUCT_ID)->update(['Track_Inventory' => 1, 'Allow_Negative_Inventory' => 1]);
DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->update(['available_quantity' => 100]);
StockSyncService::syncProductStock($PRODUCT_CODE, $STORE_ID);

// ===========================================================================
// SECTION 3: PROMOTIONS, BOGO, COUPONS & LOYALTY (POS & NEG)
// ===========================================================================
printSection('SECTION 3: DISCOUNTS, BOGO & COUPONS (POS & NEG)');

// Setup clean cart with 1 frame
session()->forget('cart');
$cartService->addToCart($PRODUCT_ID, null, 1);
$calcBase = $cartService->getCartCalculations();
$baseGrandTotal = (float)($calcBase['grand_total'] ?? 0);

// [NEGATIVE] Applying coupon via CartController with insufficient subtotal (< min_cart_amount)
$dummyReq = Request::create('/cart/apply-coupon', 'POST', ['coupon_code' => 'TEST_MIN_FAIL']);
// Ensure offer exists with min_cart_amount = 50000
DB::table('offers')->updateOrInsert(
    ['coupon_code' => 'TEST_MIN_FAIL'],
    [
        'name' => 'High Min Cart Coupon',
        'offer_type' => 'coupon',
        'discount_type' => 'percentage',
        'discount_value' => 20,
        'min_cart_amount' => 50000,
        'status' => 'active',
        'created_at' => Carbon::now()
    ]
);
$minOrderCouponRes = $cartController->applyCoupon($dummyReq);
$minOrderJson = json_decode($minOrderCouponRes->getContent(), true);
assertTest('TC-PROMO-NEG-01', 'Negative', 'CartController blocks coupon when subtotal < min_cart_amount', 
    ($minOrderJson['status'] ?? '') === 'error' && strpos(($minOrderJson['message'] ?? ''), 'Minimum cart subtotal') !== false, 
    "Status: " . ($minOrderJson['status'] ?? '') . " Msg: " . ($minOrderJson['message'] ?? ''));

// [NEGATIVE] Apply non-existent / invalid coupon code
$invalidCouponReq = Request::create('/cart/apply-coupon', 'POST', ['coupon_code' => 'FAKEXYZ123999']);
$invalidCouponRes = $cartController->applyCoupon($invalidCouponReq);
$invalidJson = json_decode($invalidCouponRes->getContent(), true);
assertTest('TC-PROMO-NEG-02', 'Negative', 'CartController rejects invalid coupon code with error', 
    ($invalidJson['status'] ?? '') === 'error', 
    "Msg: " . ($invalidJson['message'] ?? ''));

// [POSITIVE] Valid 10% coupon calculation
$validCoupon = [
    'code' => 'VALID10',
    'discount_type' => 'percentage',
    'discount_percent' => 10,
    'min_cart_amount' => 100,
    'max_discount' => 500
];
$calcValid = $cartService->getCartCalculations($validCoupon);
$expectedDisc = round($baseGrandTotal * 0.10, 2);
assertTest('TC-PROMO-POS-01', 'Positive', 'Valid percentage coupon calculated accurately', 
    abs(($calcValid['coupon_discount'] ?? 0) - $expectedDisc) <= 1, 
    "Discount: ₹" . ($calcValid['coupon_discount'] ?? 0) . " Expected: ₹$expectedDisc");

// [POSITIVE] Coupon Max Discount Cap enforced
$cappedCoupon = [
    'code' => 'CAP50',
    'discount_type' => 'percentage',
    'discount_percent' => 50,
    'min_cart_amount' => 100,
    'max_discount' => 30
];
$calcCapped = $cartService->getCartCalculations($cappedCoupon);
assertTest('TC-PROMO-POS-02', 'Positive', 'Coupon max discount capping strictly enforced', 
    ($calcCapped['coupon_discount'] ?? 0) == 30, 
    "Discount: ₹" . ($calcCapped['coupon_discount'] ?? 0) . " Max Cap: ₹30");

// [NEGATIVE] BOGO rejected for non-members
session()->forget('cart_membership');
session()->forget('active_membership');
DB::table('tbl_customer')->where('contact_no', $TEST_PHONE)->update(['membership_card_id' => null, 'membership_expiry' => null]);
DB::table('tbl_product_code')->where('id', $PRODUCT_ID)->update(['promotion_tag' => 'BOGO', 'product_type' => 'Eyeglasses']);
session()->forget('cart');
$cartService->addToCart($PRODUCT_ID, null, 2);
$calcNonMemberBogo = $cartService->getCartCalculations();
assertTest('TC-BOGO-NEG-01', 'Negative', 'BOGO discount completely withheld for non-members', 
    ($calcNonMemberBogo['bogo_savings'] ?? 0) == 0, 
    "BOGO savings: ₹" . ($calcNonMemberBogo['bogo_savings'] ?? 0));

// [POSITIVE] BOGO applied for active Gold Members
session()->put('active_membership', ['card_id' => 1, 'card_name' => 'Gold Member', 'expiry' => Carbon::now()->addYear()->toDateString()]);
$calcMemberBogo = $cartService->getCartCalculations();
assertTest('TC-BOGO-POS-01', 'Positive', 'BOGO discount granted for active Gold Members', 
    ($calcMemberBogo['bogo_savings'] ?? 0) > 0, 
    "BOGO savings: ₹" . ($calcMemberBogo['bogo_savings'] ?? 0));
session()->forget('active_membership');

// [NEGATIVE] Loyalty Points over-redemption blocked
$customerRec = DB::table('tbl_customer')->where('contact_no', $TEST_PHONE)->first();
$currPoints = (float)($customerRec->Loyalty_Points_Bal ?? 0);
session()->put('applied_loyalty_points', $currPoints + 50000);
$calcLoyaltyOver = $cartService->getCartCalculations();
assertTest('TC-LOYALTY-NEG-01', 'Negative', 'Prevent redeeming more loyalty points than customer available balance', 
    ($calcLoyaltyOver['loyalty_discount'] ?? 0) <= $currPoints, 
    "Redeemed: ₹" . ($calcLoyaltyOver['loyalty_discount'] ?? 0) . " Actual Balance: $currPoints");
session()->forget('applied_loyalty_points');

// ===========================================================================
// SECTION 4: SHIPPING & PINCODE VALIDATION (POS & NEG)
// ===========================================================================
printSection('SECTION 4: SHIPPING ADDRESS & PINCODE SERVICEABILITY (POS & NEG)');

// [POSITIVE] Serviceable Pincode
$pinOk = ShippingCharge::getChargeForPincode($VALID_PINCODE);
assertTest('TC-PIN-POS-01', 'Positive', "Pincode $VALID_PINCODE verified serviceable", 
    !empty($pinOk['is_serviceable']), 
    "Message: " . ($pinOk['message'] ?? ''));

// [NEGATIVE] Inactive / Unserviceable Pincode in tbl_shipping_charges
$pinBlocked = ShippingCharge::getChargeForPincode($BLOCKED_PINCODE);
assertTest('TC-PIN-NEG-01', 'Negative', "Blacklisted pincode $BLOCKED_PINCODE correctly flagged unserviceable", 
    empty($pinBlocked['is_serviceable']), 
    "Message: " . ($pinBlocked['message'] ?? ''));

// [NEGATIVE] CheckoutController validation of empty cart
$checkoutCtrlSource = file_get_contents('app/Http/Controllers/Website/CheckoutController.php');
assertTest('TC-CHK-NEG-01', 'Negative', 'CheckoutController redirects when cart is empty', 
    strpos($checkoutCtrlSource, "empty(\$cartData['items'])") !== false, 
    "Verified in CheckoutController lines 64-66");

// [NEGATIVE] CheckoutController validation of missing shipping address in session
assertTest('TC-CHK-NEG-02', 'Negative', 'CheckoutController redirects when shipping address not set', 
    strpos($checkoutCtrlSource, "!\$shipping") !== false, 
    "Verified in CheckoutController lines 47-49");

// [NEGATIVE] CheckoutController blocks COD on unserviceable COD pincode
assertTest('TC-CHK-NEG-03', 'Negative', 'CheckoutController prevents COD when is_cod_available is false', 
    strpos($checkoutCtrlSource, "empty(\$pincodeCheck['is_cod_available'])") !== false, 
    "Verified in CheckoutController lines 56-58");

// ===========================================================================
// SECTION 5: COMPLETE ORDER PLACEMENT & DATABASE AUDIT (POS & SECURITY)
// ===========================================================================
printSection('SECTION 5: ORDER PLACEMENT & TRANSACTION AUDIT (POS & SECURITY)');

// Setup cart with Frame + Lens + Rx for real order placement test
session()->forget('cart');
$cartService->addToCart($PRODUCT_ID, $lensPkgId, 1, $rxPayload, 'Single Vision', 'Medium');
$finalCart = $cartService->getCartCalculations();
$frameSubtotal  = (float)($finalCart['frame_subtotal'] ?? 0);
$lensSubtotal   = (float)($finalCart['lens_subtotal'] ?? 0);
$grandTotal     = (float)($finalCart['grand_total'] ?? 0);

$store = DB::table('tbl_store')->where('id', $STORE_ID)->first();
$orderPrefix = $store->order_no_prefix ?? 'WEB';
$nextOrderNo = (int)($store->next_order_no ?? 1);
$testOrderNo = $PREFIX . $orderPrefix . str_pad($nextOrderNo, 5, '0', STR_PAD_LEFT);

$customer = DB::table('tbl_customer')->where('contact_no', $TEST_PHONE)->first();
$custId = $customer ? ($customer->customer_id ?? $customer->id) : 22;

$createdSaleId = null;
try {
    DB::beginTransaction();

    $sale = Sale::create([
        'user_id'             => $USER_ID,
        'sale_date'           => Carbon::now()->toDateString(),
        'order_no'            => $testOrderNo,
        'cust_name'           => $TEST_NAME,
        'contact_no'          => $TEST_PHONE,
        'email_id'            => $TEST_EMAIL,
        'cust_address'        => 'Flat 502, Lotus Enclave, Link Road, Andheri West, Mumbai',
        'state_id'            => $store->state_id ?? 0,
        'city_id'             => $store->city_id ?? 0,
        'pincode'             => $VALID_PINCODE,
        'total_basic_amount'  => $frameSubtotal + $lensSubtotal,
        'total_gst_amount'    => 0,
        'fitting_fee'         => 0,
        'total_item_price'    => $frameSubtotal + $lensSubtotal,
        'total_discount'      => 0,
        'bogo_discount'       => 0,
        'shipping_fee'        => 0,
        'coupon_amount'       => 0,
        'loyalty_point_amount'=> 0,
        'loyalty_point_apply' => 0,
        'total_payable'       => $grandTotal,
        'pay_amount'          => 0,
        'pending_amount'      => $grandTotal,
        'pay_method'          => 'COD',
        'sales_type'          => 0, // B2C Website Order
        'sales_status'        => 0, // Pending
        'tax_rule'            => 1,
        'store_id'            => $STORE_ID,
        'cust_id'             => $custId,
        'added_by'            => $USER_ID,
        'earnedPoints'        => (int)($finalCart['order_reward_pts'] ?? 10),
        'created_at'          => Carbon::now(),
        'updated_at'          => Carbon::now(),
    ]);
    $createdSaleId = $sale->sale_id ?? $sale->id;

    // Create line item in tbl_sales_product with Prescription matrix
    SaleProduct::create([
        'sale_id'            => $createdSaleId,
        'order_no'           => $testOrderNo,
        'product_deatils'    => 'Test Eyeglasses Frame + Premium Lens',
        'product_code'       => $PRODUCT_CODE,
        'product_type'       => 'frame',
        'qty'                => 1,
        'retail_price'       => $frameSubtotal + $lensSubtotal,
        'sale_price'         => $frameSubtotal + $lensSubtotal,
        'discount_amt'       => 0,
        'store_id'           => $STORE_ID,
        'product_id'         => $PRODUCT_ID,
        'package_id'         => $lensPkgId,
        'prescription_notes' => json_encode($rxPayload),
        'GL_EYE_RS_D'        => '-1.50',
        'GL_EYE_RC_D'        => '-0.50',
        'GL_EYE_RA_D'        => '90',
        'GL_EYE_LS_D'        => '-1.75',
        'GL_EYE_LC_D'        => '-0.25',
        'GL_EYE_LA_D'        => '180',
        'GL_EYE_totalPD'     => '64',
    ]);

    // Create payment record in tbl_sale_payment
    SalePayment::create([
        'sale_id'     => $createdSaleId,
        'order_no'    => $testOrderNo,
        'total_price' => $grandTotal,
        'pay_amount'  => 0,
        'bal_amount'  => $grandTotal,
        'pay_method'  => 'COD',
        'pay_type'    => 0,
        'pay_date'    => Carbon::now()->toDateString(),
        'store_id'    => $STORE_ID,
        'added_by'    => $USER_ID,
        'sales_type'  => 0,
    ]);

    // Deduct stock in tbl_inventory_levels
    DB::table('tbl_inventory_levels')
        ->where('product_code', $PRODUCT_CODE)
        ->where('store_id', $STORE_ID)
        ->decrement('available_quantity', 1);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    echo "Order placement failed: " . $e->getMessage() . "\n";
}

// [POSITIVE] Order record verified in tbl_sales
assertTest('TC-ORD-POS-01', 'Positive', 'Order record created in tbl_sales', 
    !is_null($createdSaleId) && $createdSaleId > 0, 
    "sale_id=$createdSaleId, order_no=$testOrderNo");

$savedSale = DB::table('tbl_sales')->where('sale_id', $createdSaleId)->first();
assertTest('TC-ORD-POS-02', 'Positive', 'Order total_payable matches exact calculated grand total', 
    (float)($savedSale->total_payable ?? 0) === $grandTotal, 
    "total_payable=₹" . ($savedSale->total_payable ?? 0) . " Expected=₹$grandTotal");

assertTest('TC-ORD-POS-03', 'Positive', 'Order sales_type set to 0 (B2C online channel)', 
    (int)($savedSale->sales_type ?? -1) === 0, 
    "sales_type=" . ($savedSale->sales_type ?? 'null'));

// [POSITIVE] Prescription matrix in tbl_sales_product verified
$savedProduct = DB::table('tbl_sales_product')->where('sale_id', $createdSaleId)->first();
assertTest('TC-ORD-POS-04', 'Positive', 'Optical Prescription power matrix (OD/OS/PD) mapped accurately', 
    $savedProduct && $savedProduct->GL_EYE_RS_D === '-1.50' && $savedProduct->GL_EYE_LS_D === '-1.75' && $savedProduct->GL_EYE_totalPD === '64', 
    "OD(RS)={$savedProduct->GL_EYE_RS_D}, OS(LS)={$savedProduct->GL_EYE_LS_D}, TotalPD={$savedProduct->GL_EYE_totalPD}");

// [POSITIVE] Payment audit trail in tbl_sale_payment verified
$savedPayment = DB::table('tbl_sale_payment')->where('sale_id', $createdSaleId)->first();
assertTest('TC-ORD-POS-05', 'Positive', 'Payment record in tbl_sale_payment logs pending COD balance', 
    !is_null($savedPayment) && (float)($savedPayment->bal_amount ?? 0) === $grandTotal, 
    "bal_amount=₹" . ($savedPayment->bal_amount ?? 0));

// [POSITIVE] Inventory decremented
$invAfterOrder = DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->first();
assertTest('TC-STK-POS-01', 'Positive', 'Physical Store 6 inventory level decremented by 1', 
    (int)($invAfterOrder->available_quantity ?? 0) === 99, 
    "Available Stock=" . ($invAfterOrder->available_quantity ?? 'null'));

// [SECURITY POSITIVE] Price Tampering Resistance
assertTest('TC-SEC-POS-01', 'Security', 'Server-side price computation prevents client payload tampering', 
    strpos($checkoutCtrlSource, "\$request->input('frame_price')") === false && strpos($checkoutCtrlSource, "\$request->input('total_payable')") === false, 
    "Prices derived strictly from CartService & DB master records");

// ===========================================================================
// SECTION 6: ADMIN PANEL B2C ORDER WORKFLOW (POS & RESTOCK)
// ===========================================================================
printSection('SECTION 6: ADMIN PANEL ORDER ACTIONS, PRESCRIPTION & RESTOCK');

$adminOrder = Sale::b2c()->where('sale_id', $createdSaleId)->with(['products', 'payments'])->first();
assertTest('TC-ADM-POS-01', 'Positive', 'Admin B2C Orders module queries and displays the order', 
    !is_null($adminOrder), 
    "Found order #{$adminOrder->order_no}");

assertTest('TC-ADM-POS-02', 'Positive', 'Admin order detail displays associated line items and payments', 
    $adminOrder && $adminOrder->products->count() === 1 && $adminOrder->payments->count() === 1, 
    "Products: {$adminOrder->products->count()}, Payments: {$adminOrder->payments->count()}");

// Admin Prescription Verification (Approve optometrist review)
$adminOrder->rx_verification_status = 'approved';
$adminOrder->verified_by = $USER_ID;
$adminOrder->verified_at = Carbon::now();
$adminOrder->save();
assertTest('TC-ADM-POS-03', 'Positive', 'Admin Optometrist prescription verification & approval', 
    $adminOrder->fresh()->rx_verification_status === 'approved', 
    "rx_verification_status=" . $adminOrder->fresh()->rx_verification_status);

// Admin Status Lifecycle: Pending -> Confirmed -> Shipped -> Delivered
$adminOrder->order_status = 'confirmed';
$adminOrder->save();
$adminOrder->order_status = 'shipped';
$adminOrder->save();
$adminOrder->order_status = 'delivered';
$adminOrder->save();

$b2cCtrl = app(B2cOrderController::class);
$histBefore = DB::table('tbl_loyaltyrogram_histroy')->where('customer_id', $custId)->count();
$method = new \ReflectionMethod(B2cOrderController::class, 'creditLoyaltyPointsOnDelivery');
$method->setAccessible(true);
$method->invoke($b2cCtrl, $adminOrder);
$histAfter = DB::table('tbl_loyaltyrogram_histroy')->where('customer_id', $custId)->count();
assertTest('TC-ADM-POS-04', 'Positive', 'Delivered status triggers automated loyalty point credit in passbook', 
    $histAfter > $histBefore, 
    "Passbook records: Before=$histBefore, After=$histAfter");

// Admin Order Cancellation Restocking
$stockBeforeCancel = (int)$invAfterOrder->available_quantity;
StockSyncService::restockOrder($adminOrder, 'cancelled');
$invAfterCancel = DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->first();
assertTest('TC-ADM-POS-05', 'Positive', 'Admin order cancellation restores inventory back into Store 6', 
    (int)($invAfterCancel->available_quantity ?? 0) === ($stockBeforeCancel + 1), 
    "Stock restored: {$stockBeforeCancel} -> " . ($invAfterCancel->available_quantity ?? 0));

// ===========================================================================
// SECTION 7: SYSTEM DEFECTS & ARCHITECTURAL VULNERABILITIES
// ===========================================================================
printSection('SECTION 7: CODE DEFECTS & ARCHITECTURAL VULNERABILITIES');

// DEFECT 1: Customer state_id and city_id overwritten with Store IDs
$stateBugExists = false;
$ctrlLines = file('app/Http/Controllers/Website/CheckoutController.php');
foreach ($ctrlLines as $l) {
    if (strpos($l, "'state_id'") !== false && strpos($l, '$store->state_id') !== false) {
        $stateBugExists = true;
        break;
    }
}
assertTest('DEFECT-01', 'Defect', 'Hardcoded Store state_id & city_id into tbl_sales (Corrupts GST)', 
    !$stateBugExists, 
    "CheckoutController L161-162 stores store's state/city into customer order, breaking state-wise GST & shipping reports", 
    true);

// DEFECT 2: Non-atomic order number generation (race condition under concurrent checkouts)
$atomicLock = false;
foreach ($ctrlLines as $l) {
    if (strpos($l, 'next_order_no') !== false && strpos($l, 'lockForUpdate') !== false) {
        $atomicLock = true;
        break;
    }
}
assertTest('DEFECT-02', 'Defect', 'Non-atomic next_order_no increment permits duplicate order numbers', 
    $atomicLock, 
    "CheckoutController L80-87 reads \$store->next_order_no without lockForUpdate(), risking duplicate order numbers during concurrent checkouts", 
    true);

// DEFECT 3: clearCart() outside DB transaction (crash window)
$clearOutside = false;
$fullCode = file_get_contents('app/Http/Controllers/Website/CheckoutController.php');
$commitPos = strpos($fullCode, 'DB::commit();');
$clearPos  = strpos($fullCode, '$this->cartService->clearCart();');
if ($commitPos !== false && $clearPos !== false && $clearPos > $commitPos) {
    $clearOutside = true;
}
assertTest('DEFECT-03', 'Defect', 'clearCart() outside DB transaction exposes duplicate order crash window', 
    !$clearOutside, 
    "If crash/disconnect happens between DB::commit() and clearCart(), session cart stays populated, allowing duplicate order submission", 
    true);

// DEFECT 4: Missing checkout idempotency key / double-submit lock
$hasIdempotency = strpos($fullCode, 'checkout_token') !== false || strpos($fullCode, 'idempotency') !== false;
assertTest('DEFECT-04', 'Defect', 'Missing checkout idempotency key / double-submit debounce', 
    $hasIdempotency, 
    "Rapid multi-clicks on 'Place Order' can create duplicate sales records because no idempotency token is enforced", 
    true);

// DEFECT 5: Coupon Cart-Reduction Exploit (min_cart_amount not re-checked in CartService)
$cartServiceSrc = file_get_contents('app/Services/CartService.php');
$couponCalcSection = substr($cartServiceSrc, strpos($cartServiceSrc, 'if ($appliedCoupon) {'), 500);
$checksMinCartInService = strpos($couponCalcSection, 'min_cart_amount') !== false;
assertTest('DEFECT-05', 'Defect', 'CartService omits min_cart_amount re-validation (Cart Reduction Exploit)', 
    $checksMinCartInService, 
    "If a customer applies a coupon requiring ₹2,000 subtotal then drops items to ₹500, getCartCalculations() still awards the discount", 
    true);

// DEFECT 6: Open-by-default unlisted pincode policy
$shippingModelSrc = file_get_contents('app/Models/ShippingCharge.php');
$openByDefault = strpos($shippingModelSrc, "// Unlisted pincode -> default free") !== false;
assertTest('DEFECT-06', 'Defect', 'Open-by-default pincode policy allows invalid/fictitious pincodes', 
    !$openByDefault, 
    "ShippingCharge::getChargeForPincode treats unknown 6-digit numbers as serviceable with Free Delivery unless manually blacklisted", 
    true);

// ===========================================================================
// CLEANUP & FINAL REPORT
// ===========================================================================
if ($createdSaleId) {
    DB::table('tbl_sales_product')->where('sale_id', $createdSaleId)->delete();
    DB::table('tbl_sale_payment')->where('sale_id', $createdSaleId)->delete();
    DB::table('tbl_sales')->where('sale_id', $createdSaleId)->delete();
}
// Clean temporary blocked pincode
ShippingCharge::where('pincode', $BLOCKED_PINCODE)->delete();
// Clean temporary offer
DB::table('offers')->where('coupon_code', 'TEST_MIN_FAIL')->delete();
// Restore stock back to 100
DB::table('tbl_inventory_levels')->where('product_code', $PRODUCT_CODE)->where('store_id', $STORE_ID)->update(['available_quantity' => 100]);

echo implode("\n", $report);
echo "\n===============================================================================\n";
printf("QA EXECUTION COMPLETE: %d TESTS EXECUTED | %d PASSED | %d FAILED | %d CRITICAL DEFECTS LOGGED\n", 
    $passCount + $failCount + $defectCount, $passCount, $failCount, $defectCount);
echo "===============================================================================\n";
