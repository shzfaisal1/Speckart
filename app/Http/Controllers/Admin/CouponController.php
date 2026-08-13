<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\LensPackage;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function index()
    {
        $setting['page_title'] = 'Coupons';
        $setting['lensPackages'] = LensPackage::orderBy('name')->get();
        return view('admin.coupons.list', $setting);
    }

    public function data()
    {
        $coupons = Coupon::withCount('lensPackages')->latest();

        return DataTables::of($coupons)
            ->addColumn('code_display', function ($row) {
                return '<code>' . e(strtoupper($row->code)) . '</code>';
            })
            ->addColumn('discount_display', function ($row) {
                if ($row->discount_type === 'fixed') {
                    return '<span class="badge badge-success">FLAT ₹' . number_format($row->discount_value, 0) . '</span>';
                }
                return '<span class="badge badge-info">' . rtrim(rtrim(number_format($row->discount_value, 2), '0'), '.') . '% OFF</span>';
            })
            ->addColumn('validity', function ($row) {
                if (!$row->valid_from && !$row->valid_until) {
                    return '<span class="text-muted fst-italic">Always Valid</span>';
                }
                $from  = $row->valid_from  ? $row->valid_from->format('d M Y')  : '—';
                $until = $row->valid_until ? $row->valid_until->format('d M Y') : '—';
                return $from . ' → ' . $until;
            })
            ->addColumn('usage', function ($row) {
                $current = (int) $row->current_uses;
                $max     = $row->max_uses !== null ? (int) $row->max_uses : null;
                return $current . ' / ' . ($max !== null ? $max : '∞') . ' used';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="coupon_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="coupon_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('packages_count', function ($row) {
                return '<span class="badge badge-primary">' . $row->lens_packages_count . '</span>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-coupon"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#couponModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-coupon"
                                data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['code_display', 'discount_display', 'validity', 'is_active', 'packages_count', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'                => 'required|string|max:50|unique:coupons,code',
            'description'         => 'nullable|string|max:255',
            'discount_type'       => 'required|in:percentage,fixed',
            'discount_value'      => 'required|numeric|min:0',
            'min_order_value'     => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from'          => 'nullable|date',
            'valid_until'         => 'nullable|date|after_or_equal:valid_from',
            'max_uses'            => 'nullable|integer|min:1',
            'is_active'           => 'nullable|boolean',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $coupon = Coupon::create($validated);

        if ($request->has('lens_packages')) {
            $coupon->lensPackages()->sync($request->input('lens_packages', []));
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon <strong>' . e($coupon->code) . '</strong> created successfully.',
            'data'    => $coupon,
        ]);
    }

    public function edit($id)
    {
        $coupon = Coupon::with('lensPackages:id')->findOrFail($id);
        return response()->json($coupon);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $validated = $request->validate([
            'code'                => 'required|string|max:50|unique:coupons,code,' . $id,
            'description'         => 'nullable|string|max:255',
            'discount_type'       => 'required|in:percentage,fixed',
            'discount_value'      => 'required|numeric|min:0',
            'min_order_value'     => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'valid_from'          => 'nullable|date',
            'valid_until'         => 'nullable|date|after_or_equal:valid_from',
            'max_uses'            => 'nullable|integer|min:1',
            'is_active'           => 'nullable|boolean',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $coupon->update($validated);

        $coupon->lensPackages()->sync($request->input('lens_packages', []));

        return response()->json([
            'success' => true,
            'message' => 'Coupon <strong>' . e($coupon->code) . '</strong> updated successfully.',
            'data'    => $coupon,
        ]);
    }

    public function toggleStatus($id)
    {
        $coupon            = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return response()->json([
            'success' => true,
            'message' => $coupon->code . ' is now ' . ($coupon->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $coupon->is_active,
        ]);
    }

    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully.',
        ]);
    }
}
