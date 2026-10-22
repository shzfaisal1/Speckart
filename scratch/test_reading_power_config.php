<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\product\Product;
use App\Models\ProductType;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

echo "=== START TESTING READING GLASSES POWER CONFIGURATION ===\n\n";

// 1. Verify Model Fillable & Casts
$product = new Product();
$fillable = $product->getFillable();
$casts = $product->getCasts();

echo "1. Checking Product model:\n";
assert(in_array('reading_power_from', $fillable), 'reading_power_from not in fillable');
assert(in_array('reading_power_to', $fillable), 'reading_power_to not in fillable');
assert(in_array('reading_powers', $fillable), 'reading_powers not in fillable');
assert(($casts['reading_powers'] ?? null) === 'array', 'reading_powers cast is not array');
echo "   [PASS] reading_power_from, reading_power_to, and reading_powers are in fillable and correctly casted to array.\n\n";

// 2. Test saving and reading on database
echo "2. Testing DB persistence on tbl_product_code:\n";
$testSku = 'TEST-READING-' . time();
$testProduct = Product::create([
    'product_id'               => rand(900000, 999999),
    'product_code'             => $testSku,
    'product_name'             => 'Test Reading Glasses Diopter Range',
    'product_type'             => 'Frame',
    'category_id'              => 4, // Reading Glasses
    'supported_product_types'  => [7], // Reading Glasses ID
    'selected_lens_packages'   => null,
    'reading_power_from'       => '+1.25',
    'reading_power_to'         => '+2.75',
    'reading_powers'           => ['+1.25', '+1.50', '+1.75', '+2.00', '+2.25', '+2.50', '+2.75'],
    'status'                   => 1,
    'is_b2c'                   => 1,
    'Purchase_Price'           => 300,
    'Retail_Price'             => 799,
]);

$fetched = Product::where('product_code', $testSku)->first();
assert($fetched !== null, 'Product was not inserted');
assert($fetched->reading_power_from === '+1.25', 'reading_power_from mismatch');
assert($fetched->reading_power_to === '+2.75', 'reading_power_to mismatch');
assert(is_array($fetched->reading_powers), 'reading_powers not an array');
assert(count($fetched->reading_powers) === 7, 'reading_powers count mismatch');
assert(in_array('+2.00', $fetched->reading_powers), '+2.00 not in reading_powers');
echo "   [PASS] Saved reading powers: " . implode(', ', $fetched->reading_powers) . "\n";
echo "   [PASS] Range: {$fetched->reading_power_from} to {$fetched->reading_power_to}\n\n";

// 3. Test View Rendering for Admin Create
echo "3. Testing Admin create.blade.php view rendering:\n";
$user = App\Models\User::first();
if ($user) {
    Auth::login($user);
}
$categories = Category::where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
$lensPackages = \App\Models\LensPackage::with(['categories', 'powerTypes'])->where('is_active', true)->get();
$types = DB::table('tbl_type')->where('status', '1')->get();
$shapes = DB::table('tbl_shape')->where('status', '1')->get();
$materials = DB::table('tbl_material')->where('status', '1')->get();
$colors = DB::table('tbl_color')->where('status', '1')->get();
$sizes = DB::table('tbl_size')->where('status', '1')->get();
$brands = DB::table('tbl_brand')->where('status', '1')->get();
$collections = DB::table('collections')->where('is_active', true)->get();

$html = view('products.b2c_product.create', [
    'type' => 'Frame',
    'setting' => null,
    'page_title' => 'Create B2C Product',
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

assert(str_contains($html, 'id="reading-powers-section"'), 'reading-powers-section not found in create view');
assert(str_contains($html, 'name="reading_power_from"'), 'reading_power_from input not found');
assert(str_contains($html, 'name="reading_power_to"'), 'reading_power_to input not found');
assert(str_contains($html, 'name="reading_powers[]"'), 'reading_powers[] checkboxes not found');
assert(str_contains($html, 'id="btn-generate-reading-powers"'), 'Generate button not found');
assert(str_contains($html, 'id="reading-auto-supported-type"'), 'reading-auto-supported-type not found');
echo "   [PASS] Admin create view renders reading power configuration inputs and interactive chips.\n\n";

// 4. Test View Rendering for PDP Details
echo "4. Testing Frontend details.blade.php view rendering with custom reading powers:\n";
// Let's test rendering details view
$pdpHtml = view('website.products.details', [
    'product' => $fetched,
    'categoryName' => 'Reading Glasses',
    'product_images' => [],
    'galleryImages' => [],
    'colorVariants' => collect([$fetched]),
    'productRatings' => collect([]),
    'averageRating' => 5,
    'ratingCount' => 1,
    'relatedProducts' => collect([]),
])->render();

assert(str_contains($pdpHtml, '+1.25'), '+1.25 not found in PDP HTML');
assert(str_contains($pdpHtml, '+2.75'), '+2.75 not found in PDP HTML');
echo "   [PASS] PDP successfully rendered configured power numbers (+1.25, +2.75) from product record.\n\n";

// Clean up test product
$fetched->delete();
echo "   [CLEANUP] Deleted test product {$testSku}.\n\n";

echo "=== ALL VERIFICATION TESTS PASSED SUCCESSFULLY! ===\n";
