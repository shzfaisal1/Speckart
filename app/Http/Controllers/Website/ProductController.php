<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function products(Request $request, $any = null){
        $query = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1);

        // Apply BOGO Eligible Filter (e.g. ?bogo_eligible=1 from cart CTA)
        if ($request->filled('bogo_eligible') && $request->input('bogo_eligible') == 1) {
            $query->where(function($q) {
                $q->where('promotion_tag', 'LIKE', '%BOGO%')
                  ->orWhere('promotion_tag', 'LIKE', '%Buy 1 Get 1%')
                  ->orWhereNull('promotion_tag')
                  ->orWhere('promotion_tag', '');
            })
            // Strictly exclude Contact Lenses, Solutions, and Accessories from BOGO catalog listing
            ->where(function($q) {
                $q->whereNull('product_type')
                  ->orWhere(function($sub) {
                      $sub->where('product_type', 'NOT LIKE', '%contact%')
                          ->where('product_type', 'NOT LIKE', '%solution%')
                          ->where('product_type', 'NOT LIKE', '%accessory%')
                          ->where('product_type', 'NOT LIKE', '%accessories%')
                          ->where('product_type', '!=', 'Lens')
                          ->where('product_type', '!=', 'other');
                  });
            })
            ->where(function($q) {
                $q->whereNull('Type')
                  ->orWhere(function($sub) {
                      $sub->where('Type', 'NOT LIKE', '%contact%')
                          ->where('Type', 'NOT LIKE', '%solution%')
                          ->where('Type', 'NOT LIKE', '%accessory%')
                          ->where('Type', 'NOT LIKE', '%accessories%')
                          ->where('Type', '!=', 'Lens')
                          ->where('Type', '!=', 'other');
                  });
            });
        }

        // Apply Offer Filter (by ID or Coupon from URL e.g. ?offer=5)
        if ($request->filled('offer')) {
            $offerVal = $request->input('offer');
            $offer = DB::table('offers')
                ->where('id', $offerVal)
                ->orWhere('coupon_code', $offerVal)
                ->first();

            if ($offer) {
                // Helper to decode category/brand/product IDs safely regardless of storage format
                $safeDecodeIds = function ($val) {
                    if (is_array($val)) {
                        return $val;
                    }
                    if (empty($val)) {
                        return [];
                    }
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        return $decoded;
                    }
                    if (preg_match('/^[aOisd]:\d+:/', $val)) {
                        $unserialized = @unserialize($val);
                        if (is_array($unserialized)) {
                            return $unserialized;
                        }
                    }
                    if (strpos($val, ',') !== false) {
                        return array_map('trim', explode(',', $val));
                    }
                    return [$val];
                };

                switch ($offer->apply_on) {
                    case 'specific_category':
                        $categoryIds = $safeDecodeIds($offer->category_ids);
                        if (!empty($categoryIds)) {
                            $query->whereIn('category_id', $categoryIds);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                        break;

                    case 'specific_brand':
                        $brandIds = $safeDecodeIds($offer->brand_ids);
                        if (!empty($brandIds)) {
                            $brandNames = DB::table('tbl_brand')
                                ->whereIn('brand_id', $brandIds)
                                ->pluck('brand_name')
                                ->toArray();
                            if (!empty($brandNames)) {
                                $query->whereIn('Company', $brandNames);
                            } else {
                                $query->whereRaw('1 = 0');
                            }
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                        break;

                    case 'specific_products':
                        $productIds = $safeDecodeIds($offer->product_ids);
                        if (!empty($productIds)) {
                            $query->whereIn('id', $productIds);
                        } else {
                            $query->whereRaw('1 = 0');
                        }
                        break;

                    case 'all_products':
                    default:
                        break;
                }
            }
        }

        // Apply Category Filter (by slug from URL e.g. ?category=women)
        $activeCategory = null;
        if ($request->filled('category')) {
            $activeCategory = Category::where('slug', $request->input('category'))
                                      ->where('is_active', true)
                                      ->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        // Apply product type filters (Frame/Goggles)
        // Apply product type filters (Frame/Goggles/Contact Lens)
        if ($request->filled('type')) {
            $types = array_filter(array_map('trim', explode(',', $request->input('type'))));
            $query->whereIn('product_type', $types);
        }

        // Apply Frame Type Filter (Full Rim / Half Rim / Rimless)
        if ($request->filled('frame_type')) {
            $frameTypes = array_filter(array_map('trim', explode(',', $request->input('frame_type'))));
            $query->where(function($q) use ($frameTypes) {
                foreach ($frameTypes as $ft) {
                    $q->orWhere('Type', 'LIKE', '%' . $ft . '%');
                }
            });
        }

        // Apply Frame Shape Filter (Round, Rectangle, Aviator, Cat Eye, Square, Wayfarer, Oval, Hexagonal)
        if ($request->filled('shape')) {
            $shapes = array_filter(array_map('trim', explode(',', $request->input('shape'))));
            $query->where(function($q) use ($shapes) {
                foreach ($shapes as $sh) {
                    $q->orWhere('Shape', 'LIKE', '%' . $sh . '%');
                }
            });
        }

        // Apply Color Filter
        if ($request->filled('color')) {
            $colors = array_filter(array_map('trim', explode(',', $request->input('color'))));
            $query->where(function($q) use ($colors) {
                foreach ($colors as $cl) {
                    $q->orWhere('Color', 'LIKE', '%' . $cl . '%');
                }
            });
        }

        // Apply Brand/Company Filter
        if ($request->filled('brand')) {
            $brands = array_filter(array_map('trim', explode(',', $request->input('brand'))));
            $query->whereIn('Company', $brands);
        }

        // Apply Modality Filter (Contact Lenses Disposability)
        if ($request->filled('modality')) {
            $modalities = array_filter(array_map('trim', explode(',', $request->input('modality'))));
            $query->whereIn('Modality', $modalities);
        }

        // Apply Frame Size Filter (Small, Medium, Large)
        if ($request->filled('size')) {
            $sizes = array_filter(array_map('trim', explode(',', $request->input('size'))));
            $query->where(function($q) use ($sizes) {
                foreach ($sizes as $sz) {
                    $q->orWhere('Size', 'LIKE', '%' . $sz . '%');
                }
            });
        }

        // Apply Material Filter
        if ($request->filled('material')) {
            $materials = array_filter(array_map('trim', explode(',', $request->input('material'))));
            $query->whereIn('Material', $materials);
        }

        // Apply Age Filter (stored as JSON/string, use LIKE)
        if ($request->filled('age')) {
            $ageVal = $request->input('age');
            $query->where(function($q) use ($ageVal) {
                foreach (explode(',', $ageVal) as $a) {
                    $q->orWhere('age', 'LIKE', '%' . trim($a) . '%');
                }
            });
        }

        // Apply Occasion Filter (stored as JSON/string, use LIKE)
        if ($request->filled('occasion')) {
            $occVal = $request->input('occasion');
            $query->where(function($q) use ($occVal) {
                foreach (explode(',', $occVal) as $o) {
                    $q->orWhere('occasion', 'LIKE', '%' . trim($o) . '%');
                }
            });
        }

        // Apply Face Shape Filter
        if ($request->filled('face_shape')) {
            $faceVal = $request->input('face_shape');
            $query->where(function($q) use ($faceVal) {
                foreach (explode(',', $faceVal) as $f) {
                    $q->orWhere('face_shape', 'LIKE', '%' . trim($f) . '%');
                }
            });
        }

        // Apply Sunglass / Lens Tint Colour Filter (Search in Color attribute safely)
        if ($request->filled('sunglass_colour')) {
            $sgColors = array_filter(array_map('trim', explode(',', $request->input('sunglass_colour'))));
            $query->where(function($q) use ($sgColors) {
                foreach ($sgColors as $sc) {
                    $q->orWhere('Color', 'LIKE', '%' . $sc . '%');
                }
            });
        }

        // Apply Price Range Filter
        if ($request->filled('price_range')) {
            $priceRanges = array_filter(array_map('trim', explode(',', $request->input('price_range'))));
            $query->where(function($q) use ($priceRanges) {
                foreach ($priceRanges as $pr) {
                    if ($pr == 'under_1000') {
                        $q->orWhere('Retail_Price', '<', 1000);
                    } else if ($pr == 'under_2000') {
                        $q->orWhere('Retail_Price', '<', 2000);
                    } else if ($pr == 'under_5000') {
                        $q->orWhere('Retail_Price', '<', 5000);
                    }
                }
            });
        }

        // Apply Gender Filter (Men / Women / Kids / Unisex)
        if ($request->filled('gender')) {
            $genders = array_filter(array_map('trim', explode(',', $request->input('gender'))));
            $query->where(function($q) use ($genders) {
                foreach ($genders as $g) {
                    $gLower = strtolower($g);
                    if ($gLower === 'men') {
                        // Match 'Men', 'Unisex', or comma-separated lists without matching 'Women'
                        $q->orWhere(function($subQ) {
                            $subQ->where('Gender', 'Men')
                                 ->orWhere('Gender', 'LIKE', '%Unisex%')
                                 ->orWhere('Gender', 'LIKE', 'Men,%')
                                 ->orWhere('Gender', 'LIKE', '%,Men%')
                                 ->orWhere('Gender', 'LIKE', '%,Men')
                                 ->orWhere('Gender', '')
                                 ->orWhereNull('Gender');
                        });
                    } elseif ($gLower === 'women') {
                        $q->orWhere(function($subQ) {
                            $subQ->where('Gender', 'LIKE', '%Women%')
                                 ->orWhere('Gender', 'LIKE', '%Unisex%')
                                 ->orWhere('Gender', '')
                                 ->orWhereNull('Gender');
                        });
                    } else {
                        $q->orWhere('Gender', 'LIKE', "%{$g}%")
                          ->orWhere('Gender', 'LIKE', '%Unisex%')
                          ->orWhere('Gender', '')
                          ->orWhereNull('Gender');
                    }
                }
            });
        }

        // Apply Tag Filter (New Arrivals, Best Seller, Trending)
        if ($request->filled('tag')) {
            $tagVal = $request->input('tag');
            if ($tagVal === 'new-arrival') {
                $query->orderBy('created_at', 'desc');
            } elseif ($tagVal === 'best-seller') {
                $query->where(function($q) {
                    $q->where('promotion_tag', 'LIKE', '%Best%')
                      ->orWhere('product_name', 'LIKE', '%Best%');
                });
            } elseif ($tagVal === 'trending') {
                $query->where(function($q) {
                    $q->where('promotion_tag', 'LIKE', '%Trend%')
                      ->orWhere('product_name', 'LIKE', '%Trend%');
                });
            }
        }

        // Apply Collection Filter
        if ($request->filled('collection')) {
            $colVal = $request->input('collection');
            $query->where(function($q) use ($colVal) {
                $q->where('product_name', 'LIKE', "%{$colVal}%")
                  ->orWhere('Company', 'LIKE', "%{$colVal}%");
            });
        }

        // Search Input
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('Company', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $sort = $request->input('sort');
        if ($sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else if ($sort === 'price_low') {
            $query->orderBy('Retail_Price', 'asc');
        } else if ($sort === 'price_high') {
            $query->orderBy('Retail_Price', 'desc');
        } else {
            // Prioritize products with images first, then latest
            $query->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
                  ->orderBy('id', 'desc');
        }

        $productsList = $query->paginate(12);

        // Fetch sibling color variants for all products on current page
        $parentCodes = $productsList->pluck('parent_product_code')->filter()->unique()->toArray();
        $siblingsByParent = [];
        if (!empty($parentCodes)) {
            $allSiblings = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->whereIn('parent_product_code', $parentCodes)
                ->get();

            foreach ($allSiblings as $sib) {
                $sib->image_url  = getProductImageUrl($sib, $sib->main_image);
                $sib->detail_url = url('/product/' . ($sib->product_id ?: $sib->id));
                $siblingsByParent[$sib->parent_product_code][] = $sib;
            }
        }

        // Fetch live stock for all products on current page from Store 6 (tbl_inventory_levels)
        $pageProductCodes = $productsList->pluck('product_code')->filter()->toArray();
        $liveStockMap = [];
        if (!empty($pageProductCodes)) {
            $ecomStoreId = \App\Services\StockSyncService::getEcommerceStoreId();
            $liveStockMap = DB::table('tbl_inventory_levels')
                ->where('store_id', $ecomStoreId)
                ->whereIn('product_code', $pageProductCodes)
                ->groupBy('product_code')
                ->select('product_code', DB::raw('SUM(available_quantity) as web_stock'))
                ->pluck('web_stock', 'product_code')
                ->toArray();
        }

        // Map helper for images, URLs, color variants, and live stock
        $productsList->getCollection()->transform(function ($p) use ($siblingsByParent, $liveStockMap) {
            $p->image_url  = getProductImageUrl($p);
            $p->detail_url = url('/product/' . ($p->product_id ?: $p->id));

            // Live stock mapping from Store 6
            if (isset($liveStockMap[$p->product_code])) {
                $p->stock_quantity = max(0, (int)$liveStockMap[$p->product_code]);
            } else {
                $p->stock_quantity = (int)($p->stock_quantity ?? 0);
            }
            $p->stock_status = $p->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';

            if (!empty($p->parent_product_code) && isset($siblingsByParent[$p->parent_product_code])) {
                $p->color_variants_list = $siblingsByParent[$p->parent_product_code];
            } else {
                $p->color_variants_list = [];
            }
            return $p;
        });

        // Determine allowed filters & dynamic filter options from the database
        $allowedFilters = [];
        $filterData = [
            'brands'     => [],
            'colors'     => [],
            'sizes'      => [],
            'shapes'     => [],
            'modalities' => [],
            'materials'  => [],
            'genders'    => ['Men', 'Women', 'Unisex', 'Kids'],
            'occasions'  => ['Party', 'Casual', 'Office', 'Sports', 'Formal', 'Everyday'],
            'ages'       => ['Kids', 'Teen', 'Adult', 'Senior'],
        ];

        if ($activeCategory) {
            // Use DB-configured allowed_filters first
            $dbFilters = $activeCategory->allowed_filters ?: [];

            // If no filters configured in DB yet, use smart defaults based on category name
            if (empty($dbFilters)) {
                $catNameLower = strtolower($activeCategory->name);
                $isLensCat = str_contains($catNameLower, 'lens')
                    || str_contains($catNameLower, 'contact');

                if ($isLensCat) {
                    $dbFilters = ['modality', 'brand', 'color', 'price_range', 'collections'];
                } else {
                    // Frame / Sunglasses / Computer Glasses etc.
                    $dbFilters = [
                        'brand', 'frame_type', 'shape', 'gender', 'occasion',
                        'age', 'color', 'material', 'size', 'price_range', 'collections'
                    ];
                }
            }

            $allowedFilters = $dbFilters;

            $baseProductQuery = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->where('category_id', $activeCategory->id);

            $filterData['brands']     = (clone $baseProductQuery)->whereNotNull('Company')->where('Company', '!=', '')->distinct()->pluck('Company')->toArray();
            $filterData['colors']     = (clone $baseProductQuery)->whereNotNull('Color')->where('Color', '!=', '')->distinct()->pluck('Color')->toArray();
            $filterData['sizes']      = (clone $baseProductQuery)->whereNotNull('Size')->where('Size', '!=', '')->distinct()->pluck('Size')->toArray();
            $filterData['shapes']     = (clone $baseProductQuery)->whereNotNull('Shape')->where('Shape', '!=', '')->distinct()->pluck('Shape')->toArray();
            $filterData['modalities'] = (clone $baseProductQuery)->whereNotNull('Modality')->where('Modality', '!=', '')->distinct()->pluck('Modality')->toArray();
            $filterData['materials']  = (clone $baseProductQuery)->whereNotNull('Material')->where('Material', '!=', '')->distinct()->pluck('Material')->toArray();
        } else {
            // Default filters if no category is active — show all frame filters
            $allowedFilters = [
                'brand', 'frame_type', 'shape', 'gender', 'occasion',
                'age', 'color', 'material', 'size', 'price_range', 'collections'
            ];
        }

        $wishlistProductIds = [];
        if (\Auth::check()) {
            $wishlistProductIds = DB::table('wishlists')->where('user_id', \Auth::id())->pluck('product_id')->toArray();
        }

        if ($request->ajax()) {
            return view('website.products.product_grid', compact('productsList', 'activeCategory', 'allowedFilters', 'filterData', 'wishlistProductIds'));
        }
        return view('website.products.products', compact('productsList', 'activeCategory', 'allowedFilters', 'filterData', 'wishlistProductIds'));
    }

    public function details($slug){
        $product = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->where(function($query) use ($slug) {
                $query->where('product_id', $slug)
                      ->orWhere('id', $slug)
                      ->orWhere('product_code', $slug);
            })
            ->first();

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Resolve live stock and perbox from Store 6 (tbl_inventory_levels)
        $ecomStoreId = \App\Services\StockSyncService::getEcommerceStoreId();
        $invRecord = DB::table('tbl_inventory_levels')
            ->where('product_code', $product->product_code)
            ->where('store_id', $ecomStoreId)
            ->first();
        if ($invRecord) {
            $product->stock_quantity = \App\Services\StockSyncService::getLiveStock($product->product_code, $ecomStoreId);
            if (!empty($invRecord->perbox)) {
                $product->perbox = (int)$invRecord->perbox;
            }
        } else {
            $product->stock_quantity = (int)($product->stock_quantity ?? 0);
        }
        if (empty($product->Pieces_Per_Box) && !empty($product->perbox)) {
            $product->Pieces_Per_Box = $product->perbox;
        }
        $product->stock_status = $product->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';

        // Fetch category name
        $categoryName = DB::table('categories')->where('id', $product->category_id)->value('name') ?: 'Products';

        // Resolve lens pack variants for Contact Lenses
        $lensPackVariants = collect();
        $isContactLensType = (strtolower(trim($product->product_type ?? '')) === 'lens') || !empty($product->Modality) || str_contains(strtolower($categoryName ?? ''), 'contact');
        if ($isContactLensType) {
            $pQuery = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->where(function($q) {
                    $q->where('product_type', 'Lens')
                      ->orWhereNotNull('Modality');
                });

            if (!empty($product->parent_product_code)) {
                $pQuery->where('parent_product_code', $product->parent_product_code);
            } else {
                $pQuery->where('product_name', $product->product_name);
            }

            $rawVariants = $pQuery->get();
            if ($rawVariants->isEmpty()) {
                $rawVariants = collect([$product]);
            }

            $lensPackVariants = $rawVariants->map(function ($pv) use ($product, $ecomStoreId) {
                // Live stock
                $hasInv = DB::table('tbl_inventory_levels')
                    ->where('product_code', $pv->product_code)
                    ->where('store_id', $ecomStoreId)
                    ->first();
                $pvStock = $hasInv ? \App\Services\StockSyncService::getLiveStock($pv->product_code, $ecomStoreId) : (int)($pv->stock_quantity ?? 0);
                $pv->stock_quantity = $pvStock;
                $pv->in_stock = ($pvStock > 0);

                if (empty($pv->Pieces_Per_Box) && $hasInv && !empty($hasInv->perbox)) {
                    $pv->Pieces_Per_Box = (int)$hasInv->perbox;
                }

                // Format pack label
                $count = null;
                if (!empty($pv->Pieces_Per_Box) && is_numeric($pv->Pieces_Per_Box) && (int)$pv->Pieces_Per_Box > 0) {
                    $count = (int)$pv->Pieces_Per_Box;
                } elseif (!empty($pv->pack_size) && is_numeric($pv->pack_size) && (int)$pv->pack_size > 0) {
                    $count = (int)$pv->pack_size;
                } elseif (!empty($pv->Packing_Type) && preg_match('/(\d+)/', (string)$pv->Packing_Type, $m)) {
                    $count = (int)$m[1];
                }

                if ($count !== null && $count > 0) {
                    $pv->pack_label = $count === 1 ? '1 Lens / Box' : ($count . ' Lenses / Box');
                    $pv->pack_subtitle = $count === 1 ? '1 lens/box' : ($count . ' lens/box');
                    $pv->pack_count = $count;
                } elseif (!empty($pv->Packing_Type)) {
                    $pv->pack_label = trim($pv->Packing_Type);
                    $pv->pack_subtitle = strtolower(trim($pv->Packing_Type));
                    $pv->pack_count = 1;
                } else {
                    $pv->pack_label = '1 Lens / Box';
                    $pv->pack_subtitle = '1 lens/box';
                    $pv->pack_count = 1;
                }

                // Pricing
                $pvSellingPrice = (float)($pv->Retail_Price ?? 0);
                $pvMrp = (float)($pv->Purchase_Price ?? 0);
                if ($pvMrp <= $pvSellingPrice && $pvSellingPrice > 0) {
                    $pvMrp = round($pvSellingPrice * 1.35); // 35% markup fallback
                }
                $pv->calc_price = $pvSellingPrice;
                $pv->calc_mrp = $pvMrp;
                $pv->has_discount = ($pvMrp > $pvSellingPrice);
                $pv->is_current = ($pv->id == $product->id);
                $pv->detail_url = url('/product/' . (isset($pv->product_id) && $pv->product_id ? $pv->product_id : $pv->id));

                return $pv;
            })->sortBy('pack_count')->values();
        }

        // Map main image URL
        $product->image_url = getProductImageUrl($product, $product->main_image);
        
        // Map gallery images dynamically across DB and disk folders
        $galleryImages = getProductGalleryImages($product);
        
        // Fetch color variants (siblings sharing same parent_product_code, plus current product)
        $colorVariants = collect();
        if (!empty($product->parent_product_code)) {
            $colorVariants = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->where('parent_product_code', $product->parent_product_code)
                ->get()
                ->map(function($v) use ($ecomStoreId) {
                    $v->image_url   = getProductImageUrl($v, $v->main_image);
                    $v->detail_url  = url('/product/' . (isset($v->product_id) && $v->product_id ? $v->product_id : $v->id));
                    // Color field stores "#hex1" or "#hex1 / #hex2" (primary / secondary)
                    $colorRaw       = $v->Color ?? '';
                    $colorParts     = array_map('trim', explode('/', $colorRaw));
                    $v->color_primary   = $colorParts[0] ?? '#1a1a1a';
                    $v->color_secondary = $colorParts[1] ?? null;
                    // Human-readable label for tooltip
                    $v->color_name  = $colorRaw ?: 'Default';

                    // Resolve live stock for variant from Store 6
                    $vHasLive = DB::table('tbl_inventory_levels')
                        ->where('product_code', $v->product_code)
                        ->where('store_id', $ecomStoreId)
                        ->exists();
                    if ($vHasLive) {
                        $v->stock_quantity = \App\Services\StockSyncService::getLiveStock($v->product_code, $ecomStoreId);
                    } else {
                        $v->stock_quantity = (int)($v->stock_quantity ?? 0);
                    }
                    $v->stock_status   = $v->stock_quantity > 0 ? 'in_stock' : 'out_of_stock';

                    return $v;
                });
        }
        if ($colorVariants->isEmpty()) {
            $product->detail_url = url('/product/' . (isset($product->product_id) && $product->product_id ? $product->product_id : $product->id));
            $colorRaw            = $product->Color ?? '';
            $colorParts          = array_map('trim', explode('/', $colorRaw));
            $product->color_primary   = $colorParts[0] ?? '#1a1a1a';
            $product->color_secondary = $colorParts[1] ?? null;
            $product->color_name = $colorRaw ?: 'Default';
            $colorVariants->push($product);
        }

        // Fetch related products (same type, excluding current)
        $relatedProducts = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->where('product_type', $product->product_type)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get()
            ->map(function ($p) {
                $p->image_url = getProductImageUrl($p, $p->main_image);
                $p->detail_url = url('/product/' . ($p->product_id ?: $p->id));
                return $p;
            });

        $wishlistProductIds = [];
        if (\Auth::check()) {
            $wishlistProductIds = DB::table('wishlists')->where('user_id', \Auth::id())->pluck('product_id')->toArray();
        }

        return view('website.products.details', compact('product', 'categoryName', 'galleryImages', 'colorVariants', 'relatedProducts', 'wishlistProductIds', 'lensPackVariants'));
    }

    public function getSimilarProducts(Request $request, $id)
    {
        $product = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->where(function($query) use ($id) {
                $query->where('product_id', $id)
                      ->orWhere('id', $id)
                      ->orWhere('product_code', $id);
            })
            ->first();

        if (!$product) {
            return response()->json(['status' => 'error', 'message' => 'Product not found'], 404);
        }

        // Query similar products based on category, shape, rim type or company
        $query = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->where('id', '!=', $product->id);

        if (!empty($product->category_id)) {
            $query->where('category_id', $product->category_id);
        }

        // Prioritize matching Shape, Rim_Type, or Company
        $query->where(function($q) use ($product) {
            $hasCondition = false;
            if (!empty($product->Shape)) {
                $q->orWhere('Shape', $product->Shape);
                $hasCondition = true;
            }
            if (!empty($product->Rim_Type)) {
                $q->orWhere('Rim_Type', $product->Rim_Type);
                $hasCondition = true;
            }
            if (!empty($product->Company)) {
                $q->orWhere('Company', $product->Company);
                $hasCondition = true;
            }
            if (!$hasCondition) {
                $q->where('status', 1);
            }
        });

        // Ensure products with images appear first
        $similarProducts = $query
            ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('id', 'desc')
            ->limit(12)
            ->get()
            ->map(function ($p) {
                $p->image_url  = getProductImageUrl($p, $p->main_image);
                $p->detail_url = url('/product/' . ($p->product_id ?: $p->id));
                return $p;
            });

        // Fallback: If not enough similar products found, get latest products from same category or catalog
        if ($similarProducts->count() < 4) {
            $existingIds = $similarProducts->pluck('id')->push($product->id)->toArray();
            $fallback = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->whereNotIn('id', $existingIds)
                ->when(!empty($product->category_id), function($q) use ($product) {
                    return $q->where('category_id', $product->category_id);
                })
                ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
                ->orderBy('id', 'desc')
                ->limit(12 - $similarProducts->count())
                ->get()
                ->map(function ($p) {
                    $p->image_url  = getProductImageUrl($p, $p->main_image);
                    $p->detail_url = url('/product/' . ($p->product_id ?: $p->id));
                    return $p;
                });

            $similarProducts = $similarProducts->concat($fallback);
        }

        // Fetch sibling color variants for similar products
        $parentCodes = $similarProducts->pluck('parent_product_code')->filter()->unique()->toArray();
        $siblingsByParent = [];
        if (!empty($parentCodes)) {
            $allSiblings = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->whereIn('parent_product_code', $parentCodes)
                ->get();

            foreach ($allSiblings as $sib) {
                $sib->image_url  = getProductImageUrl($sib, $sib->main_image);
                $sib->detail_url = url('/product/' . ($sib->product_id ?: $sib->id));
                $siblingsByParent[$sib->parent_product_code][] = $sib;
            }
        }

        $similarProducts = $similarProducts->map(function ($p) use ($siblingsByParent) {
            if (!empty($p->parent_product_code) && isset($siblingsByParent[$p->parent_product_code])) {
                $p->color_variants_list = $siblingsByParent[$p->parent_product_code];
            } else {
                $p->color_variants_list = [];
            }
            return $p;
        });

        $wishlistProductIds = [];
        if (\Auth::check()) {
            $wishlistProductIds = DB::table('wishlists')->where('user_id', \Auth::id())->pluck('product_id')->toArray();
        }

        $productName = $product->product_name ?: $product->product_code;
        $html = view('website.products.similar_modal_grid', compact('similarProducts', 'product', 'wishlistProductIds'))->render();

        return response()->json([
            'status' => 'success',
            'product_name' => $productName,
            'product_brand' => $product->Company ?: 'Speckart',
            'count' => $similarProducts->count(),
            'html' => $html
        ]);
    }

    public function ajaxSearch(Request $request)
    {
        $search = $request->input('search');

        if (empty($search)) {
            // Return trending categories when search is empty
            // Get some active categories
            $categories = Category::where('is_active', true)
                ->whereIn('name', ['Eyeglasses', 'Sunglasses', 'Computer Glasses', 'Contact Lenses'])
                ->get()
                ->map(function ($c) {
                    $imageUrl = asset('website/assets/img/icon/specs-men.png'); // Generic fallback
                    if (!empty($c->image)) {
                        $imageUrl = asset($c->image);
                    }
                    return [
                        'type' => 'category',
                        'name' => $c->name,
                        'url' => route('products', ['category' => $c->slug]),
                        'image_url' => $imageUrl
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $categories
            ]);
        }

        // Return matched products
        $products = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('Company', 'like', "%{$search}%");
            })
            ->limit(6)
            ->get()
            ->map(function ($p) {
                return [
                    'type'      => 'product',
                    'name'      => $p->product_name ?: $p->product_code,
                    'url'       => url('/product/' . ($p->product_id ?: $p->id)),
                    'image_url' => getProductImageUrl($p),
                    'price'     => $p->Retail_Price
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $products
        ]);
    }
}
