<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    public function boot()
    {
        $host = request()->getHost(); // get current host like admin.apnashyam.com
        $uri = request()->path();     // get the URI path like 'quickdaak/...'
        $subdomain = explode('.',  request()->getHost())[0];
        Route::middleware('web')->group(base_path('routes/auth.php'));
        $isLocal = ($host === '127.0.0.1' || $host === 'localhost');
        if ($subdomain === 'franchise' || $subdomain === 'speckart' || $isLocal) {
            $vendorRoute = Route::middleware('web');
            $adminRoute = Route::middleware('web');

            if (!$isLocal) {
                Route::middleware('api')->domain(config('app.vendor_domain'))->prefix('api')->group(base_path('routes/api.php'));
                $vendorRoute = $vendorRoute->domain(config('app.vendor_domain'));
                $adminRoute = $adminRoute->domain(config('app.admin_domain'));
                $vendorRoute->group(base_path('routes/client.php'));
            } else {
                // Commented out on local to prevent crashes from missing API and Client controllers
                // Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
            }

            $adminRoute->group(base_path('routes/web.php'));
            $adminRoute->group(base_path('routes/purchases.php'));
            $adminRoute->group(base_path('routes/setting.php'));
            $adminRoute->group(base_path('routes/inventory.php'));
            $adminRoute->group(base_path('routes/customer.php'));
            $adminRoute->group(base_path('routes/sales.php'));
            $adminRoute->group(base_path('routes/account.php'));

            foreach (glob(base_path('routes/admin*.php')) as $routeFile) {
                $adminRoute->group($routeFile);
            }
        }

        // ── Share dynamic nav menu data with all website views ──
        \Illuminate\Support\Facades\View::composer('website.*', function ($view) {
            try {
                $navData = \Illuminate\Support\Facades\Cache::remember('website_dynamic_navbar_data', 1800, function () {
                    // Fallback icon pool for brands without images
                    $defaultIconPool = [
                        'website/assets/img/icon/specs1.png',
                        'website/assets/img/icon/specs2.png',
                        'website/assets/img/icon/specs3.png',
                        'website/assets/img/icon/specs4.png',
                        'website/assets/img/icon/specs5.png',
                        'website/assets/img/icon/specs6.png',
                        'website/assets/img/icon/specs7.png',
                    ];

                    // 1. Nav Brands — active from tbl_brand complemented by top brands from tbl_product_code
                    $activeTblBrands = \Illuminate\Support\Facades\DB::table('tbl_brand')
                        ->where('status', '1')
                        ->whereNotNull('brand_name')
                        ->where('brand_name', '!=', '')
                        ->get(['brand_name', 'image'])
                        ->keyBy(fn($b) => strtolower(trim($b->brand_name)));

                    $productBrands = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                        ->where('status', 1)
                        ->whereNotNull('Company')
                        ->where('Company', '!=', '')
                        ->select('Company', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                        ->groupBy('Company')
                        ->orderByDesc('count')
                        ->limit(15)
                        ->pluck('Company');

                    $mergedBrands = collect();
                    foreach ($activeTblBrands as $lower => $b) {
                        $mergedBrands->put($lower, (object)[
                            'brand_name' => $b->brand_name,
                            'image'      => $b->image,
                        ]);
                    }
                    foreach ($productBrands as $pBrand) {
                        $lower = strtolower(trim($pBrand));
                        if (!$mergedBrands->has($lower)) {
                            $mergedBrands->put($lower, (object)[
                                'brand_name' => $pBrand,
                                'image'      => null,
                            ]);
                        }
                    }

                    $navBrands = $mergedBrands->values()->take(9)->map(function ($b, $idx) use ($defaultIconPool) {
                        $iconUrl = (!empty($b->image))
                            ? asset($b->image)
                            : asset($defaultIconPool[$idx % count($defaultIconPool)]);

                        $minPrice = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                            ->where('status', 1)
                            ->where('Company', $b->brand_name)
                            ->where('Retail_Price', '>=', 400)
                            ->min('Retail_Price');

                        return (object) [
                            'name'      => $b->brand_name,
                            'icon_url'  => $iconUrl,
                            'min_price' => $minPrice ? '₹' . number_format($minPrice) : '₹499',
                            'url'       => route('products', ['brand' => $b->brand_name]),
                        ];
                    });

                    // 2. Nav Categories — active categories from DB
                    $navCategories = \Illuminate\Support\Facades\DB::table('categories')
                        ->where('is_active', 1)
                        ->whereNull('deleted_at')
                        ->orderBy('id', 'asc')
                        ->get()
                        ->map(function ($c) {
                            return (object) [
                                'id'    => $c->id,
                                'name'  => $c->name,
                                'slug'  => $c->slug,
                                'image' => !empty($c->image) ? asset($c->image) : null,
                                'url'   => route('products', ['category' => $c->slug]),
                            ];
                        });

                    // 3. Frame Types — with per-type min price from DB
                    $frameTypeMap = [
                        'Full-Rim' => ['icon' => 'website/assets/img/icon/specs4.png', 'fallback' => 500],
                        'Half-Rim' => ['icon' => 'website/assets/img/icon/Half-Rim.png', 'fallback' => 800],
                        'Rimless'  => ['icon' => 'website/assets/img/icon/Rimless.png', 'fallback' => 1200],
                    ];
                    $navFrameTypes = collect($frameTypeMap)->map(function ($data, $ft) {
                        $minPrice = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                            ->where('status', 1)
                            ->where('Type', 'LIKE', "%{$ft}%")
                            ->where('Retail_Price', '>', 50)
                            ->min('Retail_Price');
                        return (object) [
                            'label'     => $ft . ' Frames',
                            'param'     => $ft,
                            'icon_url'  => asset($data['icon']),
                            'min_price' => '₹' . number_format($minPrice ?: $data['fallback']),
                            'url'       => route('products', ['category' => 'eyeglasses', 'frame_type' => $ft]),
                        ];
                    })->values();

                    // 4. Shapes — with per-shape min price from DB
                    $shapeMap = [
                        'Round'     => ['icon' => 'website/assets/img/icon/specs6.png', 'fallback' => 799],
                        'Rectangle' => ['icon' => 'website/assets/img/icon/specs5.png', 'fallback' => 699],
                        'Wayfarer'  => ['icon' => 'website/assets/img/icon/specs3.png', 'fallback' => 899],
                        'Cat Eye'   => ['icon' => 'website/assets/img/icon/specs7.png', 'fallback' => 999],
                        'Oval'      => ['icon' => 'website/assets/img/icon/specs2.png', 'fallback' => 750],
                        'Square'    => ['icon' => 'website/assets/img/icon/specs1.png', 'fallback' => 650],
                        'Aviator'   => ['icon' => 'website/assets/img/bg/Sunglasses3.png', 'fallback' => 999],
                    ];
                    $navShapes = collect($shapeMap)->map(function ($data, $sh) {
                        $minPrice = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                            ->where('status', 1)
                            ->where('Shape', 'LIKE', "%{$sh}%")
                            ->where('Retail_Price', '>', 50)
                            ->min('Retail_Price');
                        return (object) [
                            'label'     => $sh . ' Frames',
                            'param'     => $sh,
                            'icon_url'  => asset($data['icon']),
                            'min_price' => '₹' . number_format($minPrice ?: $data['fallback']),
                            'url'       => route('products', ['category' => 'eyeglasses', 'shape' => $sh]),
                        ];
                    })->values();

                    // 5. Sunglasses Styles
                    $sunStyleMap = [
                        ['label' => 'Aviators & Navigators',   'shape' => 'Aviator',   'icon' => 'website/assets/img/bg/Sunglasses3.png', 'fallback' => 1200],
                        ['label' => 'Wayfarers & Classics',     'shape' => 'Wayfarer',  'icon' => 'website/assets/img/bg/Sunglasses4.png', 'fallback' => 999],
                        ['label' => 'Round & Hexagonal Sun',    'shape' => 'Round',     'icon' => 'website/assets/img/bg/Sunglasses1.png', 'fallback' => 1100],
                        ['label' => 'Cat-Eye & Chic Styles',    'shape' => 'Cat Eye',   'icon' => 'website/assets/img/icon/specs7.png',    'fallback' => 1400],
                        ['label' => 'Polarized & Sport Sun',    'shape' => 'Rectangle', 'icon' => 'website/assets/img/bg/Sunglasses2.png', 'fallback' => 1500],
                        ['label' => 'Clubmaster & Retro',       'shape' => 'Square',    'icon' => 'website/assets/img/icon/specs2.png',    'fallback' => 1350],
                    ];
                    $navSunStyles = collect($sunStyleMap)->map(function ($item) {
                        $minPrice = \Illuminate\Support\Facades\DB::table('tbl_product_code')
                            ->where('status', 1)
                            ->where('Shape', 'LIKE', "%{$item['shape']}%")
                            ->where('Retail_Price', '>', 50)
                            ->min('Retail_Price');
                        return (object) [
                            'label'     => $item['label'],
                            'shape'     => $item['shape'],
                            'icon_url'  => asset($item['icon']),
                            'min_price' => '₹' . number_format($minPrice ?: $item['fallback']),
                            'url'       => route('products', ['category' => 'sunglasses', 'shape' => $item['shape']]),
                        ];
                    });

                    // 6. Contact Lens Modalities
                    $navContactTypes = collect([
                        (object) ['label' => 'Daily Disposable',     'modality' => 'Daily',   'min_price' => '₹750',  'url' => route('products', ['type' => 'Contact Lens', 'modality' => 'Daily'])],
                        (object) ['label' => 'Monthly Disposable',   'modality' => 'Monthly', 'min_price' => '₹950',  'url' => route('products', ['type' => 'Contact Lens', 'modality' => 'Monthly'])],
                        (object) ['label' => 'Color Contact Lenses', 'modality' => 'Color',   'min_price' => '₹599',  'url' => route('products', ['type' => 'Contact Lens', 'color' => 'Green'])],
                        (object) ['label' => 'Toric Astigmatism',    'modality' => 'Toric',   'min_price' => '₹1200', 'url' => route('products', ['type' => 'Contact Lens'])],
                    ]);

                    return [
                        'navBrands'        => $navBrands,
                        'navCategories'    => $navCategories,
                        'navFrameTypes'    => $navFrameTypes,
                        'navShapes'        => $navShapes,
                        'navSunStyles'     => $navSunStyles,
                        'navContactTypes'  => $navContactTypes,
                    ];
                });

                $view->with($navData);
            } catch (\Throwable $e) {
                // Graceful fallback — never break the page if DB is unavailable
                $view->with([
                    'navBrands'       => collect(),
                    'navCategories'   => collect(),
                    'navFrameTypes'   => collect(),
                    'navShapes'       => collect(),
                    'navSunStyles'    => collect(),
                    'navContactTypes' => collect(),
                ]);
            }
        });
    }
}
