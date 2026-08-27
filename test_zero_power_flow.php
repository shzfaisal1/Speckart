<?php

require_once __DIR__ . '/../../../../../xampp/htdocs/Merge_speckart_09-07_26/vendor/autoload.php';
$app = require_once __DIR__ . '/../../../../../xampp/htdocs/Merge_speckart_09-07_26/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\product\Product;
use App\Models\LensPackage;
use App\Services\CartService;
use App\Http\Controllers\Website\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

echo "=================================================================\n";
echo "   SPECKART QA TEST SUITE: ZERO POWER & LENS PACKAGE FLOW      \n";
echo "=================================================================\n\n";

$results = [];

function runTest($testName, $closure) {
    global $results;
    try {
        $msg = $closure();
        $results[] = ['name' => $testName, 'status' => 'PASSED', 'details' => $msg];
        echo " [PASS] $testName\n";
    } catch (\Throwable $e) {
        $results[] = ['name' => $testName, 'status' => 'FAILED', 'details' => $e->getMessage()];
        echo " [FAIL] $testName: " . $e->getMessage() . "\n";
    }
}

// Find an active frame product
$testProduct = Product::where('status', 1)->first();
$frameId = $testProduct ? $testProduct->id : 1;

// Find active lens packages (both free and paid)
$freeLensPackage = LensPackage::where('is_active', 1)
    ->where(function($q) {
        $q->where('is_free_lens', 1)->orWhere('current_price', 0)->orWhere('package_type', 'free_lens');
    })
    ->first();

$paidLensPackage = LensPackage::where('is_active', 1)
    ->where('current_price', '>', 0)
    ->first();

$freePackageId = $freeLensPackage ? $freeLensPackage->id : null;
$paidPackageId = $paidLensPackage ? $paidLensPackage->id : null;

// ─────────────────────────────────────────────────────────────
// TEST 1: Zero Power with Paid Lens Package (e.g. Blue-Cut Coating)
// ─────────────────────────────────────────────────────────────
runTest("Test 1: Zero Power + Paid Lens Package Checkout (No Prescription Required)", function() use ($frameId, $paidPackageId, $paidLensPackage) {
    if (!$paidPackageId) throw new Exception("No active paid lens package found in DB.");

    Session::flush();
    $cartService = new CartService();
    $controller = new CartController($cartService);

    $req = Request::create('/cart/add', 'POST', [
        'frame_id'        => $frameId,
        'quantity'        => 1,
        'size'            => 'Medium',
        'lens_package_id' => $paidPackageId,
        'lens_type'       => 'Zero Power',
        // Note: No prescription_data or prescription_file provided
    ]);

    $res = $controller->addToCart($req);
    $data = $res->getData(true);

    if (empty($data['status']) || $data['status'] !== 'success') {
        throw new Exception("Cart addition failed: " . ($data['message'] ?? 'Unknown error'));
    }

    // Inspect Session Cart State
    $cart = session('cart', []);
    if (empty($cart)) throw new Exception("Cart session is empty after addition.");

    $cartItem = reset($cart);
    if (($cartItem['lens_type'] ?? '') !== 'Zero Power') {
        throw new Exception("Expected lens_type 'Zero Power', got: " . ($cartItem['lens_type'] ?? ''));
    }
    if ((int)($cartItem['lens_package_id'] ?? 0) !== (int)$paidPackageId) {
        throw new Exception("Lens package ID mismatch in cart session.");
    }
    if (!empty($cartItem['prescription_data'])) {
        throw new Exception("Prescription data was unexpectedly attached for Zero Power!");
    }

    $cartCalc = $cartService->getCartCalculations();
    $expectedLensPrice = (float)$paidLensPackage->current_price;
    if ((float)$cartCalc['lens_subtotal'] !== $expectedLensPrice) {
        throw new Exception("Expected lens subtotal ₹{$expectedLensPrice}, calculated ₹{$cartCalc['lens_subtotal']}");
    }

    return "Zero Power item with Paid Package '{$paidLensPackage->name}' added with exact price ₹{$expectedLensPrice} and zero Rx required.";
});

// ─────────────────────────────────────────────────────────────
// TEST 2: Zero Power with Free Lens Package (e.g. Standard Anti-Glare)
// ─────────────────────────────────────────────────────────────
runTest("Test 2: Zero Power + Free Lens Package (₹0 Lens Price)", function() use ($frameId, $freePackageId, $freeLensPackage) {
    if (!$freePackageId) throw new Exception("No active free lens package found in DB.");

    Session::flush();
    $cartService = new CartService();
    $controller = new CartController($cartService);

    $req = Request::create('/cart/add', 'POST', [
        'frame_id'        => $frameId,
        'quantity'        => 1,
        'size'            => 'Medium',
        'lens_package_id' => $freePackageId,
        'lens_type'       => 'Zero Power',
    ]);

    $res = $controller->addToCart($req);
    $data = $res->getData(true);

    if (empty($data['status']) || $data['status'] !== 'success') {
        throw new Exception("Cart addition failed: " . ($data['message'] ?? 'Unknown error'));
    }

    $cartCalc = $cartService->getCartCalculations();
    if ((float)$cartCalc['lens_subtotal'] !== 0.00) {
        throw new Exception("Expected Free lens price ₹0.00, got: ₹" . $cartCalc['lens_subtotal']);
    }

    return "Zero Power item with Free Lens Package correctly added with ₹0.00 lens fee.";
});

// ─────────────────────────────────────────────────────────────
// TEST 3: Zero Power Cart Line Item & Quantity Adjustments
// ─────────────────────────────────────────────────────────────
runTest("Test 3: Zero Power Cart Multi-Quantity & Subtotal Integrity", function() use ($frameId, $paidPackageId, $paidLensPackage) {
    if (!$paidPackageId) throw new Exception("No active paid lens package found.");

    Session::flush();
    $cartService = new CartService();
    $controller = new CartController($cartService);

    // Add 2 quantity of Zero Power glasses
    $req = Request::create('/cart/add', 'POST', [
        'frame_id'        => $frameId,
        'quantity'        => 2,
        'size'            => 'Medium',
        'lens_package_id' => $paidPackageId,
        'lens_type'       => 'Zero Power',
    ]);

    $controller->addToCart($req);
    $cartCalc = $cartService->getCartCalculations();

    $expectedTotalLens = (float)$paidLensPackage->current_price * 2;
    if ((float)$cartCalc['lens_subtotal'] !== $expectedTotalLens) {
        throw new Exception("Quantity multiplier mismatch. Expected ₹{$expectedTotalLens}, got: ₹{$cartCalc['lens_subtotal']}");
    }

    return "Cart quantity x2 correctly computed lens total ₹{$expectedTotalLens} for Zero Power.";
});

// ─────────────────────────────────────────────────────────────
// TEST 4: Powered Eyeglass Isolation (Prescription Verification)
// ─────────────────────────────────────────────────────────────
runTest("Test 4: Powered Eyeglass Flow Isolation (Prescription Correctly Attached)", function() use ($frameId, $paidPackageId) {
    Session::flush();
    $cartService = new CartService();
    $controller = new CartController($cartService);

    $mockRx = json_encode([
        'right_sph' => '-1.50',
        'left_sph'  => '-1.25',
        'type'      => 'manual'
    ]);

    $req = Request::create('/cart/add', 'POST', [
        'frame_id'          => $frameId,
        'quantity'          => 1,
        'size'              => 'Medium',
        'lens_package_id'   => $paidPackageId,
        'lens_type'         => 'Single Vision',
        'prescription_data' => $mockRx,
    ]);

    $res = $controller->addToCart($req);
    $data = $res->getData(true);

    if (empty($data['status']) || $data['status'] !== 'success') {
        throw new Exception("Powered eyeglass addition failed.");
    }

    $cart = session('cart', []);
    $cartItem = reset($cart);

    if (empty($cartItem['prescription_data'])) {
        throw new Exception("Prescription was lost for Powered Eyeglass!");
    }

    return "Powered Eyeglass correctly preserved Prescription while Zero Power bypassed it.";
});

echo "\n=================================================================\n";
echo "                      QA TEST SUMMARY REPORT                     \n";
echo "=================================================================\n";
$passedCount = count(array_filter($results, fn($r) => $r['status'] === 'PASSED'));
$totalCount  = count($results);
echo " Total Test Scenarios: $totalCount | Passed: $passedCount | Failed: " . ($totalCount - $passedCount) . "\n";
echo "=================================================================\n";
