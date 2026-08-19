<?php

namespace App\Http\Controllers\Website;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class WebSiteController extends Controller
{
    public function index(){
        
        
        $data = array();
        $data['categories'] = DB::table('categories')->where('is_active','1')->get();
        
        // ── Fetch dynamic banners from offers table ──
        $now = now()->toDateString();

        $offerBanners = DB::table('offers')
            ->where('status', 'active')
            ->where('show_as_banner', 1)
            ->whereNotNull('banner_image')
            ->where('banner_image', '!=', '')
            ->orderBy('id', 'desc')
            ->get();

        // Group offer banners by position
        $positions = ['main_slider' => [], 'promo_1' => [], 'promo_2' => [], 'spotlight' => []];

        foreach ($offerBanners as $o) {
            $pos = $o->banner_position ?? 'main_slider';
            if (isset($positions[$pos])) {
                $positions[$pos][] = (object) [
                    'image'    => $o->banner_image,
                    'link_url' => url('/products?offer=' . $o->id),
                    'title'    => $o->name ?? '',
                ];
            }
        }

        $data['main_slider'] = $positions['main_slider'];
        $data['promo_1']     = $positions['promo_1'];
        $data['promo_2']     = $positions['promo_2'];
        $data['spotlight']   = $positions['spotlight'];

        // ── Fetch dynamic gender data & counts ──
        $genderDefaults = [
            'Men' => [
                'name'  => 'Men',
                'slug'  => 'Men',
                'icon'  => asset('website/assets/img/icon/specs-men.png'),
                'url'   => route('products', ['gender' => 'Men']),
                'count' => 0,
            ],
            'Women' => [
                'name'  => 'Women',
                'slug'  => 'Women',
                'icon'  => asset('website/assets/img/icon/specs-women.png'),
                'url'   => route('products', ['gender' => 'Women']),
                'count' => 0,
            ],
            'Kids' => [
                'name'  => 'Kids',
                'slug'  => 'Kids',
                'icon'  => asset('website/assets/img/icon/specs-kid.png'),
                'url'   => route('products', ['gender' => 'Kids']),
                'count' => 0,
            ],
        ];

        try {
            $genderRows = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->whereNotNull('Gender')
                ->where('Gender', '!=', '')
                ->pluck('Gender');

            foreach ($genderRows as $gVal) {
                if (empty($gVal)) continue;
                if (stripos($gVal, 'men') !== false && stripos($gVal, 'women') === false) {
                    $genderDefaults['Men']['count']++;
                }
                if (stripos($gVal, 'women') !== false) {
                    $genderDefaults['Women']['count']++;
                }
                if (stripos($gVal, 'kid') !== false || stripos($gVal, 'child') !== false) {
                    $genderDefaults['Kids']['count']++;
                }
                if (stripos($gVal, 'unisex') !== false) {
                    $genderDefaults['Men']['count']++;
                    $genderDefaults['Women']['count']++;
                }
            }
        } catch (\Throwable $e) {
            // DB fallback
        }

        $data['genders'] = array_values($genderDefaults);

        // ── Fetch dynamic product data (only B2C products) ──
        // 1. Trending
        $trendingList = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->whereRaw("FIND_IN_SET('trending', promotion_tag)")
            ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('id', 'desc')
            ->select('tbl_product_code.*')
            ->limit(8)
            ->get();

        if ($trendingList->isEmpty()) {
            $trendingList = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();
        }

        // 2. Best Seller
        $bestSellerList = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->whereRaw("FIND_IN_SET('best_seller', promotion_tag)")
            ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('id', 'desc')
            ->select('tbl_product_code.*')
            ->limit(8)
            ->get();

        if ($bestSellerList->isEmpty()) {
            $bestSellerList = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();
        }

        // 3. New Arrivals (tagged with new_arrival, fallback to latest created B2C products)
        $newArrivalsList = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->whereRaw("FIND_IN_SET('new_arrival', promotion_tag)")
            ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();

        if ($newArrivalsList->isEmpty()) {
            $newArrivalsList = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(8)
                ->get();
        }

        // 4. Own Creation (custom creations)
        $ownCreationsList = DB::table('tbl_product_code')
            ->where('status', 1)
            ->where('is_b2c', 1)
            ->orderByRaw('CASE WHEN main_image IS NOT NULL AND main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('id', 'asc')
            ->limit(8)
            ->get();

        // 5. Sunglasses
        $sunglassesList = DB::table('tbl_product_code')
            ->join('categories', 'categories.id', '=', 'tbl_product_code.category_id')
            ->where('tbl_product_code.status', 1)
            ->where('tbl_product_code.is_b2c', 1)
            ->where('categories.slug', 'sunglasses')
            ->orderByRaw('CASE WHEN tbl_product_code.main_image IS NOT NULL AND tbl_product_code.main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('tbl_product_code.id', 'desc')
            ->select('tbl_product_code.*')
            ->limit(8)
            ->get();

        // 6. Eyeglasses
        $eyeglassesList = DB::table('tbl_product_code')
            ->join('categories', 'categories.id', '=', 'tbl_product_code.category_id')
            ->where('tbl_product_code.status', 1)
            ->where('tbl_product_code.is_b2c', 1)
            ->where('categories.slug', 'eyeglasses')
            ->orderByRaw('CASE WHEN tbl_product_code.main_image IS NOT NULL AND tbl_product_code.main_image != "" THEN 0 ELSE 1 END')
            ->orderBy('tbl_product_code.id', 'desc')
            ->select('tbl_product_code.*')
            ->limit(8)
            ->get();

        // Map helper for images and URLs
        $mapProducts = function ($products, $defaultFallback) {
            return collect($products)->map(function ($p) use ($defaultFallback) {
                $typeLower = strtolower($p->product_type ?: 'frame');
                $p->image_url = asset($defaultFallback);
                
                if ($p->main_image) {
                    if ($p->parent_product_code) {
                        $path = "uploads/{$typeLower}/product/{$p->parent_product_code}/{$p->main_image}";
                        if (file_exists(public_path($path))) {
                            $p->image_url = asset($path);
                        }
                    } else {
                        // Fallback 1: with product_id
                        $pathWithId = "uploads/{$typeLower}/product/{$p->product_id}/{$p->main_image}";
                        if (file_exists(public_path($pathWithId))) {
                            $p->image_url = asset($pathWithId);
                        } else if (file_exists(public_path($p->main_image))) {
                            $p->image_url = asset($p->main_image);
                        }
                    }
                }
                
                $p->detail_url = url('/product/' . ($p->product_id ?: $p->id));
                return $p;
            });
        };

        $data['trending_products'] = $mapProducts($trendingList, 'website/assets/img/bg/Sunglasses1.png');
        $data['best_sellers']      = $mapProducts($bestSellerList, 'website/assets/img/bg/Eyeglasses1.png');
        $data['sunglasses']        = $mapProducts($sunglassesList, 'website/assets/img/bg/Sunglasses1.png');
        $data['eyeglasses']        = $mapProducts($eyeglassesList, 'website/assets/img/bg/Eyeglasses1.png');
        $data['new_arrivals']      = $mapProducts($newArrivalsList, 'website/assets/img/bg/Sunglasses1.png');
        $data['own_creations']     = $mapProducts($ownCreationsList, 'website/assets/img/bg/Creation1.png');

        // ── Fetch dynamic brands for homepage slider ──
        $defaultBrandBgs = [
            asset('website/assets/img/bg/brands1.png'),
            asset('website/assets/img/bg/brands2.png'),
            asset('website/assets/img/bg/brands3.png'),
            asset('website/assets/img/bg/brands4.png'),
        ];
        $defaultBrandLogos = [
            asset('website/assets/img/bg/brand-sm1.png'),
            asset('website/assets/img/bg/brand-sm2.png'),
            asset('website/assets/img/bg/brand-sm3.png'),
            asset('website/assets/img/bg/brand-sm4.png'),
        ];

        $rawBrands = DB::table('tbl_brand')
            ->where('status', '1')
            ->orderBy('brand_id', 'desc')
            ->get();

        $brands = collect($rawBrands)->map(function ($b, $idx) use ($defaultBrandBgs, $defaultBrandLogos) {
            // 1. Logo image
            $logoUrl = null;
            if (!empty($b->image) && file_exists(public_path($b->image))) {
                $logoUrl = asset($b->image);
            } elseif (!empty($b->image)) {
                $logoUrl = asset($b->image);
            } else {
                $logoUrl = $defaultBrandLogos[$idx % count($defaultBrandLogos)];
            }

            // 2. Fetch top active B2C product image as lifestyle/product background
            $topProduct = DB::table('tbl_product_code')
                ->where('status', 1)
                ->where('is_b2c', 1)
                ->where('Company', $b->brand_name)
                ->whereNotNull('main_image')
                ->where('main_image', '!=', '')
                ->orderBy('id', 'desc')
                ->first();

            $bgImage = $defaultBrandBgs[$idx % count($defaultBrandBgs)];
            if ($topProduct && !empty($topProduct->main_image)) {
                $typeLower = strtolower($topProduct->product_type ?: 'frame');
                if (!empty($topProduct->parent_product_code)) {
                    $path = "uploads/{$typeLower}/product/{$topProduct->parent_product_code}/{$topProduct->main_image}";
                    if (file_exists(public_path($path))) {
                        $bgImage = asset($path);
                    }
                } else {
                    $pathWithId = "uploads/{$typeLower}/product/{$topProduct->product_id}/{$topProduct->main_image}";
                    if (file_exists(public_path($pathWithId))) {
                        $bgImage = asset($pathWithId);
                    } elseif (file_exists(public_path($topProduct->main_image))) {
                        $bgImage = asset($topProduct->main_image);
                    }
                }
            }

            $b->name         = $b->brand_name;
            $b->url          = route('products', ['brand' => $b->brand_name]);
            $b->catalog_url  = $b->url;
            $b->logo_img     = $logoUrl;
            $b->logo_url     = $logoUrl;
            $b->bg_image_url = $bgImage;
            $cleanName       = preg_replace('/[^a-zA-Z0-9]/', '', $b->brand_name ?: 'BR');
            $b->initials     = strtoupper(substr($cleanName, 0, 2)) ?: 'BR';

            return $b;
        });

        $data['brands'] = $brands;

        return view('website.index',$data);
    }

    public function membership()
    {
        
        $membershipCards = DB::table('tbl_membership_card')
              ->where('flag', 0)
            ->get();

        return view('website.membership', compact('membershipCards'));
    }
}

