<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\product\Product;
use App\Models\Category;
use App\Models\LensPackage;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display the product catalog.
     */
    public function index(Request $request)
    {
        $page_title = 'B2C Product Catalog';
        $active     = 'product catalog';

        return view('products.b2c_product.catalog', compact('page_title', 'active'));
    }

    /**
     * Search and paginate the product catalog (B2C only — is_b2c = 1).
     */
    public function searchCatalog(Request $request)
    {
        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);

        // Only show B2C products
        $query = Product::where('is_b2c', 1);

        // Search filter
        if ($search = $request->input('search1')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('Company', 'like', "%{$search}%")
                  ->orWhere('Description', 'like', "%{$search}%")
                  ->orWhere('parent_product_code', 'like', "%{$search}%")
                  ->orWhere('product_id', 'like', "%{$search}%");
            });
        }

        $totalData     = Product::where('is_b2c', 1)->distinct()->count(DB::raw('COALESCE(NULLIF(parent_product_code, ""), product_id)'));
        $totalFiltered = (clone $query)->distinct()->count(DB::raw('COALESCE(NULLIF(parent_product_code, ""), product_id)'));

        $parentCodes = (clone $query)->select(DB::raw('COALESCE(NULLIF(parent_product_code, ""), product_id) as group_key'))
            ->distinct()
            ->offset($start)
            ->limit($limit)
            ->pluck('group_key');

        $products = Product::where('is_b2c', 1)
            ->where(function($q) use ($parentCodes) {
                $q->whereIn('parent_product_code', $parentCodes)
                  ->orWhereIn('product_id', $parentCodes);
            })
            ->get();
            
        $grouped = $products->groupBy(function($item) {
            return !empty($item->parent_product_code) ? $item->parent_product_code : $item->product_id;
        });

        $data = [];
        foreach ($parentCodes as $pkey) {
            $variants = $grouped->get($pkey);
            if (!$variants || $variants->isEmpty()) continue;

            $first    = $variants->first();
            $isActive = $variants->contains('status', '1');
            $skus     = $variants->pluck('product_code')->filter()->unique()->values()->toArray();

            $data[] = [
                'id'                  => $first->id,
                'product_id'          => $first->product_id,
                'product_code'        => $first->parent_product_code ?: $first->product_code,
                'product_name'        => $first->product_name,
                'Company'             => $first->Company ?? 'N/A',
                'product_type'        => $first->product_type,
                'parent_product_code' => $first->parent_product_code,
                'skus'                => $skus,
                'status'              => $isActive ? 1 : 0,
                'is_b2c'              => $first->is_b2c,
                'created_at'          => $first->created_at
                    ? ($first->created_at instanceof \Carbon\Carbon
                        ? $first->created_at->format('d M, Y')
                        : date('d M, Y', strtotime($first->created_at)))
                    : 'N/A',
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalData,
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ]);
    }

    /**
     * Toggle status for all variants of a product ID.
     */
    public function toggleStatus(Request $request)
    {
        $productId = $request->input('product_id');
        $value     = $request->input('value'); // 1 or 0

        Product::where('product_id', $productId)->update([
            'status' => $value ? '1' : '0',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully!',
        ]);
    }

    /**
     * Show the create form for a new multi-variant product.
     * Product type (Frame/Lens) is determined by the selected category on the client side,
     * NOT from a URL query parameter.
     */
    public function create(Request $request)
    {
        // Default to 'Frame'. The actual type is determined dynamically by the category
        // selection in the browser — the hidden input #product_type is updated by JS.
        $type    = 'Frame';
        $setting = DB::table('tbl_product_code_setting')->where('product_type', $type)->first();

        // Fetch master records
        $brands      = DB::table('tbl_brand')->where('status', '1')->get();
        $sizes       = DB::table('tbl_size')->where('status', '1')->get();
        $types       = DB::table('tbl_type')->where('status', '1')->get();
        $shapes      = DB::table('tbl_shape')->where('status', '1')->get();
        $materials   = DB::table('tbl_material')->where('status', '1')->get();
        $colors      = DB::table('tbl_color')->where('status', '1')->get();
        $categories  = Category::where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
        $collections = DB::table('collections')->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();

        $page_title = 'Create B2C Product';
        $active     = 'product code';

        $lensPackages = LensPackage::with(['categories', 'powerTypes'])->where('is_active', true)->orderBy('name')->get();

        return view('products.b2c_product.create', compact(
            'type', 'setting', 'page_title', 'active',
            'brands', 'sizes', 'types', 'shapes', 'materials', 'colors',
            'categories', 'collections', 'lensPackages'
        ));
    }

    /**
     * Get subcategories for a given category.
     */
    public function getSubcategories(Request $request)
    {
        $subcategories = DB::table('subcategories')
            ->where('category_id', $request->get('category_id'))
            ->where('is_active', true)
            ->whereNull('deleted_at')
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }

    /**
     * Check if a SKU exists (used for live AJAX validation)
     */
    public function checkSku(Request $request)
    {
        $sku = $request->input('sku');
        $excludeId = $request->input('exclude_id');
        
        if (empty($sku)) {
            return response()->json(['exists' => false]);
        }

        $query = DB::table('tbl_product_code')->where('product_code', $sku);
        if ($excludeId) {
            // Fetch parent_product_code of the excluded ID to exclude all its variants
            $product = DB::table('tbl_product_code')->where('id', $excludeId)->first();
            if ($product && !empty($product->parent_product_code)) {
                $query->where('parent_product_code', '!=', $product->parent_product_code);
            } else {
                $query->where('id', '!=', $excludeId);
            }
        }

        $exists = $query->exists();
        return response()->json(['exists' => $exists]);
    }

    /**
     * Store a new product with one or more variants.
     * parent_product_code is auto-generated here — never sent from the form.
     */
    public function storeold(Request $request)
    {
        $type     = $request->input('product_type', 'Frame');
        $variants = $request->input('variants', []);

        // AUTO-GENERATE parent_product_code — always server-side, never from form input
        $parentProductCode = $this->generateUniqueParentCode();

        // --- Global validation ---
        $globalValidator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string',
            'variants'     => 'required|array|min:1',
        ]);

        if ($globalValidator->fails()) {
            return response()->json(['error' => $globalValidator->errors()->all()]);
        }

        // --- Validate each variant's SKU uniqueness ---
        $skuErrors = [];
        foreach ($variants as $idx => $variant) {
            $sku = $variant['product_code'] ?? '';
            if (empty($sku)) {
                $skuErrors[] = "Variant #" . ($idx + 1) . ": SKU / Product Code is required.";
                continue;
            }
            if (strlen($sku) < 3) {
                $skuErrors[] = "Variant #" . ($idx + 1) . ": SKU must be at least 3 characters.";
                continue;
            }
            if (DB::table('tbl_product_code')->where('product_code', $sku)->exists()) {
                $skuErrors[] = "Variant #" . ($idx + 1) . ": SKU '{$sku}' already exists.";
            }
        }

        if (!empty($skuErrors)) {
            return response()->json(['error' => $skuErrors]);
        }

        // --- Shared upload folder for this product group ---
        $typeLower  = strtolower($type);
        $folderPath = public_path("uploads/{$typeLower}/product/{$parentProductCode}");
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // --- Shared/global fields ---
        $productName       = $request->input('product_name');
        $company           = $request->input('Company')            ?: null;
        $description       = $request->input('Description')        ?: null;
        $trackInventory    = $request->input('Track_Inventory', '1');
        $allowNegInventory = $request->input('Allow_Negative_Inventory', '1');
        $status            = $request->input('status', '1');
        $isB2c             = $request->input('is_b2c', '1');
        $categoryId        = $request->input('category_id')        ?: null;
        $subcategoryId     = $request->input('subcategory_id')     ?: null;
        $vendor            = $request->input('vendor')             ?: null;
        $tags              = $request->input('tags')               ?: null;
        $seoTitle          = $request->input('seo_title')          ?: null;
        $seoDescription    = $request->input('seo_description')    ?: null;
        $promotionTags     = $request->input('promotion_tag');
        $promotionTag      = is_array($promotionTags) ? implode(',', $promotionTags) : $promotionTags;
        $promotionTag      = $promotionTag                         ?: null;
        $specialCollection = $request->input('special_collection') ?: null;

        // --- Create each variant row ---
        $productId = null;
        foreach ($variants as $idx => $variant) {
            $productId    = $this->generateUniqueProductId();
            $sku          = $variant['product_code'];
            $colorPrimary = $variant['Color']           ?? '';
            $colorSec     = $variant['Secondary_Color'] ?? '';
            $color        = !empty($colorSec) ? "{$colorPrimary} / {$colorSec}" : ($colorPrimary ?: null);
            $size         = $variant['Size']          ?? null;
            $typeField    = $variant['Type']          ?? null;
            $gender       = $variant['Gender']        ?? null;
            $shape        = $variant['Shape']         ?? null;
            $material     = $variant['Material']      ?? null;
            $templeDetail = $variant['Temple_Detail'] ?? null;
            $bridgeSize   = $variant['Bridge_Size']   ?? null;
            $quality      = $variant['Quality']       ?? null;
            $purBase      = $this->nullableFloat($variant['Purchase_Base_Price'] ?? null);
            $purPrice     = $this->nullableFloat($variant['Purchase_Price']      ?? null);
            $retailPrice  = $this->nullableFloat($variant['Retail_Price']        ?? null);
            $bbPrice      = $this->nullableFloat($variant['BB_Price']            ?? null);

            $details = implode(' - ', array_filter([
                $productName, $company, $quality, $color, $size,
                $typeField, $gender, $shape, $material, $bridgeSize,
            ]));

            // Main image
            $mainImageName = null;
            if ($request->hasFile("variants.{$idx}.main_image")) {
                $file          = $request->file("variants.{$idx}.main_image");
                $mainImageName = time() . '_main_' . $idx . '.' . $file->getClientOriginalExtension();
                $file->move($folderPath, $mainImageName);
            }

            // Gallery images
            $galleryImages = [];
            if ($request->hasFile("variants.{$idx}.images")) {
                foreach ($request->file("variants.{$idx}.images") as $file) {
                    $imgName         = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($folderPath, $imgName);
                    $galleryImages[] = $imgName;
                }
            }

            Product::create([
                'product_id'               => $productId,
                'product_type'             => $type,
                'product_code'             => $sku,
                'product_name'             => $productName,
                'productdetails'           => $details,
                'Company'                  => $company,
                'Quality'                  => $quality,
                'Color'                    => $color,
                'Size'                     => $size,
                'Type'                     => $typeField,
                'Gender'                   => $gender,
                'Shape'                    => $shape,
                'Material'                 => $material,
                'Temple_Detail'            => $templeDetail,
                'Bridge_Size'              => $bridgeSize,
                'Description'              => $description,
                'Track_Inventory'          => $trackInventory,
                'Allow_Negative_Inventory' => $allowNegInventory,
                'Purchase_Base_Price'      => $purBase,
                'Purchase_Price'           => $purPrice,
                'Retail_Price'             => $retailPrice,
                'BB_Price'                 => $bbPrice,
                'product_image'            => json_encode($galleryImages),
                'main_image'               => $mainImageName,
                'status'                   => $status,
                'is_b2c'                   => $isB2c,
                'category_id'              => $categoryId,
                'subcategory_id'           => $subcategoryId,
                'vendor'                   => $vendor,
                'tags'                     => $tags,
                'seo_title'                => $seoTitle,
                'seo_description'          => $seoDescription,
                'promotion_tag'            => $promotionTag,
                'special_collection'       => $specialCollection,
                'parent_product_code'      => $parentProductCode, // same for all variants in this group
                'added_by'                 => auth()->id(),
                'store_id'                 => auth()->user()->store_id ?? null,
            ]);
        }

        return response()->json([
            'success'              => count($variants) . ' variant(s) created successfully.',
            'parent_product_code'  => $parentProductCode,
            'redirect'             => $this->getIndexRoute($type),
        ]);
    }

    public function store(Request $request)
    {
        // BUG FIX: old code had $type = "" (empty string) — product_type never saved correctly
        $type     = $request->input('product_type', 'Frame');
        $variants = $request->input('variants', []);

        // AUTO-GENERATE parent_product_code if not supplied by admin
        $masterCodeInput = trim($request->input('product_code_master', ''));
        if (!empty($masterCodeInput)) {
            $existingParent = DB::table('tbl_product_code')->where('parent_product_code', $masterCodeInput)->exists();
            $parentProductCode = $existingParent ? $this->generateUniqueParentCode() : $masterCodeInput;
        } else {
            $parentProductCode = $this->generateUniqueParentCode();
        }
     
        // Global validation — parent_product_code removed from rules (it's auto-generated)
        $globalValidator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|in:Frame,Lens,Sunglass,Solution,Accessory,Other,Glass',
            'variants'     => 'required|array|min:1',
        ]);

        if ($globalValidator->fails()) {
            return response()->json(['error' => $globalValidator->errors()->all()]);
        }

        // Validate each variant SKU, pricing, and check for intra-payload duplicates
        $validationErrors = [];
        $seenSkus  = [];
        foreach ($variants as $idx => $variant) {
            $variantNum = $idx + 1;
            $sku = trim($variant['product_code'] ?? '');
            if (empty($sku)) {
                $validationErrors[] = "Variant #{$variantNum}: SKU is required.";
                continue;
            }
            if (strlen($sku) < 3) {
                $validationErrors[] = "Variant #{$variantNum}: SKU must be at least 3 characters.";
                continue;
            }
            $skuLower = strtolower($sku);
            if (in_array($skuLower, $seenSkus, true)) {
                $validationErrors[] = "Variant #{$variantNum}: Duplicate SKU '{$sku}' is used multiple times in this form.";
                continue;
            }
            $seenSkus[] = $skuLower;

            if (DB::table('tbl_product_code')->where('product_code', $sku)->exists()) {
                $validationErrors[] = "Variant #{$variantNum}: SKU '{$sku}' already exists.";
            }

            // Price validation
            $mrp = isset($variant['Retail_Price']) && is_numeric($variant['Retail_Price']) ? (float)$variant['Retail_Price'] : null;
            $discount = isset($variant['discount_price']) && is_numeric($variant['discount_price']) ? (float)$variant['discount_price'] : null;

            if ($mrp !== null && $mrp < 0) {
                $validationErrors[] = "Variant #{$variantNum}: Retail Price (MRP) cannot be negative.";
            }
            if ($discount !== null && $mrp !== null && $discount > $mrp && $mrp > 0) {
                $validationErrors[] = "Variant #{$variantNum}: Discount / Sale Price (₹{$discount}) cannot exceed Retail Price (₹{$mrp}).";
            }

            // Image Aspect Ratio Validation (~2:1 Landscape Ratio)
            if ($request->hasFile("variants.{$idx}.main_image")) {
                $mainFile = $request->file("variants.{$idx}.main_image");
                $imgInfo  = @getimagesize($mainFile->getRealPath());
                if ($imgInfo) {
                    $w = $imgInfo[0];
                    $h = $imgInfo[1];
                    $r = $h > 0 ? $w / $h : 0;
                    if ($r < 1.2 || $r > 3.2) {
                        $validationErrors[] = "Variant #{$variantNum}: Main Image must be in landscape ~2:1 ratio (recommended 800x400px or 1000x500px). Uploaded image is {$w}x{$h}px (Ratio: " . round($r, 2) . ":1).";
                    }
                }
            }

            if ($request->hasFile("variants.{$idx}.images")) {
                foreach ($request->file("variants.{$idx}.images") as $gIdx => $gFile) {
                    $imgInfo = @getimagesize($gFile->getRealPath());
                    if ($imgInfo) {
                        $w = $imgInfo[0];
                        $h = $imgInfo[1];
                        $r = $h > 0 ? $w / $h : 0;
                        if ($r < 1.2 || $r > 3.2) {
                            $validationErrors[] = "Variant #{$variantNum}: Gallery Image #" . ($gIdx + 1) . " must be in landscape ~2:1 ratio (recommended 800x400px). Uploaded image is {$w}x{$h}px (Ratio: " . round($r, 2) . ":1).";
                        }
                    }
                }
            }
        }

        if (!empty($validationErrors)) {
            return response()->json(['error' => $validationErrors]);
        }

        try {
            DB::beginTransaction();

            $typeLower  = strtolower($type);
            $folderPath = public_path("uploads/{$typeLower}/product/{$parentProductCode}");
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            // Shared global fields (same for all variants in this product group)
            $productName       = $request->input('product_name');
            $company           = $request->input('Company')            ?: null;
            $description       = $request->input('Description')        ?: null;
            $trackInventory    = $request->input('Track_Inventory', '1');
            $allowNegInventory = $request->input('Allow_Negative_Inventory', '1');
            $status            = $request->input('status', '1');
            $isB2c             = $request->input('is_b2c', '1');
            $categoryId        = $request->input('category_id')        ?: null;
            $subcategoryId     = $request->input('subcategory_id')     ?: null;
            $vendor            = $request->input('vendor')             ?: null;
            $tags              = $request->input('tags')               ?: null;
            $seoTitle          = $request->input('seo_title')          ?: null;
            $seoDescription    = $request->input('seo_description')    ?: null;
            $promotionTags     = $request->input('promotion_tag');
            $promotionTag      = is_array($promotionTags) ? implode(',', $promotionTags) : $promotionTags;
            $promotionTag      = $promotionTag                         ?: null;
            $specialCollection = $request->input('special_collection') ?: null;
            
            $age               = $request->input('age');
            $occasion          = $request->input('occasion');
            $faceShape         = $request->input('face_shape');
            $gender            = $request->input('gender');
            $typeTop           = $request->input('Type');
            $shapeTop          = $request->input('Shape');
            $supportedProductTypes = $request->input('supported_product_types');
            $selectedLensPackages  = $request->input('selected_lens_packages');

            $createdCount = 0;

            foreach ($variants as $idx => $variant) {
                // Each variant gets its own unique product_id
                // but shares the same parent_product_code with siblings
                $productId    = $this->generateUniqueProductId();
                $sku          = trim($variant['product_code']);

                $colorPrimary = $variant['Color']           ?? '';
                $colorSec     = $variant['Secondary_Color'] ?? '';
                $color        = !empty($colorSec)
                    ? trim($colorPrimary) . ' / ' . trim($colorSec)
                    : ($colorPrimary ?: null);

                $size         = $variant['Size']          ?? null;
                if (is_array($size)) {
                    $size = implode(',', $size);
                }
                $typeField    = $typeTop;
                $shape        = $shapeTop;
                $material     = $variant['Material']      ?? null;
                $templeDetail = $variant['Temple_Detail'] ?? null;
                $bridgeSize   = $variant['Bridge_Size']   ?? null;
                $quality      = $variant['Quality']       ?? null;
                
                $lensWidth     = $variant['lens_width']    ?? null;
                $templeLength  = $variant['temple_length'] ?? null;
                $frameWidth    = $variant['frame_width']   ?? null;
                $stockQty      = $variant['stock_quantity']?? 0;
                $stockStatus   = $variant['stock_status']  ?? null;
                $polarized     = $variant['polarized']     ?? 0;
                $uvProtection  = $variant['uv_protection'] ?? null;
                $barcode       = $variant['barcode']       ?? null;
                $discountPrice = $this->nullableFloat($variant['discount_price'] ?? null);
                $taxHsnCode    = $variant['tax_hsn_code']  ?? null;
                $variantStatus = $variant['status']        ?? $status; // Fallback to global status if not set

                // Lens-specific fields (with resilient aliases)
                $modality      = $variant['Modality']      ?? $variant['modality']      ?? null;
                $wc            = $variant['WC']            ?? $variant['wc']            ?? null;
                $dk_t          = $variant['Dk_t']          ?? $variant['dk_t']          ?? null;
                $bc            = $variant['BC']            ?? $variant['base_curve']    ?? $variant['base_carve'] ?? null;
                $dia           = $variant['DIA']           ?? $variant['diameter']      ?? null;
                $sph           = $variant['SPH']           ?? $variant['sph_range']     ?? null;
                $cyl           = $variant['CYL']           ?? $variant['cyl_range']     ?? null;
                $axis          = $variant['AXIS']          ?? $variant['axis_range']    ?? null;

                // nullableFloat prevents SQLSTATE[22007] on empty price fields
                $purBase     = $this->nullableFloat($variant['Purchase_Base_Price'] ?? null);
                $purPrice    = $this->nullableFloat($variant['Purchase_Price']      ?? null);
                $retailPrice = $this->nullableFloat($variant['Retail_Price']        ?? null);
                $bbPrice     = $this->nullableFloat($variant['BB_Price']            ?? null);

                $genderString = is_array($gender) ? implode(',', $gender) : $gender;
                $details = implode(' - ', array_filter([
                    $productName, $company, $quality, $color,
                    $size, $typeField, $genderString, $shape, $material, $bridgeSize,
                ]));

                // Main image upload
                $mainImageName = null;
                if ($request->hasFile("variants.{$idx}.main_image")) {
                    $file          = $request->file("variants.{$idx}.main_image");
                    $mainImageName = time() . '_' . uniqid() . '_main_' . $idx . '.' . $file->getClientOriginalExtension();
                    $file->move($folderPath, $mainImageName);
                }

                // Gallery images upload
                $galleryImages = [];
                if ($request->hasFile("variants.{$idx}.images")) {
                    foreach ($request->file("variants.{$idx}.images") as $file) {
                        $imgName         = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move($folderPath, $imgName);
                        $galleryImages[] = $imgName;
                    }
                }

                Product::create([
                    'product_id'               => $productId,
                    'product_type'             => $type,
                    'product_code'             => $sku,
                    'product_name'             => $productName,
                    'productdetails'           => $details,
                    'Company'                  => $company,
                    'Quality'                  => $quality,
                    'Color'                    => $color,
                    'Size'                     => $size,
                    'Type'                     => $typeField,
                    'Gender'                   => $gender,
                    'age'                      => $age,
                    'occasion'                 => $occasion,
                    'face_shape'               => $faceShape,
                    'lens_width'               => $lensWidth,
                    'temple_length'            => $templeLength,
                    'frame_width'              => $frameWidth,
                    'stock_quantity'           => $stockQty,
                    'stock_status'             => $stockStatus,
                    'polarized'                => $polarized,
                    'uv_protection'            => $uvProtection,
                    'barcode'                  => $barcode,
                    'discount_price'           => $discountPrice,
                    'hsn_code'                 => $taxHsnCode,
                    'Shape'                    => $shape,
                    'Material'                 => $material,
                    'Temple_Detail'            => $templeDetail,
                    'Bridge_Size'              => $bridgeSize,
                    'Description'              => $description,
                    'Track_Inventory'          => $trackInventory,
                    'Allow_Negative_Inventory' => $allowNegInventory,
                    'Purchase_Base_Price'      => $purBase,
                    'supported_product_types'  => $supportedProductTypes,
                    'selected_lens_packages'   => $selectedLensPackages,
                    'Purchase_Price'           => $purPrice,
                    'Retail_Price'             => $retailPrice,
                    'BB_Price'                 => $bbPrice,
                    'product_image'            => json_encode($galleryImages),
                    'main_image'               => $mainImageName,
                    'status'                   => $variantStatus,
                    'is_b2c'                   => $isB2c,
                    'category_id'              => $categoryId,
                    'subcategory_id'           => $subcategoryId,
                    'vendor'                   => $vendor,
                    'tags'                     => $tags,
                    'seo_title'                => $seoTitle,
                    'seo_description'          => $seoDescription,
                    'promotion_tag'            => $promotionTag,
                    'special_collection'       => $specialCollection,
                    'parent_product_code'      => $parentProductCode,
                    'added_by'                 => auth()->id(),
                    'store_id'                 => auth()->user()->store_id ?? null,
                    'Modality'                 => $modality,
                    'Packing_Type'             => $variant['pack_size'] ?? $variant['Packing_Type'] ?? null,
                    'Validity'                 => $variant['Validity'] ?? $variant['validity'] ?? null,
                    'WC'                       => $wc,
                    'Dk_t'                     => $dk_t,
                    'SPH'                      => $sph,
                    'CYL'                      => $cyl,
                    'AXIS'                     => $axis,
                    'base_carve'               => $bc,
                    'Diameter'                 => $dia,
                ]);

                $createdCount++;
            }

            DB::commit();

            return response()->json([
                'success'             => $createdCount . ' variant(s) created successfully.',
                'parent_product_code' => $parentProductCode,
                'redirect'            => $this->getIndexRoute($type),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => ['Database Error: ' . $e->getMessage()]]);
        }
    }

    /**
     * Show the edit form for all variants sharing the same parent_product_code.
     */
    public function edit(Request $request, $product_id)
    {
        $firstVariant = Product::where('product_id', $product_id)->first();
        if (!$firstVariant) {
            abort(404, 'Product not found.');
        }

        // Load all variants that share the same parent_product_code
        $variants = !empty($firstVariant->parent_product_code)
            ? Product::where('parent_product_code', $firstVariant->parent_product_code)->get()
            : Product::where('product_id', $product_id)->get(); // fallback for legacy data

        if ($variants->isEmpty()) {
            abort(404, 'Product not found.');
        }

        $first   = $variants->first();
        $type    = $first->product_type;
        $setting = DB::table('tbl_product_code_setting')->where('product_type', $type)->first();

        $brands      = DB::table('tbl_brand')->where('status', '1')->get();
        $sizes       = DB::table('tbl_size')->where('status', '1')->get();
        $types       = DB::table('tbl_type')->where('status', '1')->get();
        $shapes      = DB::table('tbl_shape')->where('status', '1')->get();
        $materials   = DB::table('tbl_material')->where('status', '1')->get();
        $colors      = DB::table('tbl_color')->where('status', '1')->get();
        $categories  = DB::table('categories')->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();
        $collections = DB::table('collections')->where('is_active', true)->whereNull('deleted_at')->orderBy('name')->get();

        $page_title = 'Edit B2C ' . $type . ' Product';
        $active     = 'product code';

        $lensPackages = LensPackage::with(['categories', 'powerTypes'])->where('is_active', true)->orderBy('name')->get();

        return view('products.b2c_product.create', compact(
            'type', 'setting', 'variants', 'first', 'product_id', 'page_title', 'active',
            'brands', 'sizes', 'types', 'shapes', 'materials', 'colors',
            'categories', 'collections', 'lensPackages'
        ));
    }

    /**
     * Update all variants of a product group (by parent_product_code).
     * parent_product_code is always fetched from the DB — never from form input.
     */
    public function updateold(Request $request, $product_id)
    {
        // Always resolve parent_product_code from DB — never trust form input
        $firstVariant = Product::where('product_id', $product_id)->first();
        if (!$firstVariant) {
            return response()->json(['error' => ['Product not found.']]);
        }

        // Use existing parent code, or generate one for legacy rows that don't have it
        $parentProductCode = !empty($firstVariant->parent_product_code)
            ? $firstVariant->parent_product_code
            : $this->generateUniqueParentCode();

        $globalValidator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
        ]);

        if ($globalValidator->fails()) {
            return response()->json(['error' => $globalValidator->errors()->all()]);
        }

        $variants = $request->input('variants', []);
        if (empty($variants)) {
            return response()->json(['error' => ['At least one variant is required.']]);
        }

        $type              = $request->input('product_type', 'Frame');
        $typeLower         = strtolower($type);
        $productName       = $request->input('product_name');
        $company           = $request->input('Company')            ?: null;
        $description       = $request->input('Description')        ?: null;
        $trackInventory    = $request->input('Track_Inventory', '1');
        $allowNegInventory = $request->input('Allow_Negative_Inventory', '1');
        $status            = $request->input('status', '1');
        $isB2c             = $request->input('is_b2c', '1');
        $categoryId        = $request->input('category_id')        ?: null;
        $subcategoryId     = $request->input('subcategory_id')     ?: null;
        $vendor            = $request->input('vendor')             ?: null;
        $tags              = $request->input('tags')               ?: null;
        $seoTitle          = $request->input('seo_title')          ?: null;
        $seoDescription    = $request->input('seo_description')    ?: null;
        $promotionTags     = $request->input('promotion_tag');
        $promotionTag      = is_array($promotionTags) ? implode(',', $promotionTags) : $promotionTags;
        $promotionTag      = $promotionTag                         ?: null;
        $specialCollection = $request->input('special_collection') ?: null;

        $folderPath = public_path("uploads/{$typeLower}/product/{$parentProductCode}");
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $keptIds = [];

        foreach ($variants as $idx => $variant) {
            $variantId    = $variant['id'] ?? null;
            $sku          = $variant['product_code'] ?? '';
            $colorPrimary = $variant['Color']           ?? '';
            $colorSec     = $variant['Secondary_Color'] ?? '';
            $color        = !empty($colorSec) ? "{$colorPrimary} / {$colorSec}" : ($colorPrimary ?: null);
            $size         = $variant['Size']          ?? null;
            $typeField    = $variant['Type']          ?? null;
            $gender       = $variant['Gender']        ?? null;
            $shape        = $variant['Shape']         ?? null;
            $material     = $variant['Material']      ?? null;
            $templeDetail = $variant['Temple_Detail'] ?? null;
            $bridgeSize   = $variant['Bridge_Size']   ?? null;
            $quality      = $variant['Quality']       ?? null;
            $purBase      = $this->nullableFloat($variant['Purchase_Base_Price'] ?? null);
            $purPrice     = $this->nullableFloat($variant['Purchase_Price']      ?? null);
            $retailPrice  = $this->nullableFloat($variant['Retail_Price']        ?? null);
            $bbPrice      = $this->nullableFloat($variant['BB_Price']            ?? null);

            $details = implode(' - ', array_filter([
                $productName, $company, $quality, $color, $size,
                $typeField, $gender, $shape, $material, $bridgeSize,
            ]));

            // Main image
            $mainImageName = null;
            if ($request->hasFile("variants.{$idx}.main_image")) {
                $file          = $request->file("variants.{$idx}.main_image");
                $mainImageName = time() . '_main_' . $idx . '.' . $file->getClientOriginalExtension();
                $file->move($folderPath, $mainImageName);
            }

            // Gallery images
            $newGallery = [];
            if ($request->hasFile("variants.{$idx}.images")) {
                foreach ($request->file("variants.{$idx}.images") as $file) {
                    $imgName    = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($folderPath, $imgName);
                    $newGallery[] = $imgName;
                }
            }

            $sharedData = [
                'product_name'             => $productName,
                'productdetails'           => $details,
                'Company'                  => $company,
                'Quality'                  => $quality,
                'Color'                    => $color,
                'Size'                     => $size,
                'Type'                     => $typeField,
                'Gender'                   => $gender,
                'Shape'                    => $shape,
                'Material'                 => $material,
                'Temple_Detail'            => $templeDetail,
                'Bridge_Size'              => $bridgeSize,
                'Description'              => $description,
                'Track_Inventory'          => $trackInventory,
                'Allow_Negative_Inventory' => $allowNegInventory,
                'Purchase_Base_Price'      => $purBase,
                'Purchase_Price'           => $purPrice,
                'Retail_Price'             => $retailPrice,
                'BB_Price'                 => $bbPrice,
                'status'                   => $status,
                'is_b2c'                   => $isB2c,
                'category_id'              => $categoryId,
                'subcategory_id'           => $subcategoryId,
                'vendor'                   => $vendor,
                'tags'                     => $tags,
                'seo_title'                => $seoTitle,
                'seo_description'          => $seoDescription,
                'promotion_tag'            => $promotionTag,
                'special_collection'       => $specialCollection,
                'parent_product_code'      => $parentProductCode,
                'updated_by'               => auth()->id(),
            ];

            if ($variantId) {
                // Update existing variant
                $existing = Product::find($variantId);
                if ($existing) {
                    $existingGallery        = $existing->product_image ? json_decode($existing->product_image, true) : [];
                    $sharedData['product_image'] = json_encode(array_merge($existingGallery, $newGallery));
                    if ($mainImageName) {
                        $sharedData['main_image'] = $mainImageName;
                    }
                    $existing->update($sharedData);
                    $keptIds[] = $variantId;
                }
            } else {
                // New variant added during edit
                if (empty($sku)) continue;
                if (DB::table('tbl_product_code')->where('product_code', $sku)->exists()) continue;

                $sharedData['product_id']    = $this->generateUniqueProductId();
                $sharedData['product_type']  = $type;
                $sharedData['product_code']  = $sku;
                $sharedData['product_image'] = json_encode($newGallery);
                $sharedData['main_image']    = $mainImageName;
                $sharedData['added_by']      = auth()->id();
                $sharedData['store_id']      = auth()->user()->store_id ?? null;

                $newRow    = Product::create($sharedData);
                $keptIds[] = $newRow->id;
            }
        }

        // Delete variants removed in the UI
        if (!empty($keptIds)) {
            Product::where('parent_product_code', $parentProductCode)
                ->whereNotIn('id', $keptIds)
                ->delete();

            // Sync status across all kept variants
            Product::whereIn('id', $keptIds)->update(['status' => $status]);
        }

        return response()->json([
            'success'  => 'Product updated successfully.',
            'redirect' => $this->getIndexRoute($type),
        ]);
    }
    
    public function update(Request $request, $product_id)
    {
        // Fetch first variant by product_id, id, or parent_product_code
        $firstVariant = Product::where('product_id', $product_id)
            ->orWhere('id', $product_id)
            ->orWhere('parent_product_code', $product_id)
            ->first();

        if (!$firstVariant) {
            return response()->json(['error' => ['Product not found.']]);
        }

        $parentProductCode = !empty($firstVariant->parent_product_code)
            ? $firstVariant->parent_product_code
            : $this->generateUniqueParentCode(); // assign one if legacy row had none

        $globalValidator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'product_type' => 'required|string|in:Frame,Lens,Sunglass,Solution,Accessory,Other,Glass',
        ]);

        if ($globalValidator->fails()) {
            return response()->json(['error' => $globalValidator->errors()->all()]);
        }

        $variants = $request->input('variants', []);
        if (empty($variants)) {
            return response()->json(['error' => ['At least one variant is required.']]);
        }

        // Validate each variant SKU, pricing, and check for intra-payload duplicates
        $validationErrors = [];
        $seenSkus  = [];
        foreach ($variants as $idx => $variant) {
            $variantNum = $idx + 1;
            $variantId = $variant['id'] ?? null;
            $sku = trim($variant['product_code'] ?? '');
            if (empty($sku)) {
                $validationErrors[] = "Variant #{$variantNum}: SKU is required.";
                continue;
            }
            if (strlen($sku) < 3) {
                $validationErrors[] = "Variant #{$variantNum}: SKU must be at least 3 characters.";
                continue;
            }
            $skuLower = strtolower($sku);
            if (in_array($skuLower, $seenSkus, true)) {
                $validationErrors[] = "Variant #{$variantNum}: Duplicate SKU '{$sku}' is used multiple times in this form.";
                continue;
            }
            $seenSkus[] = $skuLower;

            $duplicateQuery = DB::table('tbl_product_code')->where('product_code', $sku);
            if (!empty($variantId)) {
                $duplicateQuery->where('id', '!=', $variantId);
            }
            if ($duplicateQuery->exists()) {
                $validationErrors[] = "Variant #{$variantNum}: SKU '{$sku}' already exists.";
            }

            // Price validation
            $mrp = isset($variant['Retail_Price']) && is_numeric($variant['Retail_Price']) ? (float)$variant['Retail_Price'] : null;
            $discount = isset($variant['discount_price']) && is_numeric($variant['discount_price']) ? (float)$variant['discount_price'] : null;

            if ($mrp !== null && $mrp < 0) {
                $validationErrors[] = "Variant #{$variantNum}: Retail Price (MRP) cannot be negative.";
            }
            if ($discount !== null && $mrp !== null && $discount > $mrp && $mrp > 0) {
                $validationErrors[] = "Variant #{$variantNum}: Discount / Sale Price (₹{$discount}) cannot exceed Retail Price (₹{$mrp}).";
            }

            // Image Aspect Ratio Validation (~2:1 Landscape Ratio)
            if ($request->hasFile("variants.{$idx}.main_image")) {
                $mainFile = $request->file("variants.{$idx}.main_image");
                $imgInfo  = @getimagesize($mainFile->getRealPath());
                if ($imgInfo) {
                    $w = $imgInfo[0];
                    $h = $imgInfo[1];
                    $r = $h > 0 ? $w / $h : 0;
                    if ($r < 1.2 || $r > 3.2) {
                        $validationErrors[] = "Variant #{$variantNum}: Main Image must be in landscape ~2:1 ratio (recommended 800x400px or 1000x500px). Uploaded image is {$w}x{$h}px (Ratio: " . round($r, 2) . ":1).";
                    }
                }
            }

            if ($request->hasFile("variants.{$idx}.images")) {
                foreach ($request->file("variants.{$idx}.images") as $gIdx => $gFile) {
                    $imgInfo = @getimagesize($gFile->getRealPath());
                    if ($imgInfo) {
                        $w = $imgInfo[0];
                        $h = $imgInfo[1];
                        $r = $h > 0 ? $w / $h : 0;
                        if ($r < 1.2 || $r > 3.2) {
                            $validationErrors[] = "Variant #{$variantNum}: Gallery Image #" . ($gIdx + 1) . " must be in landscape ~2:1 ratio (recommended 800x400px). Uploaded image is {$w}x{$h}px (Ratio: " . round($r, 2) . ":1).";
                        }
                    }
                }
            }
        }

        if (!empty($validationErrors)) {
            return response()->json(['error' => $validationErrors]);
        }

        $type              = $request->input('product_type', 'Frame');
        $typeLower         = strtolower($type);
        $productName       = $request->input('product_name');
        $company           = $request->input('Company')            ?: null;
        $description       = $request->input('Description')        ?: null;
        $trackInventory    = $request->input('Track_Inventory', '1');
        $allowNegInventory = $request->input('Allow_Negative_Inventory', '1');
        $status            = $request->input('status', '1');
        $isB2c             = $request->input('is_b2c', '1');
        $categoryId        = $request->input('category_id')        ?: null;
        $subcategoryId     = $request->input('subcategory_id')     ?: null;
        $vendor            = $request->input('vendor')             ?: null;
        $tags              = $request->input('tags')               ?: null;
        $seoTitle          = $request->input('seo_title')          ?: null;
        $seoDescription    = $request->input('seo_description')    ?: null;
        $promotionTags     = $request->input('promotion_tag');
        $promotionTag      = is_array($promotionTags) ? implode(',', $promotionTags) : $promotionTags;
        $promotionTag      = $promotionTag                         ?: null;
        $specialCollection = $request->input('special_collection') ?: null;

        $age               = $request->input('age');
        $occasion          = $request->input('occasion');
        $faceShape         = $request->input('face_shape');
        $gender            = $request->input('gender');
        $typeTop           = $request->input('Type');
        $shapeTop          = $request->input('Shape');
        $supportedProductTypes = $request->input('supported_product_types');
        $selectedLensPackages  = $request->input('selected_lens_packages');

        $folderPath = public_path("uploads/{$typeLower}/product/{$parentProductCode}");
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        $keptIds = [];

        foreach ($variants as $idx => $variant) {
            $variantId    = $variant['id'] ?? null;
            $sku          = trim($variant['product_code'] ?? '');
            $colorPrimary = $variant['Color']           ?? '';
            $colorSec     = $variant['Secondary_Color'] ?? '';
            $color        = !empty($colorSec)
                ? trim($colorPrimary) . ' / ' . trim($colorSec)
                : ($colorPrimary ?: null);

            $size         = $variant['Size']          ?? null;
            if (is_array($size)) {
                $size = implode(',', $size);
            }
            $typeField    = $typeTop;
            $shape        = $shapeTop;
            $material     = $variant['Material']      ?? null;
            $templeDetail = $variant['Temple_Detail'] ?? null;
            $bridgeSize   = $variant['Bridge_Size']   ?? null;
            $quality      = $variant['Quality']       ?? null;
            
            $lensWidth     = $variant['lens_width']    ?? null;
            $templeLength  = $variant['temple_length'] ?? null;
            $frameWidth    = $variant['frame_width']   ?? null;
            $stockQty      = $variant['stock_quantity']?? 0;
            $stockStatus   = $variant['stock_status']  ?? null;
            $polarized     = $variant['polarized']     ?? 0;
            $uvProtection  = $variant['uv_protection'] ?? null;
            $barcode       = $variant['barcode']       ?? null;
            $discountPrice = $this->nullableFloat($variant['discount_price'] ?? null);
            $taxHsnCode    = $variant['tax_hsn_code']  ?? null;
            $variantStatus = $variant['status']        ?? $status; // Fallback to global status if not set

            // Lens-specific fields (with resilient aliases)
            $modality      = $variant['Modality']      ?? $variant['modality']      ?? null;
            $wc            = $variant['WC']            ?? $variant['wc']            ?? null;
            $dk_t          = $variant['Dk_t']          ?? $variant['dk_t']          ?? null;
            $bc            = $variant['BC']            ?? $variant['base_curve']    ?? $variant['base_carve'] ?? null;
            $dia           = $variant['DIA']           ?? $variant['diameter']      ?? null;
            $sph           = $variant['SPH']           ?? $variant['sph_range']     ?? null;
            $cyl           = $variant['CYL']           ?? $variant['cyl_range']     ?? null;
            $axis          = $variant['AXIS']          ?? $variant['axis_range']    ?? null;

            $purBase      = $this->nullableFloat($variant['Purchase_Base_Price'] ?? null);
            $purPrice     = $this->nullableFloat($variant['Purchase_Price']      ?? null);
            $retailPrice  = $this->nullableFloat($variant['Retail_Price']        ?? null);
            $bbPrice      = $this->nullableFloat($variant['BB_Price']            ?? null);

            $genderString = is_array($gender) ? implode(',', $gender) : $gender;
            $details = implode(' - ', array_filter([
                $productName, $company, $quality, $color,
                $size, $typeField, $genderString, $shape, $material, $bridgeSize,
            ]));

            // Main image
            $mainImageName = null;
            if ($request->hasFile("variants.{$idx}.main_image")) {
                $file          = $request->file("variants.{$idx}.main_image");
                $mainImageName = time() . '_' . uniqid() . '_main_' . $idx . '.' . $file->getClientOriginalExtension();
                $file->move($folderPath, $mainImageName);
            }

            // Gallery images
            $newGallery = [];
            if ($request->hasFile("variants.{$idx}.images")) {
                foreach ($request->file("variants.{$idx}.images") as $file) {
                    $imgName    = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move($folderPath, $imgName);
                    $newGallery[] = $imgName;
                }
            }

            $sharedData = [
                'product_name'             => $productName,
                'productdetails'           => $details,
                'Company'                  => $company,
                'Quality'                  => $quality,
                'Color'                    => $color,
                'Size'                     => $size,
                'Type'                     => $typeField,
                'Gender'                   => $gender,
                'age'                      => $age,
                'occasion'                 => $occasion,
                'face_shape'               => $faceShape,
                'lens_width'               => $lensWidth,
                'temple_length'            => $templeLength,
                'frame_width'              => $frameWidth,
                'stock_quantity'           => $stockQty,
                'stock_status'             => $stockStatus,
                'polarized'                => $polarized,
                'uv_protection'            => $uvProtection,
                'barcode'                  => $barcode,
                'discount_price'           => $discountPrice,
                'hsn_code'                 => $taxHsnCode,
                'Shape'                    => $shape,
                'Material'                 => $material,
                'Temple_Detail'            => $templeDetail,
                'Bridge_Size'              => $bridgeSize,
                'Description'              => $description,
                'Track_Inventory'          => $trackInventory,
                'Allow_Negative_Inventory' => $allowNegInventory,
                'Purchase_Base_Price'      => $purBase,
                'supported_product_types'  => $supportedProductTypes,
                'selected_lens_packages'   => $selectedLensPackages,
                'Purchase_Price'           => $purPrice,
                'Retail_Price'             => $retailPrice,
                'BB_Price'                 => $bbPrice,
                'status'                   => $variantStatus,
                'is_b2c'                   => $isB2c,
                'category_id'              => $categoryId,
                'subcategory_id'           => $subcategoryId,
                'vendor'                   => $vendor,
                'tags'                     => $tags,
                'seo_title'                => $seoTitle,
                'seo_description'          => $seoDescription,
                'promotion_tag'            => $promotionTag,
                'special_collection'       => $specialCollection,
                'parent_product_code'      => $parentProductCode,
                'updated_by'               => auth()->id(),
                'Modality'                 => $modality,
                'Packing_Type'             => $variant['pack_size'] ?? $variant['Packing_Type'] ?? null,
                'Validity'                 => $variant['Validity'] ?? $variant['validity'] ?? null,
                'WC'                       => $wc,
                'Dk_t'                     => $dk_t,
                'SPH'                      => $sph,
                'CYL'                      => $cyl,
                'AXIS'                     => $axis,
                'base_carve'               => $bc,
                'Diameter'                 => $dia,
            ];

            if ($variantId) {
                $existing = Product::find($variantId);
                if ($existing) {
                    $existingGallery = $existing->product_image ? json_decode($existing->product_image, true) : [];
                    if (!is_array($existingGallery)) $existingGallery = [];
                    
                    $deletedImages = $variant['deleted_images'] ?? [];
                    if (!empty($deletedImages)) {
                        $existingGallery = array_values(array_diff($existingGallery, $deletedImages));
                        foreach ($deletedImages as $delImg) {
                            $delPath = $folderPath . '/' . $delImg;
                            if (file_exists($delPath)) {
                                @unlink($delPath);
                            }
                        }
                    }

                    $sharedData['product_image'] = json_encode(array_merge($existingGallery, $newGallery));
                    if ($mainImageName) {
                        $sharedData['main_image'] = $mainImageName;
                    }
                    $existing->update($sharedData);
                    $keptIds[] = (int) $variantId;
                }
            } else {
                // New variant added during edit
                if (empty($sku)) continue;
                if (DB::table('tbl_product_code')->where('product_code', $sku)->exists()) continue;

                $sharedData['product_id']    = $this->generateUniqueProductId();
                $sharedData['product_type']  = $type;
                $sharedData['product_code']  = $sku;
                $sharedData['product_image'] = json_encode($newGallery);
                $sharedData['main_image']    = $mainImageName;
                $sharedData['added_by']      = auth()->id();
                $sharedData['store_id']      = auth()->user()->store_id ?? null;

                $newRow    = Product::create($sharedData);
                $keptIds[] = $newRow->id;
            }
        }

        if (!empty($keptIds)) {
            // BUG FIX: old code used $product_id inside closure without passing it via use()
            // causing "Undefined variable $product_id". Now using parent_product_code only — cleaner.
            Product::where('parent_product_code', $parentProductCode)
                ->whereNotIn('id', $keptIds)
                ->delete();

            // Sync status for all kept variants in this group
            Product::whereIn('id', $keptIds)->update(['status' => $status]);
        }

        return response()->json([
            'success'  => 'Product updated successfully.',
            'redirect' => $this->getIndexRoute($type),
        ]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Auto-generate a unique parent product code.
     * Format: P-XXXXXX (e.g. P-A3F9KL)
     * Guaranteed unique in the DB before returning.
     */
    protected function generateUniqueParentCode(): string
    {
        do {
            $code = 'P-' . strtoupper(Str::random(6));
        } while (DB::table('tbl_product_code')->where('parent_product_code', $code)->exists());

        return $code;
    }

    /**
     * Auto-generate a unique numeric product_id (6-digit random).
     */
    protected function generateUniqueProductId(int $min = 100000, int $max = 999999): int
    {
        do {
            $id = random_int($min, $max);
        } while (DB::table('tbl_product_code')->where('product_id', $id)->exists());

        return $id;
    }

    /**
     * Convert empty string / null to null for float DB columns.
     * Prevents SQLSTATE[22007] "Incorrect double value" errors.
     */
    protected function nullableFloat($value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }
        return (float) $value;
    }

    protected function getIndexRoute(string $type): string
    {
        return route('admin.catalog.index');
    }

    /**
     * Delete an entire product family (all variants with matching parent_product_code)
     * and clean up its upload directory from disk.
     */
    public function destroy($product_id)
    {
        $firstVariant = Product::where('product_id', $product_id)
            ->orWhere('id', $product_id)
            ->orWhere('parent_product_code', $product_id)
            ->first();

        if (!$firstVariant) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $parentCode = $firstVariant->parent_product_code ?: $firstVariant->product_id;
        $typeLower  = strtolower($firstVariant->product_type ?: 'frame');

        // Delete all variants in this family
        if (!empty($firstVariant->parent_product_code)) {
            Product::where('parent_product_code', $firstVariant->parent_product_code)->delete();
        } else {
            $firstVariant->delete();
        }

        // Clean up media directory from disk
        $folderPath = public_path("uploads/{$typeLower}/product/{$parentCode}");
        if (is_dir($folderPath)) {
            \Illuminate\Support\Facades\File::deleteDirectory($folderPath);
        }

        return response()->json([
            'success' => 'Product and all associated variants deleted successfully.'
        ]);
    }

    /**
     * Get complete 360-degree product family details for the Quick View Modal.
     */
    public function getProductDetails($product_id)
    {
        $firstVariant = Product::where('product_id', $product_id)
            ->orWhere('id', $product_id)
            ->orWhere('parent_product_code', $product_id)
            ->first();

        if (!$firstVariant) {
            return response()->json(['error' => 'Product not found.'], 404);
        }

        $variants = !empty($firstVariant->parent_product_code)
            ? Product::where('parent_product_code', $firstVariant->parent_product_code)->get()
            : Product::where('product_id', $product_id)->get();

        $first = $variants->first();
        $type = $first->product_type ?: 'Frame';
        $typeLower = strtolower($type);
        $parentCode = $first->parent_product_code ?: $first->product_id;

        // Resolve relational names
        $category = !empty($first->category_id) ? DB::table('categories')->where('id', $first->category_id)->value('name') : null;
        $subcategory = !empty($first->subcategory_id) ? DB::table('subcategories')->where('id', $first->subcategory_id)->value('name') : null;
        $collection = !empty($first->special_collection) ? DB::table('collections')->where('id', $first->special_collection)->value('name') : $first->special_collection;

        $asArray = function ($val) {
            if (is_array($val)) return $val;
            if (is_string($val)) return json_decode($val, true) ?: [];
            return [];
        };

        // Lens packages
        $packageNames = [];
        $selectedPackages = $asArray($first->selected_lens_packages);
        if (!empty($selectedPackages)) {
            $packageNames = DB::table('lens_packages')->whereIn('id', $selectedPackages)->pluck('name')->toArray();
        }

        // Supported product types
        $supportedTypeNames = [];
        $supportedTypeIds = $asArray($first->supported_product_types);
        if (!empty($supportedTypeIds)) {
            $supportedTypeNames = DB::table('product_type_masters')->whereIn('id', $supportedTypeIds)->pluck('name')->toArray();
        }

        $uploadBase = asset('uploads/' . $typeLower . '/product/' . $parentCode);

        $variantList = $variants->map(function ($v) use ($uploadBase, $asArray) {
            $mainImgUrl = !empty($v->main_image) ? $uploadBase . '/' . $v->main_image : null;
            $galleryUrls = [];
            $galleryJson = $asArray($v->product_image);
            foreach ($galleryJson as $g) {
                $galleryUrls[] = $uploadBase . '/' . $g;
            }

            return [
                'id'             => $v->id,
                'product_code'   => $v->product_code,
                'Color'          => $v->Color,
                'Secondary_Color'=> $v->Secondary_Color,
                'Size'           => $v->Size,
                'Material'       => $v->Material,
                'Quality'        => $v->Quality,
                'Temple_Detail'  => $v->Temple_Detail,
                'Bridge_Size'    => $v->Bridge_Size,
                'Purchase_Price' => $v->Purchase_Price,
                'Retail_Price'   => $v->Retail_Price,
                'discount_price' => $v->discount_price,
                'tax_hsn_code'   => $v->tax_hsn_code,
                'lens_width'     => $v->lens_width,
                'temple_length'  => $v->temple_length,
                'frame_width'    => $v->frame_width,
                'polarized'      => $v->polarized,
                'uv_protection'  => $v->uv_protection,
                // Contact lens specs
                'Modality'       => $v->Modality,
                'pack_size'      => $v->pack_size,
                'WC'             => $v->WC,
                'Dk_t'           => $v->Dk_t,
                'BC'             => $v->base_carve ?: $v->BC,
                'DIA'            => $v->Diameter ?: $v->DIA,
                'SPH'            => $v->sphere ?: $v->SPH,
                'CYL'            => $v->cylinder ?: $v->CYL,
                'AXIS'           => $v->axis ?: $v->AXIS,
                'main_image_url' => $mainImgUrl,
                'gallery_urls'   => $galleryUrls,
            ];
        });

        return response()->json([
            'success'            => true,
            'product_id'         => $first->product_id,
            'parent_product_code'=> $parentCode,
            'product_name'       => $first->product_name,
            'product_type'       => $type,
            'Company'            => $first->Company,
            'category_name'      => $category,
            'subcategory_name'   => $subcategory,
            'collection_name'    => $collection,
            'Shape'              => $first->Shape,
            'Type'               => $first->Type,
            'Description'        => $first->Description,
            'face_shape'         => $asArray($first->face_shape),
            'gender'             => $asArray($first->gender),
            'age'                => $asArray($first->age),
            'occasion'           => $asArray($first->occasion),
            'status'             => (int)$first->status,
            'is_b2c'             => (int)$first->is_b2c,
            'supported_types'    => $supportedTypeNames,
            'lens_packages'      => $packageNames,
            'variants_count'     => $variants->count(),
            'variants'           => $variantList,
            'edit_url'           => url(config('app.admin_path', 'admin') . '/products/' . $first->product_id . '/edit'),
        ]);
    }
   
}