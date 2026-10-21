<?php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\B2cOrderController;
use App\Services\CartService;
use App\Models\User;
use Carbon\Carbon;

class BrandLoyaltyQATester
{
    private $passed = 0;
    private $failed = 0;
    private $total = 0;
    private $createdBrandIds = [];
    private $createdSalesIds = [];
    private $createdHistoryIds = [];
    private $testCustomerId = null;

    public function run()
    {
        echo "====================================================================\n";
        echo "   SPECKART QA SUITE: BRAND-LEVEL LOYALTY POINTS (SSOT ARCHITECTURE)\n";
        echo "   Role: Senior QA Automation Engineer\n";
        echo "   Date: " . date('Y-m-d H:i:s') . "\n";
        echo "====================================================================\n\n";

        try {
            $this->testSuite1_DatabaseSchema();
            $this->testSuite2_ValidationAndNegativeCases();
            $this->testSuite3_AdminCrudAndDataTables();
            $this->testSuite4_CartCalculationEngine();
            $this->testSuite5_OrderPlacementAndDeliveryLifecycle();
        } catch (\Throwable $e) {
            echo "\n [FATAL EXCEPTION]: " . $e->getMessage() . "\n";
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

    // ─────────────────────────────────────────────────────────────
    // SUITE 1: DATABASE SCHEMA INTEGRITY (SSOT)
    // ─────────────────────────────────────────────────────────────
    private function testSuite1_DatabaseSchema()
    {
        echo "--- SUITE 1: DATABASE SCHEMA INTEGRITY ---\n";

        $hasEligible = Schema::hasColumn('tbl_brand', 'loyalty_eligible');
        $this->assertCondition("Column 'loyalty_eligible' exists in tbl_brand", $hasEligible);

        $hasPercent = Schema::hasColumn('tbl_brand', 'loyalty_percent');
        $this->assertCondition(
            "Column 'loyalty_percent' does not exist in tbl_brand (SSOT enforced)", 
            !$hasPercent,
            "Loyalty rate is strictly controlled by Loyalty Program (/admin/loyalty-program)"
        );

        // Check column types via information_schema
        $colInfo = DB::select("SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, IS_NULLABLE 
                               FROM INFORMATION_SCHEMA.COLUMNS 
                               WHERE TABLE_NAME = 'tbl_brand' AND COLUMN_NAME = 'loyalty_eligible'");
        
        $col = $colInfo[0] ?? null;
        $eligibleDef = $col->COLUMN_DEFAULT ?? null;
        $this->assertCondition(
            "Default value of 'loyalty_eligible' is 1 (Eligible by default for all brands)", 
            $eligibleDef == '1' || $eligibleDef == "'1'", 
            "Found default: " . $eligibleDef
        );
        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // SUITE 2: VALIDATION & NEGATIVE TEST CASES
    // ─────────────────────────────────────────────────────────────
    private function testSuite2_ValidationAndNegativeCases()
    {
        echo "--- SUITE 2: VALIDATION & NEGATIVE TEST CASES ---\n";
        $controller = new MasterController();
        auth()->login(User::first() ?? new User(['id' => 1]));

        // Negative 1: Invalid loyalty_eligible value (99)
        $req99 = new Request([
            'brand_name'       => 'QA_NEG_BRAND_' . rand(1000, 9999),
            'loyalty_eligible' => '99',
        ]);
        $res99 = $controller->brandstored($req99);
        $this->assertCondition(
            "Reject invalid loyalty_eligible value (99)", 
            $res99->getStatusCode() === 422,
            "Response HTTP: " . $res99->getStatusCode()
        );

        // Negative 2: Invalid string value ('invalid')
        $reqStr = new Request([
            'brand_name'       => 'QA_NEG_BRAND_' . rand(1000, 9999),
            'loyalty_eligible' => 'invalid_str',
        ]);
        $resStr = $controller->brandstored($reqStr);
        $this->assertCondition(
            "Reject non-boolean string loyalty_eligible ('invalid_str')", 
            $resStr->getStatusCode() === 422,
            "Response HTTP: " . $resStr->getStatusCode()
        );

        // Positive 1: Missing loyalty_eligible defaults gracefully to 1
        $reqDef = new Request([
            'brand_name' => 'QA_BRAND_DEF_' . rand(1000, 9999),
        ]);
        $resDef = $controller->brandstored($reqDef);
        $this->assertCondition(
            "Omitted loyalty_eligible accepted and defaults to 1", 
            $resDef->getStatusCode() === 200,
            "Response HTTP: " . $resDef->getStatusCode()
        );
        $brandDef = DB::table('tbl_brand')->where('brand_name', $reqDef->brand_name)->first();
        if ($brandDef) {
            $this->createdBrandIds[] = $brandDef->brand_id;
            $this->assertCondition(
                "Brand created with default loyalty_eligible = 1", 
                (int)$brandDef->loyalty_eligible === 1
            );
        }
        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // SUITE 3: ADMIN CRUD & DATATABLES RENDERING
    // ─────────────────────────────────────────────────────────────
    private function testSuite3_AdminCrudAndDataTables()
    {
        echo "--- SUITE 3: ADMIN CRUD & DATATABLES RENDERING ---\n";
        $controller = new MasterController();
        auth()->login(User::first() ?? new User(['id' => 1]));

        // Test 3.1: Create Brand with Eligible (loyalty_eligible = 1)
        $brandNameA = 'QA_BRAND_ELIG_' . time();
        $resA = $controller->brandstored(new Request([
            'brand_name'       => $brandNameA,
            'loyalty_eligible' => '1',
        ]));
        $brandA = DB::table('tbl_brand')->where('brand_name', $brandNameA)->first();
        if ($brandA) $this->createdBrandIds[] = $brandA->brand_id;
        $this->assertCondition(
            "Create Brand with loyalty_eligible = 1 (Eligible)", 
            $brandA && (int)$brandA->loyalty_eligible === 1
        );

        // Test 3.2: Create Brand with Ineligible (loyalty_eligible = 0)
        $brandNameB = 'QA_BRAND_INELIG_' . time();
        $resB = $controller->brandstored(new Request([
            'brand_name'       => $brandNameB,
            'loyalty_eligible' => '0',
        ]));
        $brandB = DB::table('tbl_brand')->where('brand_name', $brandNameB)->first();
        if ($brandB) $this->createdBrandIds[] = $brandB->brand_id;
        $this->assertCondition(
            "Create Brand with loyalty_eligible = 0 (Ineligible)", 
            $brandB && (int)$brandB->loyalty_eligible === 0
        );

        // Test 3.3: DataTables Render Check for both brands (Toggle Switch HTML)
        $dtReq = new Request([
            'draw'   => 1,
            'start'  => 0,
            'length' => 10,
            'search1'=> 'QA_BRAND_',
        ]);
        $dtData = $controller->brandDatatable($dtReq)->getData(true)['data'] ?? [];
        $indexedData = collect($dtData)->keyBy('brand_name');

        $rowA = $indexedData[$brandNameA] ?? [];
        $this->assertCondition(
            "Brand A renders interactive toggle button (checked + 'Eligible')", 
            isset($rowA['loyalty_badge']) && str_contains($rowA['loyalty_badge'], 'toggle-switch') && str_contains($rowA['loyalty_badge'], 'checked') && str_contains($rowA['loyalty_badge'], 'Eligible'),
            "Badge HTML: " . ($rowA['loyalty_badge'] ?? 'NOT FOUND')
        );

        $rowB = $indexedData[$brandNameB] ?? [];
        $this->assertCondition(
            "Brand B renders interactive toggle button (unchecked + 'Ineligible')", 
            isset($rowB['loyalty_badge']) && str_contains($rowB['loyalty_badge'], 'toggle-switch') && !str_contains($rowB['loyalty_badge'], 'checked') && str_contains($rowB['loyalty_badge'], 'Ineligible'),
            "Badge HTML: " . ($rowB['loyalty_badge'] ?? 'NOT FOUND')
        );

        // Test 3.4: AJAX Inline Toggle Endpoint (Ineligible -> Eligible)
        $resToggleOn = $controller->updateLoyaltyToggle(new Request([
            'id'    => $brandB->brand_id,
            'value' => '1',
        ]));
        $brandBUpdated = DB::table('tbl_brand')->where('brand_id', $brandB->brand_id)->first();
        $this->assertCondition(
            "Inline toggle API turns Brand B to Eligible (value=1)", 
            $resToggleOn->getData()->success === true && (int)$brandBUpdated->loyalty_eligible === 1
        );

        // Test 3.5: AJAX Inline Toggle Endpoint (Eligible -> Ineligible)
        $resToggleOff = $controller->updateLoyaltyToggle(new Request([
            'id'    => $brandB->brand_id,
            'value' => '0',
        ]));
        $brandBIneligAgain = DB::table('tbl_brand')->where('brand_id', $brandB->brand_id)->first();
        $this->assertCondition(
            "Inline toggle API turns Brand B back to Ineligible (value=0)", 
            $resToggleOff->getData()->success === true && (int)$brandBIneligAgain->loyalty_eligible === 0
        );

        // Test 3.6: Reject invalid toggle value
        try {
            $controller->updateLoyaltyToggle(new Request([
                'id'    => $brandB->brand_id,
                'value' => '99',
            ]));
            $this->assertCondition("Reject invalid toggle value (99)", false);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertCondition("Reject invalid toggle value (99)", true, "ValidationException caught");
        }
        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // SUITE 4: CART CALCULATION ENGINE (EDGE CASES)
    // ─────────────────────────────────────────────────────────────
    private function testSuite4_CartCalculationEngine()
    {
        echo "--- SUITE 4: CART CALCULATION ENGINE (EDGE CASES) ---\n";
        auth()->logout(); // Clear session auth to prevent inheriting admin membership BOGO
        session()->flush();

        $cartService = app(CartService::class);
        $frame = DB::table('tbl_product_code')->first();
        $frameId = $frame ? $frame->id : 1;
        $frameCode = $frame ? $frame->product_code : 'FRM001';

        // Find existing created brands
        $brandIneligObj = DB::table('tbl_brand')->whereIn('brand_id', $this->createdBrandIds)->where('loyalty_eligible', 0)->first();
        $brandEligObj   = DB::table('tbl_brand')->whereIn('brand_id', $this->createdBrandIds)->where('loyalty_eligible', 1)->first();

        $bNameInelig = $brandIneligObj->brand_name;
        $bNameElig   = $brandEligObj->brand_name;

        // Case 4.1: Cart with ONLY Ineligible Brand (₹1,500 subtotal)
        session()->forget(['cart', 'applied_coupon', 'applied_voucher', 'use_loyalty_points']);
        session()->put('cart', [
            'item_1' => [
                'key' => 'item_1', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'F1',
                'brand' => $bNameInelig, 'frame_price' => 1500.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ]
        ]);
        $c1 = $cartService->getCartCalculations();
        $this->assertCondition(
            "Cart with 100% Ineligible brand earns exactly 0 points on ₹1,500 order", 
            (int)$c1['order_reward_pts'] === 0,
            "Earned: {$c1['order_reward_pts']} pts (Expected: 0)"
        );

        // Case 4.2: Cart with ONLY Eligible Brand (standard rate on ₹1,500)
        session()->put('cart', [
            'item_1' => [
                'key' => 'item_1', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'F1',
                'brand' => $bNameElig, 'frame_price' => 1500.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ]
        ]);
        $c2 = $cartService->getCartCalculations();
        $expectedC2 = (int)round((1500 * (float)$c2['cashback_percent']) / 100);
        $this->assertCondition(
            "Cart with 100% Eligible brand earns full points ({$expectedC2} pts at {$c2['cashback_percent']}%)", 
            (int)$c2['order_reward_pts'] === $expectedC2,
            "Earned: {$c2['order_reward_pts']} pts (Expected: {$expectedC2})"
        );

        // Case 4.3: Mixed Cart (Item A: Eligible Brand @ ₹1,000, Item B: Ineligible Brand @ ₹1,000)
        // Subtotal = ₹2,000.
        session()->put('cart', [
            'item_a' => [
                'key' => 'item_a', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'FA',
                'brand' => $bNameElig, 'frame_price' => 1000.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ],
            'item_b' => [
                'key' => 'item_b', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'FB',
                'brand' => $bNameInelig, 'frame_price' => 1000.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ]
        ]);
        $c3 = $cartService->getCartCalculations();
        $expectedC3 = (int)round((1000 * (float)$c3['cashback_percent']) / 100);
        $this->assertCondition(
            "Mixed Cart: Only eligible brand portion earns points ({$expectedC3} pts out of ₹2,000 total)", 
            (int)$c3['order_reward_pts'] === $expectedC3,
            "Earned: {$c3['order_reward_pts']} pts (Expected: {$expectedC3})"
        );

        // Case 4.4: Mixed Cart with asymmetrical item pricing
        // Item A (Eligible) = ₹3,000, Item B (Ineligible) = ₹1,000. Total = ₹4,000.
        // Item A is 75% of gross.
        session()->put('cart', [
            'item_a' => [
                'key' => 'item_a', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'FA',
                'brand' => $bNameElig, 'frame_price' => 3000.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ],
            'item_b' => [
                'key' => 'item_b', 'frame_id' => $frameId, 'frame_code' => $frameCode, 'frame_name' => 'FB',
                'brand' => $bNameInelig, 'frame_price' => 1000.0, 'lens_name' => 'Single Vision', 'lens_price' => 0.0,
                'quantity' => 1, 'product_type' => 'frame'
            ]
        ]);
        $c4 = $cartService->getCartCalculations();
        $expectedC4 = (int)round((3000 * (float)$c4['cashback_percent']) / 100);
        $this->assertCondition(
            "Asymmetrical Mixed Cart: Item A (₹3,000 / 75%) earns proportional points ({$expectedC4} pts)", 
            (int)$c4['order_reward_pts'] === $expectedC4,
            "Earned: {$c4['order_reward_pts']} pts (Expected: {$expectedC4})"
        );
        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // SUITE 5: FULL ORDER PLACEMENT & DELIVERY LIFECYCLE
    // ─────────────────────────────────────────────────────────────
    private function testSuite5_OrderPlacementAndDeliveryLifecycle()
    {
        echo "--- SUITE 5: FULL ORDER LIFECYCLE (PLACEMENT -> DELIVERY CREDIT) ---\n";

        // Setup mock customer in tbl_customer
        $testPhone = '99999' . rand(10000, 99999);
        $testEmail = 'qatest_' . time() . '@example.com';
        
        $this->testCustomerId = DB::table('tbl_customer')->insertGetId([
            'cust_unique_id'        => 'C' . rand(100000, 999999),
            'cust_type'             => 'B2C',
            'cust_name'             => 'QA Loyalty Tester',
            'contact_no'            => $testPhone,
            'email_id'              => $testEmail,
            'Loyalty_Points'        => 100,
            'Loyalty_Points_Redeem' => 0,
            'Loyalty_Points_Bal'    => 100,
            'is_Deleted'            => 0,
            'added_by'              => 1,
            'store_id'              => 1,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $orderNo = 'QA-ORD-' . strtoupper(substr(md5(time()), 0, 8));
        $pointsToEarn = 75; // Say cart calculated 75 reward pts due to brand eligibility

        // 5.1 Place mock order in tbl_sales
        $saleId = DB::table('tbl_sales')->insertGetId([
            'order_no'            => $orderNo,
            'sale_date'           => now()->toDateString(),
            'cust_name'           => 'QA Loyalty Tester',
            'contact_no'          => $testPhone,
            'email_id'            => $testEmail,
            'cust_address'        => 'Test Address',
            'state_id'            => 1,
            'city_id'             => 1,
            'pincode'             => '400703',
            'total_basic_amount'  => 1500,
            'total_item_price'    => 1500,
            'total_discount'      => 0,
            'total_payable'       => 1500,
            'pay_amount'          => 1500,
            'pending_amount'      => 0,
            'pay_method'          => 'ONLINE',
            'sales_type'          => 0,
            'sales_status'        => 0,
            'order_status'        => 'pending',
            'tax_rule'            => 1,
            'store_id'            => 1,
            'cust_id'             => $this->testCustomerId,
            'added_by'            => 1,
            'earnedPoints'        => $pointsToEarn,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
        $this->createdSalesIds[] = $saleId;

        $this->assertCondition(
            "Order #{$orderNo} placed with earnedPoints = {$pointsToEarn}", 
            $saleId > 0
        );

        // 5.2 Simulate order delivery trigger in B2cOrderController
        $b2cController = new B2cOrderController();
        $order = \App\Models\sale\Sale::where('sale_id', $saleId)->first();

        // Use reflection to invoke protected/private creditLoyaltyPointsOnDelivery method
        $refMethod = new \ReflectionMethod(B2cOrderController::class, 'creditLoyaltyPointsOnDelivery');
        $refMethod->setAccessible(true);
        $refMethod->invoke($b2cController, $order);

        // Verify Customer balance updated from 100 -> 175
        $updatedCustomer = DB::table('tbl_customer')->where('customer_id', $this->testCustomerId)->first();
        $this->assertCondition(
            "Customer balance incremented from 100 -> 175 on Delivery", 
            $updatedCustomer && (float)$updatedCustomer->Loyalty_Points_Bal === 175.0,
            "Balance found: " . ($updatedCustomer->Loyalty_Points_Bal ?? 'null')
        );

        // Verify Passbook history log
        $histEntry = DB::table('tbl_loyaltyrogram_histroy')
            ->where('customer_id', $this->testCustomerId)
            ->where('description', 'LIKE', '%' . $orderNo . '%')
            ->first();

        if ($histEntry) $this->createdHistoryIds[] = $histEntry->id;

        $this->assertCondition(
            "Passbook ledger record created with add_remove = 1 (Credit)", 
            $histEntry && (int)$histEntry->add_remove === 1 && (float)$histEntry->redeem === 75.0,
            "Ledger description: " . ($histEntry->description ?? 'NOT FOUND')
        );

        // 5.3 Idempotency Test: Attempting to credit again must NOT credit duplicate points
        $refMethod->invoke($b2cController, $order);
        $customerAfterDuplicate = DB::table('tbl_customer')->where('customer_id', $this->testCustomerId)->first();
        $this->assertCondition(
            "Idempotency verified: Duplicate delivery credit rejected (Balance remains 175)", 
            $customerAfterDuplicate && (float)$customerAfterDuplicate->Loyalty_Points_Bal === 175.0,
            "Balance found: " . ($customerAfterDuplicate->Loyalty_Points_Bal ?? 'null')
        );
        echo "\n";
    }

    // ─────────────────────────────────────────────────────────────
    // TEARDOWN & CLEANUP
    // ─────────────────────────────────────────────────────────────
    private function teardown()
    {
        echo "--- TEARDOWN & CLEANUP ---\n";
        if (!empty($this->createdBrandIds)) {
            $deletedBrands = DB::table('tbl_brand')->whereIn('brand_id', $this->createdBrandIds)->delete();
            echo " [INFO] Cleaned up {$deletedBrands} test brand(s).\n";
        }
        if (!empty($this->createdSalesIds)) {
            $deletedSales = DB::table('tbl_sales')->whereIn('sale_id', $this->createdSalesIds)->delete();
            echo " [INFO] Cleaned up {$deletedSales} test order(s).\n";
        }
        if (!empty($this->createdHistoryIds)) {
            $deletedHist = DB::table('tbl_loyaltyrogram_histroy')->whereIn('id', $this->createdHistoryIds)->delete();
            echo " [INFO] Cleaned up {$deletedHist} passbook ledger row(s).\n";
        }
        if ($this->testCustomerId) {
            DB::table('tbl_customer')->where('customer_id', $this->testCustomerId)->delete();
            echo " [INFO] Cleaned up test customer.\n";
        }
        session()->forget(['cart', 'applied_coupon', 'applied_voucher', 'use_loyalty_points']);
    }
}

$tester = new BrandLoyaltyQATester();
$tester->run();
