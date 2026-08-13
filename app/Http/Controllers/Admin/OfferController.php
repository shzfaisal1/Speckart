<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Offer;
use App\Models\product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class OfferController extends Controller
{
    /**
     * Offer list page (DataTable).
     */
    public function index()
    {
        $setting['page_title'] = 'Offers';
        return view('admin.offers.index', $setting);
    }

    /**
     * DataTable AJAX source for offers list.
     */
    public function data()
    {
        $offers = Offer::latest();

        $stats = [
            'total' => Offer::count(),
            'active' => Offer::active()->count(),
            'draft' => Offer::where('status', 'draft')->count(),
            'inactive' => Offer::where('status', 'inactive')->count(),
        ];

        return DataTables::of($offers)
            ->addColumn('offer_name', function ($row) {
                return '<strong>' . e($row->name) . '</strong>';
            })
            ->addColumn('discount_display', function ($row) {
                if ($row->discount_type === 'fixed') {
                    return '<span class="badge badge-success">FLAT ₹' . number_format($row->discount_value, 0) . '</span>';
                }
                return '<span class="badge badge-info">' . rtrim(rtrim(number_format($row->discount_value, 2), '0'), '.') . '% OFF</span>';
            })
            ->addColumn('coupon', function ($row) {
                return $row->coupon_code ? '<code>' . e(strtoupper($row->coupon_code)) . '</code>' : '<span class="text-muted">—</span>';
            })
            ->addColumn('validity', function ($row) {
                if (!$row->start_date && !$row->end_date) {
                    return '<span class="text-muted fst-italic">No dates set</span>';
                }
                $from  = $row->start_date  ? $row->start_date->format('d M Y')  : '—';
                $until = $row->end_date    ? $row->end_date->format('d M Y')    : '—';
                return $from . ' → ' . $until;
            })
            ->addColumn('apply_info', function ($row) {
                $category_count = is_array($row->category_ids) ? count($row->category_ids) : 0;
                $brand_count = is_array($row->brand_ids) ? count($row->brand_ids) : 0;
                $product_count = is_array($row->product_ids) ? count($row->product_ids) : 0;

                $labels = [
                    'all_products'      => '<span class="badge badge-primary">All Products</span>',
                    'specific_category' => '<span class="badge badge-warning">Categories: ' . $category_count . '</span>',
                    'specific_brand'    => '<span class="badge badge-warning">Brands: ' . $brand_count . '</span>',
                    'specific_products' => '<span class="badge badge-warning">Products: ' . $product_count . '</span>',
                ];
                return $labels[$row->apply_on] ?? $row->apply_on;
            })
            ->addColumn('banner_info', function ($row) {
                if (!$row->show_as_banner) {
                    return '<span class="text-muted">—</span>';
                }
                $posLabels = [
                    'main_slider' => 'Slider',
                    'promo_1'     => 'Promo 1',
                    'promo_2'     => 'Promo 2',
                    'spotlight'   => 'Spotlight',
                ];
                $posLabel = $posLabels[$row->banner_position] ?? '—';
                return '<span class="badge badge-success"><i class="fa fa-image"></i> ' . $posLabel . '</span>';
            })
            ->addColumn('status_display', function ($row) {
                $checked = ($row->status === 'active') ? 'checked' : '';
                return '
                    <label class="custom-switch" style="cursor: pointer; display: inline-flex; align-items: center; margin: 0;">
                        <input type="checkbox" class="custom-switch-input toggle-offer-status" 
                               data-id="' . $row->id . '" ' . $checked . '>
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description font-weight-semibold text-muted" 
                              style="font-size: 11px; text-transform: uppercase; margin-left: 8px; vertical-align: middle;">
                            ' . ucfirst($row->status) . '
                        </span>
                    </label>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-info btn-view-offer"
                                data-id="' . $row->id . '" title="Preview Offer">
                            <i class="fa fa-eye"></i>
                        </button>
                        <a href="' . url(config('app.admin_path') . '/offers/' . $row->id . '/edit') . '" class="btn btn-sm btn-success" title="Edit Offer">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete-offer"
                                data-id="' . $row->id . '" title="Delete Offer">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['offer_name', 'discount_display', 'coupon', 'validity', 'apply_info', 'banner_info', 'status_display', 'action'])
            ->with('stats', $stats)
            ->make(true);
    }

    /**
     * Show the "Create Offer" multi-step wizard form.
     */
    public function create()
    {
        $setting['page_title']   = 'Create Offer';
        $setting['categories']   = Category::where('is_active', 1)->orderBy('name')->get();
        $setting['brands']       = Brand::where('status', 1)->orderBy('brand_name')->get();
        $setting['memberships']  = DB::table('tbl_membership_card')->where('flag', 0)->orderBy('card_name')->get();
        $setting['all_products'] = Product::where('status', 1)->orderBy('product_name')->limit(200)->get(['id', 'product_name', 'product_code', 'Retail_Price']);
        $setting['breadcrumbs']  = [
            ['name' => 'Dashboard', 'link' => '/'],
            ['name' => 'Offers', 'link' => url(config('app.admin_path') . '/offers')],
            ['name' => 'Create Offer', 'link' => 'javascript:void(0)'],
        ];

        return view('admin.offers.create', $setting);
    }

    /**
     * Persist a new offer to the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'offer_type'             => 'required|in:percentage_discount,flat_discount,buy1get1,gift_voucher',
            'discount_type'          => 'nullable|in:percentage,fixed',
            'discount_value'         => 'nullable|numeric|min:0',
            'coupon_code'            => 'nullable|string|max:50|unique:offers,coupon_code',
            'description'            => 'nullable|string|max:1000',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date|after_or_equal:start_date',
            'min_cart_amount'        => 'nullable|numeric|min:0',
            'max_discount'           => 'nullable|numeric|min:0',
            'user_type'              => 'required|string|max:255',
            'usage_limit'            => 'nullable|integer|min:1',
            'usage_limit_per_user'   => 'nullable|integer|min:1',
            'apply_on'               => 'required|in:all_products,specific_category,specific_brand,specific_products',
            'status'                 => 'required|in:active,inactive,draft',
            'categories'             => 'nullable|array',
            'categories.*'           => 'exists:categories,id',
            'brands'                 => 'nullable|array',
            'brands.*'               => 'exists:tbl_brand,brand_id',
            'products'               => 'nullable|array',
            'products.*'             => 'exists:tbl_product_code,id',
            'show_as_banner'         => 'nullable|boolean',
            'banner_position'        => 'nullable|string|max:100',
            'banner_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // BOGO fields
            'bogo_buy_qty'           => 'nullable|integer|min:1',
            'bogo_get_qty'           => 'nullable|integer|min:1',
            'bogo_free_discount'     => 'nullable|numeric|min:1|max:100',
            'bogo_extra_enabled'     => 'nullable|boolean',
            'bogo_extra_discount'    => 'nullable|numeric|min:1|max:99',
            // Voucher fields
            'voucher_value'          => 'nullable|numeric|min:1',
            'voucher_validity_days'  => 'nullable|integer|min:1',
            // Membership Bundle fields
            'linked_product_id'      => 'nullable|integer',
            'membership_mrp'         => 'nullable|numeric|min:0',
            'membership_sale_price'  => 'nullable|numeric|min:0',
            'entitlement_type'       => 'nullable|string|max:100',
            'entitlement_scope'      => 'nullable|string|max:255',
            'cashback_percent'       => 'nullable|numeric|min:0|max:100',
            'cashback_delay_days'    => 'nullable|integer|min:0',
            'stack_with_coupons'     => 'nullable|boolean',
        ]);

        // Clean coupon code
        if (!empty($validated['coupon_code'])) {
            $validated['coupon_code'] = strtoupper($validated['coupon_code']);
        }

        $validated['added_by'] = Auth::id();
        $validated['store_id'] = Auth::user()->store_id ?? null;

        // Set arrays based on apply_on type
        $validated['category_ids'] = ($request->apply_on === 'specific_category' && $request->has('categories')) ? $request->input('categories') : null;
        $validated['brand_ids']    = ($request->apply_on === 'specific_brand'    && $request->has('brands'))     ? $request->input('brands')     : null;
        $validated['product_ids']  = ($request->apply_on === 'specific_products' && $request->has('products'))   ? $request->input('products')   : null;

        $validated['show_as_banner']      = $request->boolean('show_as_banner', false);
        $validated['bogo_extra_enabled']  = $request->boolean('bogo_extra_enabled', false);
        $validated['stack_with_coupons']  = $request->boolean('stack_with_coupons', true);

        // Clear bonus tier discount if toggle is off
        if (!$validated['bogo_extra_enabled']) {
            $validated['bogo_extra_discount'] = null;
        }

        // Set fallbacks for discount_value & discount_type for Gift Vouchers & BOGO
        if ($validated['offer_type'] === 'gift_voucher' && !empty($validated['voucher_value'])) {
            $validated['discount_type']  = $validated['discount_type'] ?? 'fixed';
            $validated['discount_value'] = $validated['discount_value'] ?? (float) $validated['voucher_value'];
        } else {
            $validated['discount_value'] = $validated['discount_value'] ?? 0;
            $validated['discount_type']  = $validated['discount_type'] ?? 'percentage';
        }

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->banner_image->extension();
            $request->banner_image->move(public_path('uploads/banners'), $imageName);
            $validated['banner_image'] = 'uploads/banners/' . $imageName;
        }

        $offer = Offer::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Offer <strong>' . e($offer->name) . '</strong> created successfully.',
            'data'    => $offer,
        ]);
    }

    /**
     * Show the edit form for a specific offer.
     */
    public function edit($id)
    {
        $offer = Offer::findOrFail($id);

        $setting['page_title']   = 'Edit Offer: ' . $offer->name;
        $setting['categories']   = Category::where('is_active', 1)->orderBy('name')->get();
        $setting['brands']       = Brand::where('status', 1)->orderBy('brand_name')->get();
        $setting['all_products'] = Product::where('status', 1)->orderBy('product_name')->limit(200)->get(['id', 'product_name', 'product_code', 'Retail_Price']);
        $setting['offer']        = $offer;

        $setting['selected_products'] = [];
        if ($offer->apply_on === 'specific_products' && is_array($offer->product_ids)) {
            $setting['selected_products'] = Product::whereIn('id', $offer->product_ids)
                ->where('status', 1)
                ->get([
                    'id',
                    'product_name',
                    'product_code',
                    'Company',
                    'product_type',
                    'Retail_Price',
                    'product_image'
                ]);
        }

        $setting['memberships'] = DB::table('tbl_membership_card')->where('flag', 0)->orderBy('card_name')->get();

        $setting['breadcrumbs'] = [
            ['name' => 'Dashboard', 'link' => '/'],
            ['name' => 'Offers', 'link' => url(config('app.admin_path') . '/offers')],
            ['name' => 'Edit Offer', 'link' => 'javascript:void(0)'],
        ];

        return view('admin.offers.create', $setting);
    }

    /**
     * Update an existing offer.
     */
    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'name'                   => 'required|string|max:255',
            'offer_type'             => 'required|in:percentage_discount,flat_discount,buy1get1,gift_voucher',
            'discount_type'          => 'nullable|in:percentage,fixed',
            'discount_value'         => 'nullable|numeric|min:0',
            'coupon_code'            => 'nullable|string|max:50|unique:offers,coupon_code,' . $id,
            'description'            => 'nullable|string|max:1000',
            'start_date'             => 'nullable|date',
            'end_date'               => 'nullable|date|after_or_equal:start_date',
            'min_cart_amount'        => 'nullable|numeric|min:0',
            'max_discount'           => 'nullable|numeric|min:0',
            'user_type'              => 'required|string|max:255',
            'usage_limit'            => 'nullable|integer|min:1',
            'usage_limit_per_user'   => 'nullable|integer|min:1',
            'apply_on'               => 'required|in:all_products,specific_category,specific_brand,specific_products',
            'status'                 => 'required|in:active,inactive,draft',
            'categories'             => 'nullable|array',
            'categories.*'           => 'exists:categories,id',
            'brands'                 => 'nullable|array',
            'brands.*'               => 'exists:tbl_brand,brand_id',
            'products'               => 'nullable|array',
            'products.*'             => 'exists:tbl_product_code,id',
            'show_as_banner'         => 'nullable|boolean',
            'banner_position'        => 'nullable|string|max:100',
            'banner_image'           => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // BOGO fields
            'bogo_buy_qty'           => 'nullable|integer|min:1',
            'bogo_get_qty'           => 'nullable|integer|min:1',
            'bogo_free_discount'     => 'nullable|numeric|min:1|max:100',
            'bogo_extra_enabled'     => 'nullable|boolean',
            'bogo_extra_discount'    => 'nullable|numeric|min:1|max:99',
            // Voucher fields
            'voucher_value'          => 'nullable|numeric|min:1',
            'voucher_validity_days'  => 'nullable|integer|min:1',
            // Membership Bundle fields
            'linked_product_id'      => 'nullable|integer',
            'membership_mrp'         => 'nullable|numeric|min:0',
            'membership_sale_price'  => 'nullable|numeric|min:0',
            'entitlement_type'       => 'nullable|string|max:100',
            'entitlement_scope'      => 'nullable|string|max:255',
            'cashback_percent'       => 'nullable|numeric|min:0|max:100',
            'cashback_delay_days'    => 'nullable|integer|min:0',
            'stack_with_coupons'     => 'nullable|boolean',
        ]);

        // Clean coupon code
        if (!empty($validated['coupon_code'])) {
            $validated['coupon_code'] = strtoupper($validated['coupon_code']);
        }

        // Set arrays based on apply_on type
        $validated['category_ids'] = ($request->apply_on === 'specific_category' && $request->has('categories')) ? $request->input('categories') : null;
        $validated['brand_ids']    = ($request->apply_on === 'specific_brand'    && $request->has('brands'))     ? $request->input('brands')     : null;
        $validated['product_ids']  = ($request->apply_on === 'specific_products' && $request->has('products'))   ? $request->input('products')   : null;

        $validated['show_as_banner']     = $request->boolean('show_as_banner', false);
        $validated['bogo_extra_enabled'] = $request->boolean('bogo_extra_enabled', false);
        $validated['stack_with_coupons']  = $request->boolean('stack_with_coupons', true);

        // Clear bonus tier discount if toggle is off
        if (!$validated['bogo_extra_enabled']) {
            $validated['bogo_extra_discount'] = null;
        }

        // Set fallbacks for discount_value & discount_type for Gift Vouchers & BOGO
        if ($validated['offer_type'] === 'gift_voucher' && !empty($validated['voucher_value'])) {
            $validated['discount_type']  = $validated['discount_type'] ?? 'fixed';
            $validated['discount_value'] = $validated['discount_value'] ?? (float) $validated['voucher_value'];
        } else {
            $validated['discount_value'] = $validated['discount_value'] ?? 0;
            $validated['discount_type']  = $validated['discount_type'] ?? 'percentage';
        }

        // Handle banner image upload
        if ($request->hasFile('banner_image')) {
            if ($offer->banner_image && file_exists(public_path($offer->banner_image))) {
                @unlink(public_path($offer->banner_image));
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->banner_image->extension();
            $request->banner_image->move(public_path('uploads/banners'), $imageName);
            $validated['banner_image'] = 'uploads/banners/' . $imageName;
        }

        $offer->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Offer <strong>' . e($offer->name) . '</strong> updated successfully.',
            'data'    => $offer,
        ]);
    }

    /**
     * Get offer details for preview.
     */
    public function show($id)
    {
        $offer = Offer::findOrFail($id);

        $categories = [];
        if (is_array($offer->category_ids) && count($offer->category_ids) > 0) {
            $categories = Category::whereIn('id', $offer->category_ids)->pluck('name')->toArray();
        }

        $brands = [];
        if (is_array($offer->brand_ids) && count($offer->brand_ids) > 0) {
            $brands = Brand::whereIn('brand_id', $offer->brand_ids)->pluck('brand_name')->toArray();
        }

        $products = [];
        if (is_array($offer->product_ids) && count($offer->product_ids) > 0) {
            $products = Product::whereIn('id', $offer->product_ids)
                ->where('status', 1)
                ->get(['product_name', 'product_code'])
                ->map(function ($p) {
                    return $p->product_name . ' (' . $p->product_code . ')';
                })
                ->toArray();
        }

        $userTypeLabel = $offer->user_type;
        if (is_numeric($offer->user_type)) {
            $membership = DB::table('tbl_membership_card')->where('id', $offer->user_type)->first();
            if ($membership) {
                $userTypeLabel = $membership->card_name;
            }
        } else {
            $userTypes = [
                'all'      => 'All Users',
                'new'      => 'New Users',
                'existing' => 'Existing Users'
            ];
            $userTypeLabel = $userTypes[$offer->user_type] ?? $offer->user_type;
        }

        $bannerImageUrl = $offer->banner_image ? asset($offer->banner_image) : null;

        return response()->json([
            'success' => true,
            'offer'   => $offer,
            'details' => [
                'categories'       => $categories,
                'brands'           => $brands,
                'products'         => $products,
                'user_type_label'  => $userTypeLabel,
                'banner_image_url' => $bannerImageUrl,
            ]
        ]);
    }

    /**
     * AJAX: toggle status between active and inactive.
     */
    public function toggleStatus($id)
    {
        $offer = Offer::findOrFail($id);
        
        $newStatus = ($offer->status === 'active') ? 'inactive' : 'active';
        $offer->status = $newStatus;
        $offer->save();

        return response()->json([
            'success' => true,
            'message' => 'Offer status updated to <strong>' . ucfirst($newStatus) . '</strong>.',
            'status'  => $newStatus,
        ]);
    }

    /**
     * Delete an offer.
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $offer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Offer deleted successfully.',
        ]);
    }

    /**
     * AJAX: search products for the "Specific Products" table.
     */
    public function searchProducts(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('Company', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->select(
            'id',
            'product_name',
            'product_code',
            'Company',
            'product_type',
            'Retail_Price',
            'product_image'
        )
        ->where('status', 1)
        ->orderBy('product_name')
        ->limit(50)
        ->get();

        return response()->json($products);
    }

    /**
     * AJAX: return brands for dropdown.
     */
    public function getBrands()
    {
        $brands = Brand::where('status', 1)->orderBy('brand_name')->get(['brand_id as id', 'brand_name as name']);
        return response()->json($brands);
    }

    /**
     * AJAX: return categories for dropdown.
     */
    public function getCategories()
    {
        $categories = Category::where('is_active', 1)->orderBy('name')->get(['id', 'name']);
        return response()->json($categories);
    }
}
