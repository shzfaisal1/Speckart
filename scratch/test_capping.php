<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\SettingMasterController;
use App\Services\CartService;

$results = [
    'passed' => 0,
    'failed' => 0,
    'details' => []
];

function runTest($name, $closure, &$results) {
    try {
        $closure();
        $results['passed']++;
        $results['details'][] = ['name' => $name, 'status' => 'PASS', 'message' => 'OK'];
        echo " \033[32m[PASS]\033[0m $name\n";
    } catch (\Throwable $e) {
        $results['failed']++;
        $results['details'][] = ['name' => $name, 'status' => 'FAIL', 'message' => $e->getMessage()];
        echo " \033[31m[FAIL]\033[0m $name: " . $e->getMessage() . "\n";
    }
}

echo "========================================================\n";
echo "   MEMBERSHIP CAPPING QA TEST SUITE (POSITIVE & NEGATIVE)\n";
echo "========================================================\n\n";

$controller = new SettingMasterController();

// ----------------------------------------------------
// SECTION 1: ADMIN CONTROLLER VALIDATION & PERSISTENCE
// ----------------------------------------------------
echo "--- SECTION 1: ADMIN CONTROLLER VALIDATION & PERSISTENCE ---\n";

// Positive Test 1: Valid Capping Values Creation
runTest("Positive 1: Create Membership Card with valid capping values", function() use ($controller) {
    $req = new Request([
        'card_name'                => 'QA VIP Test Card',
        'price'                    => '999',
        'validity_days'            => '365',
        'loyalty_earn_first'       => '15',
        'loyalty_earn_repeat'      => '10',
        'loyalty_use_percent'      => '40',
        'loyalty_min_order_amount' => '1200',
        'loyalty_max_discount'     => '500',
        'coupon_percent'           => '25',
        'coupon_min_order_amount'  => '2000',
        'coupon_max_discount'      => '750',
        'voucher_validity_days'    => '60',
    ]);

    $res = $controller->membershipcardAdd($req)->getData(true);
    if (!isset($res['success'])) {
        throw new Exception("Expected success response, got: " . json_encode($res));
    }

    $card = DB::table('tbl_membership_card')->where('card_name', 'QA VIP Test Card')->first();
    if (!$card) {
        throw new Exception("Card was not found in DB");
    }
    if ((float)$card->loyalty_min_order_amount != 1200.0) throw new Exception("loyalty_min_order_amount mismatch: {$card->loyalty_min_order_amount}");
    if ((float)$card->loyalty_max_discount != 500.0) throw new Exception("loyalty_max_discount mismatch: {$card->loyalty_max_discount}");
    if ((float)$card->coupon_min_order_amount != 2000.0) throw new Exception("coupon_min_order_amount mismatch: {$card->coupon_min_order_amount}");
    if ((float)$card->coupon_max_discount != 750.0) throw new Exception("coupon_max_discount mismatch: {$card->coupon_max_discount}");
}, $results);

// Positive Test 2: Update Existing Card Capping Values
runTest("Positive 2: Update Membership Card capping values", function() use ($controller) {
    $card = DB::table('tbl_membership_card')->where('card_name', 'QA VIP Test Card')->first();
    if (!$card) throw new Exception("Pre-requisite card not found");

    $req = new Request([
        'card_id'                  => $card->card_id,
        'card_name'                => 'QA VIP Test Card (Updated)',
        'price'                    => '1299',
        'validity_days'            => '730',
        'loyalty_earn_first'       => '20',
        'loyalty_earn_repeat'      => '10',
        'loyalty_use_percent'      => '50',
        'loyalty_min_order_amount' => '1500',
        'loyalty_max_discount'     => '800',
        'coupon_percent'           => '30',
        'coupon_min_order_amount'  => '2500',
        'coupon_max_discount'      => '1000',
        'voucher_validity_days'    => '90',
    ]);

    $res = $controller->membershipcardAdd($req)->getData(true);
    if (!isset($res['success'])) throw new Exception("Update failed: " . json_encode($res));

    $updated = DB::table('tbl_membership_card')->where('card_id', $card->card_id)->first();
    if ((float)$updated->loyalty_max_discount != 800.0) throw new Exception("loyalty_max_discount update failed");
    if ((float)$updated->coupon_max_discount != 1000.0) throw new Exception("coupon_max_discount update failed");
}, $results);

// Positive Test 3: Creation with Empty/Null Capping Fields (Default handling)
runTest("Positive 3: Create Card with empty capping fields (default to 0 & null)", function() use ($controller) {
    $req = new Request([
        'card_name'                => 'QA Minimal Card',
        'price'                    => '499',
        'validity_days'            => '180',
        'loyalty_earn_first'       => '5',
        'loyalty_earn_repeat'      => '5',
        'loyalty_use_percent'      => '50',
        'loyalty_min_order_amount' => '',
        'loyalty_max_discount'     => '',
        'coupon_percent'           => '10',
        'coupon_min_order_amount'  => '',
        'coupon_max_discount'      => '',
        'voucher_validity_days'    => '',
    ]);

    $res = $controller->membershipcardAdd($req)->getData(true);
    if (!isset($res['success'])) throw new Exception("Expected success: " . json_encode($res));

    $card = DB::table('tbl_membership_card')->where('card_name', 'QA Minimal Card')->first();
    if (!$card) throw new Exception("Card not found");
    if ((float)$card->loyalty_min_order_amount != 0.0) throw new Exception("Expected default 0 for loyalty_min_order_amount");
    if ($card->loyalty_max_discount !== null) throw new Exception("Expected null for loyalty_max_discount");
    if ((float)$card->coupon_min_order_amount != 0.0) throw new Exception("Expected default 0 for coupon_min_order_amount");
    if ($card->coupon_max_discount !== null) throw new Exception("Expected null for coupon_max_discount");
}, $results);

// Negative Test 4: Validation bounds for Coupon Percentage (> 100%)
runTest("Negative 4: Reject coupon_percent > 100%", function() use ($controller) {
    $req = new Request([
        'card_name'           => 'Invalid Percent Card',
        'price'               => '500',
        'validity_days'       => '100',
        'loyalty_earn_first'  => '10',
        'loyalty_earn_repeat' => '10',
        'loyalty_use_percent' => '50',
        'coupon_percent'      => '125', // Invalid
    ]);
    $res = $controller->membershipcardAdd($req)->getData(true);
    if (empty($res['error'])) throw new Exception("Expected validation error for coupon_percent > 100, got success");
}, $results);

// Negative Test 5: Validation bounds for Loyalty Use Percentage (< 0%)
runTest("Negative 5: Reject negative loyalty_use_percent", function() use ($controller) {
    $req = new Request([
        'card_name'           => 'Negative Percent Card',
        'price'               => '500',
        'validity_days'       => '100',
        'loyalty_earn_first'  => '10',
        'loyalty_earn_repeat' => '10',
        'loyalty_use_percent' => '-15', // Invalid
        'coupon_percent'      => '10',
    ]);
    $res = $controller->membershipcardAdd($req)->getData(true);
    if (empty($res['error'])) throw new Exception("Expected validation error for negative loyalty_use_percent");
}, $results);

// Negative Test 6: Validation for Negative Loyalty Min Order Amount
runTest("Negative 6: Reject negative loyalty_min_order_amount", function() use ($controller) {
    $req = new Request([
        'card_name'                => 'Negative Min Order Card',
        'price'                    => '500',
        'validity_days'            => '100',
        'loyalty_earn_first'       => '10',
        'loyalty_earn_repeat'      => '10',
        'loyalty_use_percent'      => '50',
        'loyalty_min_order_amount' => '-200', // Invalid
        'coupon_percent'           => '10',
    ]);
    $res = $controller->membershipcardAdd($req)->getData(true);
    if (empty($res['error'])) throw new Exception("Expected validation error for negative loyalty_min_order_amount");
}, $results);

// Negative Test 7: Validation for Negative Coupon Max Discount
runTest("Negative 7: Reject negative coupon_max_discount", function() use ($controller) {
    $req = new Request([
        'card_name'           => 'Negative Max Discount Card',
        'price'               => '500',
        'validity_days'       => '100',
        'loyalty_earn_first'  => '10',
        'loyalty_earn_repeat' => '10',
        'loyalty_use_percent' => '50',
        'coupon_percent'      => '10',
        'coupon_max_discount' => '-500', // Invalid
    ]);
    $res = $controller->membershipcardAdd($req)->getData(true);
    if (empty($res['error'])) throw new Exception("Expected validation error for negative coupon_max_discount");
}, $results);

// Negative Test 8: Validation for Missing Mandatory Card Name
runTest("Negative 8: Reject empty card_name", function() use ($controller) {
    $req = new Request([
        'card_name'           => '', // Missing
        'price'               => '500',
        'validity_days'       => '100',
        'loyalty_earn_first'  => '10',
        'loyalty_earn_repeat' => '10',
        'loyalty_use_percent' => '50',
        'coupon_percent'      => '10',
    ]);
    $res = $controller->membershipcardAdd($req)->getData(true);
    if (empty($res['error'])) throw new Exception("Expected validation error for missing card_name");
}, $results);


// ----------------------------------------------------
// SECTION 2: CART CALCULATION ENGINE & CAPPING ENFORCEMENT
// ----------------------------------------------------
echo "\n--- SECTION 2: CART CALCULATION & CAPPING ENFORCEMENT ---\n";

$cartService = new CartService();

// Positive Test 9: Coupon applied when cart subtotal meets/exceeds coupon_min_order_amount
runTest("Positive 9: Coupon applied when subtotal >= coupon_min_order_amount", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher', 'use_loyalty_points']);
    session()->put('cart_membership', [
        'card_id'                 => 9999,
        'card_name'               => 'Test Cap Card',
        'price'                   => 0, // 0 fee for pure product calculation
        'validity_days'           => 365,
        'coupon_percent'          => 20, // 20%
        'coupon_min_order_amount' => 1000,
        'coupon_max_discount'     => 500,
    ]);

    // Subtotal = 1500 (1500 >= 1000 min order)
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 1500,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    // 20% of 1500 = 300, which is <= 500 max cap
    if ($calc['coupon_discount'] != 300.0) {
        throw new Exception("Expected coupon_discount 300, got: {$calc['coupon_discount']}");
    }
}, $results);

// Negative Test 10: Coupon blocked when cart subtotal is less than coupon_min_order_amount
runTest("Negative 10: Coupon blocked when subtotal < coupon_min_order_amount", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher', 'use_loyalty_points']);
    session()->put('cart_membership', [
        'card_id'                 => 9999,
        'card_name'               => 'Test Cap Card',
        'price'                   => 0,
        'validity_days'           => 365,
        'coupon_percent'          => 20,
        'coupon_min_order_amount' => 1000, // min 1000 required
        'coupon_max_discount'     => 500,
    ]);

    // Subtotal = 800 (< 1000)
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 800,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    if ($calc['coupon_discount'] != 0.0) {
        throw new Exception("Expected coupon_discount 0 due to min order not met, got: {$calc['coupon_discount']}");
    }
}, $results);

// Positive Test 11: Coupon discount capped at coupon_max_discount
runTest("Positive 11: Coupon discount capped at coupon_max_discount", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher', 'use_loyalty_points']);
    session()->put('cart_membership', [
        'card_id'                 => 9999,
        'card_name'               => 'Test Cap Card',
        'price'                   => 0,
        'validity_days'           => 365,
        'coupon_percent'          => 20,
        'coupon_min_order_amount' => 1000,
        'coupon_max_discount'     => 400, // Capped at 400
    ]);

    // Subtotal = 3000 -> 20% of 3000 is 600, but max cap is 400
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 3000,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    if ($calc['coupon_discount'] != 400.0) {
        throw new Exception("Expected coupon_discount capped at 400, got: {$calc['coupon_discount']}");
    }
}, $results);

// Positive Test 12: Loyalty points redemption capped at loyalty_max_discount
runTest("Positive 12: Loyalty discount capped at loyalty_max_discount", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher']);
    session()->put('use_loyalty_points', true);
    session()->put('test_loyalty_points', 1000); // 1000 pts * ₹2 = ₹2000 available

    session()->put('cart_membership', [
        'card_id'                  => 9999,
        'card_name'                => 'Test Cap Card',
        'price'                    => 0,
        'validity_days'            => 365,
        'loyalty_use_percent'      => 50,  // 50% max usage
        'loyalty_min_order_amount' => 1000,
        'loyalty_max_discount'     => 350,  // Cap at 350
        'coupon_percent'           => 0,
    ]);

    // Subtotal = 2000. 50% = 1000. Max cap = 350.
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 2000,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    if ($calc['loyalty_discount'] != 350.0) {
        throw new Exception("Expected loyalty_discount capped at 350, got: {$calc['loyalty_discount']}");
    }
}, $results);

// Negative Test 13: Loyalty points redemption blocked when subtotal < loyalty_min_order_amount
runTest("Negative 13: Loyalty points blocked when subtotal < loyalty_min_order_amount", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher']);
    session()->put('use_loyalty_points', true);
    session()->put('test_loyalty_points', 1000);

    session()->put('cart_membership', [
        'card_id'                  => 9999,
        'card_name'                => 'Test Cap Card',
        'price'                    => 0,
        'validity_days'            => 365,
        'loyalty_use_percent'      => 50,
        'loyalty_min_order_amount' => 1500, // min 1500 required
        'loyalty_max_discount'     => 350,
        'coupon_percent'           => 0,
    ]);

    // Subtotal = 800 (< 1500)
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 800,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    if ($calc['loyalty_discount'] != 0.0) {
        throw new Exception("Expected loyalty_discount 0 due to min order requirement, got: {$calc['loyalty_discount']}");
    }
}, $results);

// Positive Test 14: Uncapped Card respects full allowance without arbitrary limits
runTest("Positive 14: Uncapped card allows full usage up to floor", function() use ($cartService) {
    session()->forget(['applied_coupon', 'applied_voucher']);
    session()->put('use_loyalty_points', true);
    // 250 points * ₹2/point = ₹500 available
    session()->put('test_loyalty_points', 250);

    session()->put('cart_membership', [
        'card_id'                  => 9999,
        'card_name'                => 'Uncapped Card',
        'price'                    => 0,
        'validity_days'            => 365,
        'loyalty_use_percent'      => 50, // 50%
        'loyalty_min_order_amount' => 0,  // No min
        'loyalty_max_discount'     => null, // No max
        'coupon_percent'           => 0,
    ]);

    // Subtotal = 2000. 50% = 1000. Available = 500.
    session()->put('cart', [
        'item1' => [
            'frame_id'       => 101,
            'frame_name'     => 'Test Frame',
            'frame_price'    => 2000,
            'lens_price'     => 0,
            'quantity'       => 1,
            'product_type'   => 'frame',
            'is_membership'  => false,
        ]
    ]);

    $calc = $cartService->getCartCalculations();
    if ($calc['loyalty_discount'] != 500.0) {
        throw new Exception("Expected full available points 500 to be redeemed, got: {$calc['loyalty_discount']}");
    }
}, $results);


// Clean up test records
DB::table('tbl_membership_card')->whereIn('card_name', [
    'QA VIP Test Card', 
    'QA VIP Test Card (Updated)', 
    'QA Minimal Card'
])->delete();

session()->flush();

echo "\n========================================================\n";
echo "                   TEST SUMMARY\n";
echo "========================================================\n";
echo "Total Tests Executed: " . ($results['passed'] + $results['failed']) . "\n";
echo "Passed: {$results['passed']}\n";
echo "Failed: {$results['failed']}\n";
echo "Status: " . ($results['failed'] === 0 ? "ALL TESTS PASSED! \xE2\x9C\x85" : "FAILURES DETECTED! \xE2\x9D\x8C") . "\n";
echo "========================================================\n";

if ($results['failed'] > 0) {
    exit(1);
}
exit(0);
