<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\product\Product;
use App\Models\ProductType;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Product\ProductController;

echo "===============================================================\n";
echo "  COMPREHENSIVE TEST SUITE: POSITIVE & NEGATIVE TEST CASES\n";
echo "  Reading Glasses Power Number Configuration & Package Hiding\n";
echo "===============================================================\n\n";

$user = App\Models\User::first();
if ($user) {
    Auth::login($user);
}

$passed = 0;
$failed = 0;

function runTest($name, $closure) {
    global $passed, $failed;
    echo "Running: {$name}... ";
    try {
        $closure();
        echo "[PASSED]\n";
        $passed++;
    } catch (\Throwable $e) {
        echo "[FAILED]\n";
        echo "   Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
        $failed++;
    }
}

// ─────────────────────────────────────────────────────────────
// SECTION 1: POSITIVE TEST CASES (Happy Path & Expected Behaviors)
// ─────────────────────────────────────────────────────────────
echo "--- SECTION 1: POSITIVE TESTS ---\n";

runTest("POS-1: Standard Reading Glasses Creation with Positive Diopter Range", function() {
    $sku = 'TEST-POS1-' . time();
    $prod = Product::create([
        'product_id'               => rand(900000, 999999),
        'product_code'             => $sku,
        'product_name'             => 'Standard Reading Glasses',
        'product_type'             => 'Frame',
        'category_id'              => 4, // Reading Glasses
        'supported_product_types'  => [7],
        'selected_lens_packages'   => null,
        'reading_power_from'       => '+1.00',
        'reading_power_to'         => '+3.00',
        'reading_powers'           => ['+1.00', '+1.25', '+1.50', '+1.75', '+2.00', '+2.25', '+2.50', '+2.75', '+3.00'],
        'status'                   => 1,
        'is_b2c'                   => 1,
        'Purchase_Price'           => 250,
        'Retail_Price'             => 699,
    ]);

    $check = Product::where('product_code', $sku)->first();
    assert($check !== null, 'Product was not created');
    assert($check->reading_power_from === '+1.00', 'From diopter mismatch');
    assert($check->reading_power_to === '+3.00', 'To diopter mismatch');
    assert(is_array($check->reading_powers), 'Powers must be array');
    assert(count($check->reading_powers) === 9, 'Should have 9 powers');
    assert(in_array('+1.00', $check->reading_powers), '+1.00 missing');
    assert(in_array('+3.00', $check->reading_powers), '+3.00 missing');

    $check->delete();
});

runTest("POS-2: Custom Range and Subset Selection (Partial Stock Toggled Off)", function() {
    $sku = 'TEST-POS2-' . time();
    // Merchant offers +1.50 to +2.50, but +2.00 is out of stock so deselected
    $selectedPowers = ['+1.50', '+1.75', '+2.25', '+2.50'];
    $prod = Product::create([
        'product_id'               => rand(900000, 999999),
        'product_code'             => $sku,
        'product_name'             => 'Partial Stock Reading Glasses',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'supported_product_types'  => [7],
        'reading_power_from'       => '+1.50',
        'reading_power_to'         => '+2.50',
        'reading_powers'           => $selectedPowers,
        'status'                   => 1,
        'is_b2c'                   => 1,
    ]);

    $check = Product::where('product_code', $sku)->first();
    assert(count($check->reading_powers) === 4, 'Must have exactly 4 powers');
    assert(!in_array('+2.00', $check->reading_powers), '+2.00 should be absent');
    assert(in_array('+1.50', $check->reading_powers), '+1.50 must be present');
    assert(in_array('+2.50', $check->reading_powers), '+2.50 must be present');

    $check->delete();
});

runTest("POS-3: Store Controller Automatically Assigns Reading Type 7 and Clears Lens Packages", function() {
    // When category is 4 (Reading Glasses), controller store() should auto-inject supported_product_types=[7]
    // and wipe selected_lens_packages even if empty in input
    $controller = new ProductController();
    $parentSku = 'TEST-POS3-' . rand(10000, 99999);
    $req = new Request();
    $req->merge([
        'product_name'             => 'Auto Type Injected Reading Glasses',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'status'                   => '1',
        'is_b2c'                   => '1',
        'reading_power_from'       => '+1.00',
        'reading_power_to'         => '+2.00',
        'reading_powers'           => ['+1.00', '+1.25', '+1.50', '+1.75', '+2.00'],
        'variants'                 => [
            [
                'product_code'    => $parentSku,
                'Color'           => '#000000',
                'Retail_Price'    => 499,
                'Purchase_Price'  => 200,
            ]
        ]
    ]);

    $response = $controller->store($req);
    $saved = Product::where('product_code', $parentSku)->first();
    assert($saved !== null, 'Product was not saved by controller store()');
    assert(in_array(7, (array)$saved->supported_product_types), 'Product type 7 was not auto-assigned');
    assert(empty($saved->selected_lens_packages), 'selected_lens_packages must be null or empty for reading glasses');
    assert(count($saved->reading_powers) === 5, 'Should have 5 reading powers');

    $saved->delete();
});

runTest("POS-4: Non-Reading Category (Eyeglasses) Preserves Normal Lens Packages", function() {
    // Eyeglasses category (ID 9) must NOT have its lens packages cleared!
    $controller = new ProductController();
    $sku = 'TEST-POS4-' . rand(10000, 99999);
    $req = new Request();
    $req->merge([
        'product_name'             => 'Standard Eyeglass Frame',
        'product_type'             => 'Frame',
        'category_id'              => 9, // Eyeglasses
        'supported_product_types'  => [5], // Powered Eyeglass
        'selected_lens_packages'   => [1, 2],
        'status'                   => '1',
        'is_b2c'                   => '1',
        'variants'                 => [
            [
                'product_code'    => $sku,
                'Color'           => '#111111',
                'Retail_Price'    => 1299,
                'Purchase_Price'  => 500,
            ]
        ]
    ]);

    $response = $controller->store($req);
    $saved = Product::where('product_code', $sku)->first();
    assert($saved !== null, 'Eyeglass product was not saved');
    assert(in_array(1, (array)$saved->selected_lens_packages), 'Eyeglass package 1 must be preserved');
    assert(in_array(2, (array)$saved->selected_lens_packages), 'Eyeglass package 2 must be preserved');

    $saved->delete();
});

runTest("POS-5: Frontend PDP Details Page Renders Custom Reading Powers Correctly", function() {
    $sku = 'TEST-POS5-' . time();
    $prod = Product::create([
        'product_id'               => rand(900000, 999999),
        'product_code'             => $sku,
        'product_name'             => 'PDP Test Reading Glasses',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'supported_product_types'  => [7],
        'reading_power_from'       => '+1.25',
        'reading_power_to'         => '+2.25',
        'reading_powers'           => ['+1.25', '+1.50', '+1.75', '+2.00', '+2.25'],
        'status'                   => 1,
        'is_b2c'                   => 1,
        'Purchase_Price'           => 200,
        'Retail_Price'             => 499,
    ]);

    $html = view('website.products.details', [
        'product' => $prod,
        'categoryName' => 'Reading Glasses',
        'product_images' => [],
        'galleryImages' => [],
        'colorVariants' => collect([$prod]),
        'productRatings' => collect([]),
        'averageRating' => 5,
        'ratingCount' => 1,
        'relatedProducts' => collect([]),
    ])->render();

    assert(str_contains($html, '+1.25'), 'PDP must render +1.25 chip');
    assert(str_contains($html, '+1.75'), 'PDP must render +1.75 chip');
    assert(str_contains($html, '+2.25'), 'PDP must render +2.25 chip');
    // +3.00 was not in the configured powers for this product, so must not appear as chip
    assert(!str_contains($html, '<button type="button" class="power-chip">+3.00</button>'), '+3.00 must not be rendered when not configured');

    $prod->delete();
});

echo "\n";

// ─────────────────────────────────────────────────────────────
// SECTION 2: NEGATIVE TEST CASES (Edge Cases, Sanitization & Tampering)
// ─────────────────────────────────────────────────────────────
echo "--- SECTION 2: NEGATIVE TESTS ---\n";

runTest("NEG-1: Malicious / Stale Lens Package Injection on Reading Glasses is Wiped", function() {
    // If a request for category 4 submits rogue selected_lens_packages = [99, 100], controller MUST wipe it!
    $controller = new ProductController();
    $sku = 'TEST-NEG1-' . rand(10000, 99999);
    $req = new Request();
    $req->merge([
        'product_name'             => 'Tampered Reading Glasses',
        'product_type'             => 'Frame',
        'category_id'              => 4, // Reading Glasses
        'supported_product_types'  => [7],
        'selected_lens_packages'   => [99, 100, 101], // Malicious rogue packages submitted
        'reading_power_from'       => '+1.00',
        'reading_power_to'         => '+2.00',
        'reading_powers'           => ['+1.00', '+1.25', '+1.50', '+1.75', '+2.00'],
        'variants'                 => [
            [
                'product_code'    => $sku,
                'Color'           => '#000000',
                'Retail_Price'    => 399,
                'Purchase_Price'  => 150,
            ]
        ]
    ]);

    $controller->store($req);
    $saved = Product::where('product_code', $sku)->first();
    assert($saved !== null, 'Product was not saved');
    assert(empty($saved->selected_lens_packages), 'Controller failed to wipe injected lens packages on Reading Glasses!');

    $saved->delete();
});

runTest("NEG-2: Negative Diopters (-0.50, -2.00) and Zero (0.00) are Stripped by Backend", function() {
    // Reading glasses are for presbyopia only (positive magnification).
    // If someone tries to submit negative or plano diopters, backend MUST sanitize them out.
    $controller = new ProductController();
    $sku = 'TEST-NEG2-' . rand(10000, 99999);
    $req = new Request();
    $req->merge([
        'product_name'             => 'Negative Diopter Attempt',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'supported_product_types'  => [7],
        'reading_power_from'       => '+1.00',
        'reading_power_to'         => '+2.00',
        'reading_powers'           => ['-2.50', '-1.00', '0.00', '+1.00', '+1.50', '-0.25'],
        'variants'                 => [
            [
                'product_code'    => $sku,
                'Color'           => '#000000',
                'Retail_Price'    => 399,
                'Purchase_Price'  => 150,
            ]
        ]
    ]);

    $controller->store($req);
    $saved = Product::where('product_code', $sku)->first();
    assert($saved !== null, 'Product not saved');
    $powers = $saved->reading_powers;
    assert(!in_array('-2.50', $powers), '-2.50 was not filtered out');
    assert(!in_array('-1.00', $powers), '-1.00 was not filtered out');
    assert(!in_array('0.00', $powers), '0.00 was not filtered out');
    assert(!in_array('-0.25', $powers), '-0.25 was not filtered out');
    assert(in_array('+1.00', $powers), '+1.00 should remain');
    assert(in_array('+1.50', $powers), '+1.50 should remain');

    $saved->delete();
});

runTest("NEG-3: Malformed Strings and XSS / SQL Injection in Powers Array are Stripped", function() {
    $controller = new ProductController();
    $sku = 'TEST-NEG3-' . rand(10000, 99999);
    $req = new Request();
    $req->merge([
        'product_name'             => 'XSS Powers Attempt',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'supported_product_types'  => [7],
        'reading_powers'           => ['<script>alert(1)</script>', 'DROP TABLE users;', 'abc', '+1.25', '+2.50'],
        'variants'                 => [
            [
                'product_code'    => $sku,
                'Color'           => '#000000',
                'Retail_Price'    => 399,
                'Purchase_Price'  => 150,
            ]
        ]
    ]);

    $controller->store($req);
    $saved = Product::where('product_code', $sku)->first();
    assert($saved !== null, 'Product not saved');
    $powers = $saved->reading_powers;
    assert(!in_array('<script>alert(1)</script>', $powers), 'XSS payload was not filtered');
    assert(!in_array('DROP TABLE users;', $powers), 'SQL string was not filtered');
    assert(!in_array('abc', $powers), 'Non-numeric string was not filtered');
    assert(in_array('+1.25', $powers), '+1.25 should remain');
    assert(in_array('+2.50', $powers), '+2.50 should remain');

    $saved->delete();
});

runTest("NEG-4: Empty Power Selection Gracefully Falls Back to Master Default Powers on PDP", function() {
    // If a legacy reading product has reading_powers = null in DB, PDP must NOT crash or be blank!
    $sku = 'TEST-NEG4-' . time();
    $prod = Product::create([
        'product_id'               => rand(900000, 999999),
        'product_code'             => $sku,
        'product_name'             => 'Legacy Null Powers Product',
        'product_type'             => 'Frame',
        'category_id'              => 4,
        'supported_product_types'  => [7],
        'reading_powers'           => null, // Legacy null value
        'status'                   => 1,
        'is_b2c'                   => 1,
        'Purchase_Price'           => 100,
        'Retail_Price'             => 299,
    ]);

    $html = view('website.products.details', [
        'product' => $prod,
        'categoryName' => 'Reading Glasses',
        'product_images' => [],
        'galleryImages' => [],
        'colorVariants' => collect([$prod]),
        'productRatings' => collect([]),
        'averageRating' => 5,
        'ratingCount' => 1,
        'relatedProducts' => collect([]),
    ])->render();

    // Must not throw exception and should fall back to ProductType default powers (e.g. +1.00)
    assert(str_contains($html, 'power-chip'), 'PDP failed to fall back to default power chips when reading_powers is null');

    $prod->delete();
});

runTest("NEG-5: Lens Package Section Stays Hidden in View for Reading Glasses", function() {
    $categories = Category::where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
    $lensPackages = \App\Models\LensPackage::with(['categories', 'powerTypes'])->where('is_active', true)->get();
    $types = DB::table('tbl_type')->where('status', '1')->get();
    $shapes = DB::table('tbl_shape')->where('status', '1')->get();
    $materials = DB::table('tbl_material')->where('status', '1')->get();
    $colors = DB::table('tbl_color')->where('status', '1')->get();
    $sizes = DB::table('tbl_size')->where('status', '1')->get();
    $brands = DB::table('tbl_brand')->where('status', '1')->get();
    $collections = DB::table('collections')->where('is_active', true)->get();

    // Edit an existing reading glasses product
    $readingProd = new Product([
        'category_id'             => 4,
        'supported_product_types' => [7],
    ]);

    $html = view('products.b2c_product.create', [
        'type' => 'Frame',
        'setting' => null,
        'variants' => collect([$readingProd]),
        'first' => $readingProd,
        'product_id' => 1,
        'page_title' => 'Edit B2C Frame Product',
        'active' => 'product code',
        'brands' => $brands,
        'sizes' => $sizes,
        'types' => $types,
        'shapes' => $shapes,
        'materials' => $materials,
        'colors' => $colors,
        'categories' => $categories,
        'collections' => $collections,
        'lensPackages' => $lensPackages,
    ])->render();

    // Check that #package-section has style="display: none;"
    preg_match('/<div[^>]*id="package-section"[^>]*style="([^"]*)"/i', $html, $matches);
    assert(!empty($matches) && str_contains($matches[1], 'display: none'), 'Package section must have display: none for reading glasses');

    // Check that #product-types-section has style="display: none;"
    preg_match('/<div[^>]*id="product-types-section"[^>]*style="([^"]*)"/i', $html, $ptMatches);
    assert(!empty($ptMatches) && str_contains($ptMatches[1], 'display: none'), 'Product types section must have display: none for reading glasses');

    // Check that #reading-powers-section has style="display: block;"
    preg_match('/<div[^>]*id="reading-powers-section"[^>]*style="([^"]*)"/i', $html, $rpMatches);
    assert(!empty($rpMatches) && str_contains($rpMatches[1], 'display: block'), 'Reading powers section must have display: block for reading glasses');
});

runTest("NEG-6: Reading Power Range is Hidden in View for Standard Eyeglasses Frame", function() {
    $categories = Category::where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
    $lensPackages = \App\Models\LensPackage::with(['categories', 'powerTypes'])->where('is_active', true)->get();
    $types = DB::table('tbl_type')->where('status', '1')->get();
    $shapes = DB::table('tbl_shape')->where('status', '1')->get();
    $materials = DB::table('tbl_material')->where('status', '1')->get();
    $colors = DB::table('tbl_color')->where('status', '1')->get();
    $sizes = DB::table('tbl_size')->where('status', '1')->get();
    $brands = DB::table('tbl_brand')->where('status', '1')->get();
    $collections = DB::table('collections')->where('is_active', true)->get();

    // Eyeglass product with Powered Eyeglass only (ID 5)
    $eyeglassProd = new Product([
        'category_id'             => 9, // Eyeglasses
        'supported_product_types' => [5],
    ]);

    $html = view('products.b2c_product.create', [
        'type' => 'Frame',
        'setting' => null,
        'variants' => collect([$eyeglassProd]),
        'first' => $eyeglassProd,
        'product_id' => 1,
        'page_title' => 'Edit B2C Frame Product',
        'active' => 'product code',
        'brands' => $brands,
        'sizes' => $sizes,
        'types' => $types,
        'shapes' => $shapes,
        'materials' => $materials,
        'colors' => $colors,
        'categories' => $categories,
        'collections' => $collections,
        'lensPackages' => $lensPackages,
    ])->render();

    // Check that #reading-powers-section is HIDDEN
    preg_match('/<div[^>]*id="reading-powers-section"[^>]*style="([^"]*)"/i', $html, $rpMatches);
    assert(!empty($rpMatches) && str_contains($rpMatches[1], 'display: none'), 'Reading powers section must be hidden for standard Eyeglasses without reading type');

    // Check that #product-types-section is VISIBLE
    preg_match('/<div[^>]*id="product-types-section"[^>]*style="([^"]*)"/i', $html, $ptMatches);
    assert(!empty($ptMatches) && str_contains($ptMatches[1], 'display: block'), 'Product types section must be visible for Eyeglasses');
});

echo "\n===============================================================\n";
echo "  TEST SUMMARY: {$passed} PASSED, {$failed} FAILED\n";
echo "===============================================================\n";

if ($failed > 0) {
    exit(1);
}
