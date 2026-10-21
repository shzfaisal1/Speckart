<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Services\CartService;
use App\Http\Controllers\Website\CheckoutController;
use App\Http\Controllers\Admin\B2cOrderController;
use App\Http\Controllers\Admin\MasterController;
use App\Models\sale\Sale;
use Carbon\Carbon;

class CartPurchaseLoyaltyTester
{
    private $passed = 0;
    private $failed = 0;
    private $total = 0;

    // Track test IDs for teardown
    private $createdBrandIds = [];
    private $createdProductIds = [];
    private $createdSalesIds = [];
    private $createdCustomerIds = [];
    private $createdHistoryIds = [];
    private $createdInventoryIds = [];

    public function run()
    {
        echo "====================================================================\n";
        echo "   SPECKART TEST: ADD TO CART & PURCHASE LOYALTY POINTS (BRAND ON/OFF)\n";
        echo "   Focus: Verify Brand Loyalty Toggle impacts Cart & Post-Purchase Delivery\n";
        echo "   Date: " . date('Y-m-d H:i:s') . "\n";
        echo "====================================================================\n\n";

        try {
            $fixtures = $this->setupFixtures();

            $this->testCycle1_BrandLoyaltyON($fixtures);
            $this->testCycle2_BrandLoyaltyOFF($fixtures);
            $this->testCycle3_MixedCart($fixtures);
            $this->testCycle4_DynamicToggleSwitching($fixtures);

        } catch (\Throwable $e) {
            echo "\n [FATAL EXCEPTION]: " . $e->getMessage() . "\n";
            echo $e->getFile() . ":" . $e->getLine() . "\n";
            echo $e->getTraceAsString() . "\n";
            $this->failed++;
        } finally {
            $this->teardown();
        }

        echo "\n====================================================================\n";
        echo "   TEST SUMMARY\n";
        echo "   Total Tests : {$this->total}\n";
        echo "   Passed      : {$this->passed} \n";
        echo "   Failed      : {$this->failed} " . ($this->failed > 0 ? "❌" : "") . "\n";
        echo "====================================================================\n";

        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function assertCondition($testName, $condition, $details = '')
    {
        $this->total++;
        if ($condition) {
            $this->passed++;
            echo " [PASS] {$testName}\n";
            if ($details) {
                echo "        └─> " . $details . "\n";
            }
        } else {
            $this->failed++;
            echo " [FAIL] {$testName}\n";
            if ($details) {
                echo "        └─> " . $details . "\n";
            }
        }
    }

    private function setupFixtures()
    {
        echo "--- STEP 0: SETTING UP TEST FIXTURES ---\n";

        // 1. Setup Brand A (Loyalty ON: loyalty_eligible = 1)
        $brandAId = DB::table('tbl_brand')->insertGetId([
            'brand_name'       => 'QA_Brand_ON_' . rand(100, 999),
            'status'           => '1',
            'loyalty_eligible' => 1,
            'added_by'         => 1,
            'store_id'         => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        $this->createdBrandIds[] = $brandAId;
        $brandA = DB::table('tbl_brand')->where('brand_id', $brandAId)->first();

        // 2. Setup Brand B (Loyalty OFF: loyalty_eligible = 0)
        $brandBId = DB::table('tbl_brand')->insertGetId([
            'brand_name'       => 'QA_Brand_OFF_' . rand(100, 999),
            'status'           => '1',
            'loyalty_eligible' => 0,
            'added_by'         => 1,
            'store_id'         => 1,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
        $this->createdBrandIds[] = $brandBId;
        $brandB = DB::table('tbl_brand')->where('brand_id', $brandBId)->first();

        echo " Fixture Brand ON  : '{$brandA->brand_name}' (ID: {$brandAId}, loyalty_eligible = 1)\n";
        echo " Fixture Brand OFF : '{$brandB->brand_name}' (ID: {$brandBId}, loyalty_eligible = 0)\n";

        // 3. Setup Product A (Frame for Brand A)
        $prodAId = DB::table('tbl_product_code')->insertGetId([
            'product_id'               => 990001 + rand(1, 1000),
            'product_code'             => 'QA-FRM-ON-' . rand(100, 999),
            'product_name'             => 'QA Frame for ' . $brandA->brand_name,
            'Company'                  => $brandA->brand_name,
            'Type'                     => 'Eyeglasses',
            'Retail_Price'             => 2000.0,
            'Purchase_Price'           => 2000.0,
            'Track_Inventory'          => 0,
            'Allow_Negative_Inventory' => 1,
            'status'                   => '1',
            'store_id'                 => 1,
            'stock_quantity'           => 10,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
        $this->createdProductIds[] = $prodAId;
        $prodA = DB::table('tbl_product_code')->where('id', $prodAId)->first();

        // 4. Setup Product B (Frame for Brand B)
        $prodBId = DB::table('tbl_product_code')->insertGetId([
            'product_id'               => 990002 + rand(1, 1000),
            'product_code'             => 'QA-FRM-OFF-' . rand(100, 999),
            'product_name'             => 'QA Frame for ' . $brandB->brand_name,
            'Company'                  => $brandB->brand_name,
            'Type'                     => 'Eyeglasses',
            'Retail_Price'             => 2000.0,
            'Purchase_Price'           => 2000.0,
            'Track_Inventory'          => 0,
            'Allow_Negative_Inventory' => 1,
            'status'                   => '1',
            'store_id'                 => 1,
            'stock_quantity'           => 10,
            'created_at'               => now(),
            'updated_at'               => now(),
        ]);
        $this->createdProductIds[] = $prodBId;
        $prodB = DB::table('tbl_product_code')->where('id', $prodBId)->first();

        // 4b. Setup Inventory Levels for Store 6 (Online Website)
        $invA = DB::table('tbl_inventory_levels')->insertGetId([
            'product_type'       => 'Frame',
            'product_id'         => $prodA->id,
            'product_code'       => $prodA->product_code,
            'available_quantity' => 100,
            'tota_lens_qty'      => 0,
            'store_id'           => 6,
            'status'             => 0,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
        $this->createdInventoryIds[] = $invA;

        $invB = DB::table('tbl_inventory_levels')->insertGetId([
            'product_type'       => 'Frame',
            'product_id'         => $prodB->id,
            'product_code'       => $prodB->product_code,
            'available_quantity' => 100,
            'tota_lens_qty'      => 0,
            'store_id'           => 6,
            'status'             => 0,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
        $this->createdInventoryIds[] = $invB;

        echo " Fixture Product A : '{$prodA->product_code}' (Price: ₹2,000, Brand: {$prodA->Company})\n";
        echo " Fixture Product B : '{$prodB->product_code}' (Price: ₹2,000, Brand: {$prodB->Company})\n";

        // 5. Setup Customer in tbl_customer
        $testPhone = '98' . rand(10000000, 99999999);
        $testEmail = 'qa_customer_' . rand(1000, 9999) . '@speckarttest.com';
        $customerId = DB::table('tbl_customer')->insertGetId([
            'cust_unique_id'        => 'C' . rand(100000, 999999),
            'cust_type'             => 'B2C',
            'cust_name'             => 'QA Purchase Loyalty Tester',
            'contact_no'            => $testPhone,
            'email_id'              => $testEmail,
            'Loyalty_Points'        => 100,
            'Loyalty_Points_Redeem' => 0,
            'Loyalty_Points_Bal'    => 100, // Initial balance 100 points
            'is_Deleted'            => 0,
            'added_by'              => 1,
            'store_id'              => 1,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);
        $this->createdCustomerIds[] = $customerId;
        echo " Fixture Customer  : ID {$customerId}, Phone: {$testPhone}, Initial Balance: 100 pts\n\n";

        return [
            'brandA'     => $brandA,
            'brandB'     => $brandB,
            'prodA'      => $prodA,
            'prodB'      => $prodB,
            'customerId' => $customerId,
            'testPhone'  => $testPhone,
            'testEmail'  => $testEmail,
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // TEST CYCLE 1: BRAND LOYALTY ON
    // ─────────────────────────────────────────────────────────────
    private function testCycle1_BrandLoyaltyON($fixtures)
    {
        echo "--- TEST CYCLE 1: BRAND LOYALTY ON (`loyalty_eligible = 1`) ---\n";
        auth()->logout();
        session()->flush();

        // 1. Setup session shipping address
        session()->put('checkout_shipping', [
            'full_name'    => 'QA Purchase Loyalty Tester',
            'phone'        => $fixtures['testPhone'],
            'email'        => $fixtures['testEmail'],
            'full_address' => '123 QA Test Street, Navi Mumbai',
            'pincode'      => '400703',
        ]);

        $cartService = app(CartService::class);
        $checkoutController = app(CheckoutController::class);
        $b2cController = app(B2cOrderController::class);

        // 1.1 Add to Cart
        $addRes = $cartService->addToCart($fixtures['prodA']->id, null, 1, null, 'Frame Only', 'Medium');
        $this->assertCondition(
            "1.1 Add to Cart: Successfully added Product A (Brand: {$fixtures['brandA']->brand_name})",
            ($addRes['status'] ?? false) === true,
            "Cart Message: " . ($addRes['message'] ?? 'N/A')
        );

        // 1.2 Inspect Cart Calculations
        $cartData = $cartService->getCartCalculations();
        $expectedPts = (int) round((2000.0 * (float)$cartData['cashback_percent']) / 100.0);
        $this->assertCondition(
            "1.2 Cart Calculation: Earns {$expectedPts} loyalty points on ₹2,000 cart ({$cartData['cashback_percent']}%)",
            (int)$cartData['order_reward_pts'] === $expectedPts && $expectedPts > 0,
            "Calculated order_reward_pts: {$cartData['order_reward_pts']} pts (Expected: {$expectedPts})"
        );

        // 1.3 Place Order via CheckoutController::processOrder
        $orderRes = $checkoutController->completeCheckout(new Request([
            'payment_method' => 'cod',
            'customer_note'  => 'QA Test: Brand ON Order',
        ]));

        $sale = Sale::where('contact_no', $fixtures['testPhone'])->orderByDesc('sale_id')->first();
        if (!$sale) {
            echo " completeCheckout Target URL: " . (method_exists($orderRes, 'getTargetUrl') ? $orderRes->getTargetUrl() : 'N/A') . "\n";
            echo " Session error: " . var_export(session()->get('error'), true) . "\n";
            echo " Session all: " . json_encode(session()->all()) . "\n";
        }
        if ($sale) {
            $this->createdSalesIds[] = $sale->sale_id;
        }

        $this->assertCondition(
            "1.3 Order Placement: Order {$sale->order_no} created with earnedPoints = {$expectedPts}",
            $sale && (int)$sale->earnedPoints === $expectedPts,
            "Sale ID: " . ($sale->sale_id ?? 'N/A') . ", earnedPoints in DB: " . ($sale->earnedPoints ?? 'N/A')
        );

        // 1.4 Simulate Order Delivery (Fulfillment)
        $deliveryReq = new Request([
            'order_status' => 'delivered',
            'note'         => 'QA Test: Delivered by Courier'
        ]);
        $b2cController->updateStatus($deliveryReq, $sale->sale_id);

        // Check Customer Balance (100 initial + expectedPts)
        $customerAfter = DB::table('tbl_customer')->where('customer_id', $fixtures['customerId'])->first();
        $expectedBal = 100.0 + $expectedPts;
        $this->assertCondition(
            "1.4 Post-Purchase Delivery: Customer balance successfully credited ({$expectedBal} pts)",
            $customerAfter && (float)$customerAfter->Loyalty_Points_Bal === (float)$expectedBal,
            "Opening: 100 pts -> Earned: +{$expectedPts} pts -> New Balance: " . ($customerAfter->Loyalty_Points_Bal ?? 'N/A')
        );

        // 1.5 Check Passbook History Ledger
        $historyRow = DB::table('tbl_loyaltyrogram_histroy')
            ->where('customer_id', $fixtures['customerId'])
            ->where('description', 'LIKE', '%' . $sale->order_no . '%')
            ->first();
        if ($historyRow) {
            $this->createdHistoryIds[] = $historyRow->id;
        }

        $this->assertCondition(
            "1.5 Loyalty Passbook: Transaction recorded for Order {$sale->order_no}",
            $historyRow && (int)$historyRow->redeem === $expectedPts && (int)$historyRow->add_remove === 1,
            "History Row ID: " . ($historyRow->id ?? 'N/A') . ", Description: " . ($historyRow->description ?? 'N/A')
        );

        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // TEST CYCLE 2: BRAND LOYALTY OFF
    // ─────────────────────────────────────────────────────────────
    private function testCycle2_BrandLoyaltyOFF($fixtures)
    {
        echo "--- TEST CYCLE 2: BRAND LOYALTY OFF (`loyalty_eligible = 0`) ---\n";
        auth()->logout();
        session()->flush();

        // 2. Setup session shipping address
        session()->put('checkout_shipping', [
            'full_name'    => 'QA Purchase Loyalty Tester',
            'phone'        => $fixtures['testPhone'],
            'email'        => $fixtures['testEmail'],
            'full_address' => '123 QA Test Street, Navi Mumbai',
            'pincode'      => '400703',
        ]);

        $cartService = app(CartService::class);
        $checkoutController = app(CheckoutController::class);
        $b2cController = app(B2cOrderController::class);

        // Fetch current customer balance before this purchase
        $custBefore = DB::table('tbl_customer')->where('customer_id', $fixtures['customerId'])->first();
        $balBefore = (float)($custBefore->Loyalty_Points_Bal ?? 0);

        // 2.1 Add to Cart Product B (Brand Loyalty OFF)
        $addRes = $cartService->addToCart($fixtures['prodB']->id, null, 1, null, 'Frame Only', 'Medium');
        $this->assertCondition(
            "2.1 Add to Cart: Successfully added Product B (Brand: {$fixtures['brandB']->brand_name})",
            ($addRes['status'] ?? false) === true,
            "Cart Message: " . ($addRes['message'] ?? 'N/A')
        );

        // 2.2 Inspect Cart Calculations -> Must be ZERO points
        $cartData = $cartService->getCartCalculations();
        $this->assertCondition(
            "2.2 Cart Calculation: Earns EXACTLY 0 loyalty points because Brand Loyalty is OFF",
            (int)$cartData['order_reward_pts'] === 0,
            "Calculated order_reward_pts: {$cartData['order_reward_pts']} pts (Expected: 0 pts)"
        );

        // 2.3 Place Order via CheckoutController::processOrder
        $orderRes = $checkoutController->completeCheckout(new Request([
            'payment_method' => 'cod',
            'customer_note'  => 'QA Test: Brand OFF Order',
        ]));

        $sale = Sale::where('contact_no', $fixtures['testPhone'])->orderByDesc('sale_id')->first();
        if ($sale) {
            $this->createdSalesIds[] = $sale->sale_id;
        }

        $this->assertCondition(
            "2.3 Order Placement: Order {$sale->order_no} created with earnedPoints = 0",
            $sale && (int)$sale->earnedPoints === 0,
            "Sale ID: " . ($sale->sale_id ?? 'N/A') . ", earnedPoints in DB: " . ($sale->earnedPoints ?? 'N/A')
        );

        // 2.4 Simulate Order Delivery (Fulfillment)
        $deliveryReq = new Request([
            'order_status' => 'delivered',
            'note'         => 'QA Test: Delivered by Courier'
        ]);
        $b2cController->updateStatus($deliveryReq, $sale->sale_id);

        // Check Customer Balance (Must remain exactly $balBefore)
        $customerAfter = DB::table('tbl_customer')->where('customer_id', $fixtures['customerId'])->first();
        $this->assertCondition(
            "2.4 Post-Purchase Delivery: Customer balance remains unchanged at {$balBefore} pts (+0 pts)",
            $customerAfter && (float)$customerAfter->Loyalty_Points_Bal === (float)$balBefore,
            "Previous Balance: {$balBefore} pts -> Post-Delivery Balance: " . ($customerAfter->Loyalty_Points_Bal ?? 'N/A') . " pts"
        );

        // 2.5 Check Passbook History Ledger -> Must NOT contain any row for this order
        $historyRow = DB::table('tbl_loyaltyrogram_histroy')
            ->where('customer_id', $fixtures['customerId'])
            ->where('description', 'LIKE', '%' . $sale->order_no . '%')
            ->first();

        $this->assertCondition(
            "2.5 Loyalty Passbook: No zero-point ledger entry logged for Order {$sale->order_no}",
            $historyRow === null,
            "History row found: " . ($historyRow ? "YES (Unexpected)" : "NONE (Correct)")
        );

        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // TEST CYCLE 3: MIXED CART (BRAND ON + BRAND OFF)
    // ─────────────────────────────────────────────────────────────
    private function testCycle3_MixedCart($fixtures)
    {
        echo "--- TEST CYCLE 3: MIXED CART (1x Brand ON + 1x Brand OFF) ---\n";
        auth()->logout();
        session()->flush();

        session()->put('checkout_shipping', [
            'full_name'    => 'QA Purchase Loyalty Tester',
            'phone'        => $fixtures['testPhone'],
            'email'        => $fixtures['testEmail'],
            'full_address' => '123 QA Test Street, Navi Mumbai',
            'pincode'      => '400703',
        ]);

        $cartService = app(CartService::class);
        $checkoutController = app(CheckoutController::class);
        $b2cController = app(B2cOrderController::class);

        $custBefore = DB::table('tbl_customer')->where('customer_id', $fixtures['customerId'])->first();
        $balBefore = (float)($custBefore->Loyalty_Points_Bal ?? 0);

        // 3.1 Add both Product A (Brand ON) and Product B (Brand OFF)
        $cartService->addToCart($fixtures['prodA']->id, null, 1, null, 'Frame Only', 'Medium');
        $cartService->addToCart($fixtures['prodB']->id, null, 1, null, 'Frame Only', 'Medium');

        $cartData = $cartService->getCartCalculations();
        $this->assertCondition(
            "3.1 Add to Cart: Cart contains 2 items (Total Gross: ₹4,000)",
            count($cartData['items'] ?? []) === 2,
            "Items count: " . count($cartData['items'] ?? []) . ", Grand Total: ₹" . ($cartData['grand_total'] ?? 0)
        );

        // 3.2 Points earned should ONLY be on Product A's ₹2,000 share
        // Product A = ₹2,000 (Eligible), Product B = ₹2,000 (Ineligible)
        $expectedMixedPts = (int) round((2000.0 * (float)$cartData['cashback_percent']) / 100.0);
        $this->assertCondition(
            "3.2 Cart Calculation: Mixed cart earns points ONLY for Brand ON portion ({$expectedMixedPts} pts)",
            (int)$cartData['order_reward_pts'] === $expectedMixedPts,
            "Earned: {$cartData['order_reward_pts']} pts (Expected: {$expectedMixedPts} pts from ₹2,000 eligible item, 0 pts from ₹2,000 ineligible item)"
        );

        // 3.3 Place Order
        $orderRes = $checkoutController->completeCheckout(new Request([
            'payment_method' => 'cod',
            'customer_note'  => 'QA Test: Mixed Cart Order',
        ]));

        $sale = Sale::where('contact_no', $fixtures['testPhone'])->orderByDesc('sale_id')->first();
        if ($sale) {
            $this->createdSalesIds[] = $sale->sale_id;
        }

        $this->assertCondition(
            "3.3 Order Placement: Mixed Order {$sale->order_no} created with earnedPoints = {$expectedMixedPts}",
            $sale && (int)$sale->earnedPoints === $expectedMixedPts,
            "Sale ID: " . ($sale->sale_id ?? 'N/A') . ", earnedPoints in DB: " . ($sale->earnedPoints ?? 'N/A')
        );

        // 3.4 Deliver Order
        $deliveryReq = new Request([
            'order_status' => 'delivered',
            'note'         => 'QA Test: Delivered by Courier'
        ]);
        $b2cController->updateStatus($deliveryReq, $sale->sale_id);

        $customerAfter = DB::table('tbl_customer')->where('customer_id', $fixtures['customerId'])->first();
        $expectedBal = $balBefore + $expectedMixedPts;
        $this->assertCondition(
            "3.4 Post-Purchase Delivery: Exactly {$expectedMixedPts} pts credited to customer ({$balBefore} -> {$expectedBal} pts)",
            $customerAfter && (float)$customerAfter->Loyalty_Points_Bal === (float)$expectedBal,
            "Previous Balance: {$balBefore} pts -> Post-Delivery Balance: " . ($customerAfter->Loyalty_Points_Bal ?? 'N/A') . " pts"
        );

        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // TEST CYCLE 4: DYNAMIC TOGGLE SWITCHING IN CART
    // ─────────────────────────────────────────────────────────────
    private function testCycle4_DynamicToggleSwitching($fixtures)
    {
        echo "--- TEST CYCLE 4: DYNAMIC BRAND TOGGLE SWITCHING (ON -> OFF -> ON) ---\n";
        auth()->logout();
        session()->flush();

        $cartService = app(CartService::class);
        $masterController = app(MasterController::class);

        // 4.1 Add Product A to Cart (Brand A currently ON)
        $cartService->addToCart($fixtures['prodA']->id, null, 1, null, 'Frame Only', 'Medium');
        $cBefore = $cartService->getCartCalculations();
        $this->assertCondition(
            "4.1 Initial State: Brand A in Cart earns {$cBefore['order_reward_pts']} pts",
            (int)$cBefore['order_reward_pts'] > 0
        );

        // 4.2 Admin flips Brand A toggle to OFF (0) via MasterController AJAX endpoint
        $toggleReqOff = new Request([
            'id'    => $fixtures['brandA']->brand_id,
            'value' => 0,
        ]);
        $resOff = $masterController->updateLoyaltyToggle($toggleReqOff);
        $this->assertCondition(
            "4.2 Admin toggles Brand A to OFF via AJAX API",
            $resOff->getData()->success === true && (int)$resOff->getData()->status === 0
        );

        // Re-calculate cart: Points should instantly drop to 0
        $cDuring = $cartService->getCartCalculations();
        $this->assertCondition(
            "4.3 Cart Recalculation: Points instantly drop to 0 pts when Brand toggle is switched OFF",
            (int)$cDuring['order_reward_pts'] === 0,
            "Calculated order_reward_pts: {$cDuring['order_reward_pts']} pts"
        );

        // 4.4 Admin flips Brand A toggle back to ON (1)
        $toggleReqOn = new Request([
            'id'    => $fixtures['brandA']->brand_id,
            'value' => 1,
        ]);
        $resOn = $masterController->updateLoyaltyToggle($toggleReqOn);
        $this->assertCondition(
            "4.4 Admin toggles Brand A back to ON via AJAX API",
            $resOn->getData()->success === true && (int)$resOn->getData()->status === 1
        );

        // Re-calculate cart: Points should be immediately restored!
        $cAfter = $cartService->getCartCalculations();
        $this->assertCondition(
            "4.5 Cart Recalculation: Points immediately restored to {$cAfter['order_reward_pts']} pts when Brand toggle is switched back ON",
            (int)$cAfter['order_reward_pts'] === (int)$cBefore['order_reward_pts'],
            "Calculated order_reward_pts: {$cAfter['order_reward_pts']} pts"
        );

        echo "\n";
    }

    private function teardown()
    {
        echo "--- CLEANING UP TEST FIXTURES ---\n";

        // Clean sales & sale products
        if (!empty($this->createdSalesIds)) {
            DB::table('tbl_sales_product')->whereIn('sale_id', $this->createdSalesIds)->delete();
            DB::table('tbl_sale_payment')->whereIn('sale_id', $this->createdSalesIds)->delete();
            DB::table('tbl_sales')->whereIn('sale_id', $this->createdSalesIds)->delete();
            echo " Deleted " . count($this->createdSalesIds) . " test order(s) from tbl_sales\n";
        }

        // Clean customer & history
        if (!empty($this->createdCustomerIds)) {
            DB::table('tbl_loyaltyrogram_histroy')->whereIn('customer_id', $this->createdCustomerIds)->delete();
            DB::table('tbl_customer')->whereIn('customer_id', $this->createdCustomerIds)->delete();
            echo " Deleted " . count($this->createdCustomerIds) . " test customer(s) & passbook entries\n";
        }

        // Clean products
        if (!empty($this->createdProductIds)) {
            DB::table('tbl_product_code')->whereIn('id', $this->createdProductIds)->delete();
            echo " Deleted " . count($this->createdProductIds) . " test product(s) from tbl_product_code\n";
        }

        // Clean inventory
        if (!empty($this->createdInventoryIds)) {
            DB::table('tbl_inventory_levels')->whereIn('id', $this->createdInventoryIds)->delete();
            echo " Deleted " . count($this->createdInventoryIds) . " test inventory record(s)\n";
        }

        // Clean brands
        if (!empty($this->createdBrandIds)) {
            DB::table('tbl_brand')->whereIn('brand_id', $this->createdBrandIds)->delete();
            echo " Deleted " . count($this->createdBrandIds) . " test brand(s) from tbl_brand\n";
        }

        session()->flush();
        echo " Test environment completely restored to clean state.\n";
    }
}

$tester = new CartPurchaseLoyaltyTester();
$tester->run();
