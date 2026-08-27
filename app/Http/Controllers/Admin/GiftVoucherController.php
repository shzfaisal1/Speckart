<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftVoucher;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class GiftVoucherController extends Controller
{
    /**
     * Display a listing of gift vouchers.
     */
    public function index(Request $request)
    {
        $setting['page_title'] = 'Gift Vouchers';

        if ($request->ajax()) {
            $query = GiftVoucher::query()->orderBy('id', 'desc');

            if ($status = $request->input('status')) {
                if ($status !== 'all') {
                    $query->where('status', $status);
                }
            }

            if ($search = $request->input('search.value')) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            return datatables()->of($query)
                ->addIndexColumn()
                ->editColumn('name', function ($row) {
                    $html = '<div class="d-flex align-items-center">';
                    $html .= '<div class="me-2" style="width:36px;height:36px;border-radius:8px;background:#f3e8ff;color:#7e22ce;display:flex;align-items:center;justify-content:center;font-size:16px;"><i class="fa fa-ticket"></i></div>';
                    $html .= '<div><strong>' . e($row->name) . '</strong>';
                    if (!empty($row->description)) {
                        $html .= '<br><small class="text-muted">' . e(\Illuminate\Support\Str::limit($row->description, 45)) . '</small>';
                    }
                    $html .= '</div></div>';
                    return $html;
                })
                ->editColumn('code', function ($row) {
                    return '<span class="badge" style="background:#f0efff;color:#4e54c8;font-family:monospace;font-size:13px;padding:6px 10px;border:1px dashed #4e54c8;">' . e($row->code) . '</span>';
                })
                ->editColumn('voucher_value', function ($row) {
                    return '<strong style="color:#07484A;font-size:14px;">₹' . number_format($row->voucher_value, 2) . '</strong>';
                })
                ->editColumn('membership', function ($row) {
                    if ($row->membership_scope === 'all_users') {
                        return '<span class="badge bg-light text-dark">All Users</span>';
                    } elseif ($row->membership_scope === 'any_membership') {
                        return '<span class="badge" style="background:#fef3c7;color:#92400e;"><i class="fa fa-crown me-1"></i>Any Membership</span>';
                    } else {
                        $card = DB::table('tbl_membership_card')->where('card_id', $row->membership_card_id)->first();
                        $cardName = $card ? $card->card_name : 'Specific Plan';
                        return '<span class="badge" style="background:#fef08a;color:#854d0e;"><i class="fa fa-crown me-1"></i>' . e($cardName) . '</span>';
                    }
                })
                ->editColumn('validity', function ($row) {
                    if ($row->start_date && $row->end_date) {
                        return '<small>' . $row->start_date->format('d M Y') . ' → ' . $row->end_date->format('d M Y') . '</small>';
                    }
                    return '<small class="text-muted">' . $row->validity_days . ' days after issue</small>';
                })
                ->editColumn('status', function ($row) {
                    $checked = ($row->status === 'active') ? 'checked' : '';
                    return '<label class="status-toggle mb-0">
                                <input type="checkbox" class="toggle-status" data-id="' . $row->id . '" ' . $checked . '>
                                <span class="status-slider"></span>
                            </label>';
                })
                ->addColumn('action', function ($row) {
                    $editUrl = url(config('app.admin_path') . '/gift-vouchers/' . $row->id . '/edit');
                    return '<div class="btn-group btn-group-sm">
                                <a href="' . $editUrl . '" class="btn btn-outline-primary" title="Edit Voucher"><i class="fa fa-pencil"></i></a>
                                <button type="button" class="btn btn-outline-danger btn-delete-voucher" data-id="' . $row->id . '" title="Delete"><i class="fa fa-trash"></i></button>
                            </div>';
                })
                ->rawColumns(['name', 'code', 'voucher_value', 'membership', 'validity', 'status', 'action'])
                ->make(true);
        }

        $setting['breadcrumbs'] = [
            ['name' => 'Dashboard', 'link' => '/'],
            ['name' => 'Gift Vouchers', 'link' => 'javascript:void(0)'],
        ];

        return view('admin.gift_vouchers.index', $setting);
    }

    /**
     * Show the form for creating a new gift voucher.
     */
    public function create()
    {
        $setting['page_title']   = 'Create Gift Voucher';
        $setting['categories']   = Category::where('is_active', 1)->orderBy('name')->get();
        $setting['brands']       = Brand::where('status', 1)->orderBy('brand_name')->get();
        $setting['all_products'] = Product::where('status', 1)->orderBy('product_name')->limit(100)->get(['id', 'product_name', 'product_code', 'Company', 'Retail_Price', 'product_image']);
        $setting['memberships']  = DB::table('tbl_membership_card')->where('flag', 0)->orderBy('card_name')->get();

        $setting['breadcrumbs'] = [
            ['name' => 'Dashboard', 'link' => '/'],
            ['name' => 'Gift Vouchers', 'link' => url(config('app.admin_path') . '/gift-vouchers')],
            ['name' => 'Create Voucher', 'link' => 'javascript:void(0)'],
        ];

        return view('admin.gift_vouchers.create', $setting);
    }

    /**
     * Store a newly created gift voucher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'code'                 => 'required|string|max:50|unique:tbl_gift_vouchers,code',
            'voucher_value'        => 'required|numeric|min:1',
            'min_cart_amount'      => 'nullable|numeric|min:0',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'validity_days'        => 'nullable|integer|min:1',
            'membership_scope'     => 'required|in:all_users,any_membership,specific_membership',
            'membership_card_id'   => 'nullable|required_if:membership_scope,specific_membership|exists:tbl_membership_card,card_id',
            'allow_bogo_stacking'  => 'nullable|boolean',
            'allow_coupon_stacking'=> 'nullable|boolean',
            'apply_on'             => 'required|in:all_products,specific_category,specific_brand,specific_products',
            'categories'           => 'nullable|array',
            'categories.*'         => 'exists:categories,id',
            'brands'               => 'nullable|array',
            'brands.*'             => 'exists:tbl_brand,brand_id',
            'products'             => 'nullable|array',
            'products.*'           => 'exists:tbl_product_code,id',
            'description'          => 'nullable|string|max:1000',
            'status'               => 'required|in:active,inactive,draft',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['added_by'] = Auth::id();

        // Arrays based on apply_on type
        $validated['category_ids'] = ($request->apply_on === 'specific_category' && $request->has('categories')) ? $request->input('categories') : null;
        $validated['brand_ids']    = ($request->apply_on === 'specific_brand'    && $request->has('brands'))     ? $request->input('brands')     : null;
        $validated['product_ids']  = ($request->apply_on === 'specific_products' && $request->has('products'))   ? $request->input('products')   : null;

        $validated['allow_bogo_stacking']   = $request->boolean('allow_bogo_stacking', false);
        $validated['allow_coupon_stacking'] = $request->boolean('allow_coupon_stacking', false);

        if ($validated['membership_scope'] !== 'specific_membership') {
            $validated['membership_card_id'] = null;
        }

        $voucher = GiftVoucher::create($validated);

        Cache::forget('active_gift_vouchers');

        return response()->json([
            'success' => true,
            'message' => 'Gift Voucher <strong>' . e($voucher->name) . '</strong> created successfully.',
            'data'    => $voucher,
        ]);
    }

    /**
     * Show the edit form for a gift voucher.
     */
    public function edit($id)
    {
        $voucher = GiftVoucher::findOrFail($id);

        $setting['page_title']   = 'Edit Gift Voucher: ' . $voucher->name;
        $setting['voucher']      = $voucher;
        $setting['categories']   = Category::where('is_active', 1)->orderBy('name')->get();
        $setting['brands']       = Brand::where('status', 1)->orderBy('brand_name')->get();
        $setting['all_products'] = Product::where('status', 1)->orderBy('product_name')->limit(100)->get(['id', 'product_name', 'product_code', 'Company', 'Retail_Price', 'product_image']);
        $setting['memberships']  = DB::table('tbl_membership_card')->where('flag', 0)->orderBy('card_name')->get();

        $setting['selected_products'] = [];
        if ($voucher->apply_on === 'specific_products' && is_array($voucher->product_ids)) {
            $setting['selected_products'] = Product::whereIn('id', $voucher->product_ids)
                ->where('status', 1)
                ->get(['id', 'product_name', 'product_code', 'Company', 'Retail_Price', 'product_image']);
        }

        $setting['breadcrumbs'] = [
            ['name' => 'Dashboard', 'link' => '/'],
            ['name' => 'Gift Vouchers', 'link' => url(config('app.admin_path') . '/gift-vouchers')],
            ['name' => 'Edit Voucher', 'link' => 'javascript:void(0)'],
        ];

        return view('admin.gift_vouchers.create', $setting);
    }

    /**
     * Update an existing gift voucher.
     */
    public function update(Request $request, $id)
    {
        $voucher = GiftVoucher::findOrFail($id);

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'code'                 => 'required|string|max:50|unique:tbl_gift_vouchers,code,' . $id,
            'voucher_value'        => 'required|numeric|min:1',
            'min_cart_amount'      => 'nullable|numeric|min:0',
            'start_date'           => 'nullable|date',
            'end_date'             => 'nullable|date|after_or_equal:start_date',
            'validity_days'        => 'nullable|integer|min:1',
            'membership_scope'     => 'required|in:all_users,any_membership,specific_membership',
            'membership_card_id'   => 'nullable|required_if:membership_scope,specific_membership|exists:tbl_membership_card,card_id',
            'allow_bogo_stacking'  => 'nullable|boolean',
            'allow_coupon_stacking'=> 'nullable|boolean',
            'apply_on'             => 'required|in:all_products,specific_category,specific_brand,specific_products',
            'categories'           => 'nullable|array',
            'categories.*'         => 'exists:categories,id',
            'brands'               => 'nullable|array',
            'brands.*'             => 'exists:tbl_brand,brand_id',
            'products'             => 'nullable|array',
            'products.*'           => 'exists:tbl_product_code,id',
            'description'          => 'nullable|string|max:1000',
            'status'               => 'required|in:active,inactive,draft',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        // Arrays based on apply_on type
        $validated['category_ids'] = ($request->apply_on === 'specific_category' && $request->has('categories')) ? $request->input('categories') : null;
        $validated['brand_ids']    = ($request->apply_on === 'specific_brand'    && $request->has('brands'))     ? $request->input('brands')     : null;
        $validated['product_ids']  = ($request->apply_on === 'specific_products' && $request->has('products'))   ? $request->input('products')   : null;

        $validated['allow_bogo_stacking']   = $request->boolean('allow_bogo_stacking', false);
        $validated['allow_coupon_stacking'] = $request->boolean('allow_coupon_stacking', false);

        if ($validated['membership_scope'] !== 'specific_membership') {
            $validated['membership_card_id'] = null;
        }

        $voucher->update($validated);

        Cache::forget('active_gift_vouchers');

        return response()->json([
            'success' => true,
            'message' => 'Gift Voucher <strong>' . e($voucher->name) . '</strong> updated successfully.',
            'data'    => $voucher,
        ]);
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus($id)
    {
        $voucher = GiftVoucher::findOrFail($id);
        $newStatus = ($voucher->status === 'active') ? 'inactive' : 'active';
        $voucher->status = $newStatus;
        $voucher->save();

        Cache::forget('active_gift_vouchers');

        return response()->json([
            'success' => true,
            'message' => 'Voucher status updated to <strong>' . ucfirst($newStatus) . '</strong>.',
            'status'  => $newStatus,
        ]);
    }

    /**
     * Delete a voucher.
     */
    public function destroy($id)
    {
        $voucher = GiftVoucher::findOrFail($id);
        $voucher->delete();

        Cache::forget('active_gift_vouchers');

        return response()->json([
            'success' => true,
            'message' => 'Gift Voucher deleted successfully.',
        ]);
    }
}
