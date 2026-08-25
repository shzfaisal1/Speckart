<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ShippingChargeController extends Controller
{
    /**
     * Display the Shipping Charges management page.
     */
    public function index()
    {
        $setting['page_title'] = 'Shipping Charges Master';
        $setting['total_count'] = ShippingCharge::count();
        $setting['active_count'] = ShippingCharge::where('status', 1)->count();
        $setting['disabled_count'] = ShippingCharge::where('status', 0)->count();

        return view('admin.shipping_charges.list', $setting);
    }

    /**
     * Return JSON data for DataTables.
     */
    public function data()
    {
        $charges = ShippingCharge::latest();

        return DataTables::of($charges)
            ->addColumn('pincode_display', function ($row) {
                return '<span class="badge badge-dark fs-13 px-2 py-1"><i class="fa fa-map-pin me-1"></i> ' . e($row->pincode) . '</span>';
            })
            ->addColumn('amount_display', function ($row) {
                if ((float)$row->amount <= 0) {
                    return '<span class="badge badge-success fs-13 px-2 py-1">FREE DELIVERY</span>';
                }
                return '<span class="badge badge-primary fs-13 px-2 py-1 font-weight-bold">₹' . number_format($row->amount, 2) . '</span>';
            })
            ->addColumn('cod_toggle', function ($row) {
                $checked = ($row->is_cod_available ?? 1) ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="cod_' . $row->id . '" class="toggle-switch toggle-cod"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="cod_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('status_toggle', function ($row) {
                $checked = $row->status ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="shipping_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="shipping_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-shipping"
                                data-id="' . $row->id . '"
                                title="Edit Shipping Charge">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-shipping"
                                data-id="' . $row->id . '"
                                data-pincode="' . e($row->pincode) . '"
                                title="Delete Pincode">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['pincode_display', 'amount_display', 'cod_toggle', 'status_toggle', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created shipping charge in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pincode'          => 'required|string|max:10|regex:/^[0-9A-Za-z\-]+$/|unique:tbl_shipping_charges,pincode',
            'amount'           => 'required|numeric|min:0',
            'is_cod_available' => 'nullable|in:0,1',
            'status'           => 'nullable|in:0,1',
        ], [
            'pincode.required' => 'Pincode is required.',
            'pincode.unique'   => 'This pincode already exists in the system.',
            'amount.required'  => 'Shipping amount is required.',
            'amount.numeric'   => 'Shipping amount must be a valid number.',
            'amount.min'       => 'Shipping amount cannot be negative.',
        ]);

        $validated['pincode']          = trim($validated['pincode']);
        $validated['is_cod_available'] = $request->has('is_cod_available') ? (int)$request->input('is_cod_available') : 1;
        $validated['status']           = $request->has('status') ? (int)$request->input('status') : 1;

        $charge = ShippingCharge::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => "Shipping charge for pincode {$charge->pincode} created successfully.",
            'data'    => $charge
        ]);
    }

    /**
     * Return single record for modal editing.
     */
    public function show($id)
    {
        $charge = ShippingCharge::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data'   => $charge
        ]);
    }

    /**
     * Update the specified shipping charge in storage.
     */
    public function update(Request $request, $id)
    {
        $charge = ShippingCharge::findOrFail($id);

        $validated = $request->validate([
            'pincode'          => 'required|string|max:10|regex:/^[0-9A-Za-z\-]+$/|unique:tbl_shipping_charges,pincode,' . $id,
            'amount'           => 'required|numeric|min:0',
            'is_cod_available' => 'nullable|in:0,1',
            'status'           => 'nullable|in:0,1',
        ], [
            'pincode.required' => 'Pincode is required.',
            'pincode.unique'   => 'This pincode already exists in the system.',
            'amount.required'  => 'Shipping amount is required.',
            'amount.numeric'   => 'Shipping amount must be a valid number.',
            'amount.min'       => 'Shipping amount cannot be negative.',
        ]);

        $validated['pincode']          = trim($validated['pincode']);
        $validated['is_cod_available'] = $request->has('is_cod_available') ? (int)$request->input('is_cod_available') : 1;
        $validated['status']           = $request->has('status') ? (int)$request->input('status') : 1;

        $charge->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => "Shipping charge for pincode {$charge->pincode} updated successfully.",
            'data'    => $charge
        ]);
    }

    /**
     * Toggle COD availability for a shipping charge.
     */
    public function toggleCod($id)
    {
        $charge = ShippingCharge::findOrFail($id);
        $charge->is_cod_available = ($charge->is_cod_available ?? 1) == 1 ? 0 : 1;
        $charge->save();

        $codLabel = $charge->is_cod_available == 1 ? 'COD Enabled' : 'Prepaid Only (COD Disabled)';

        return response()->json([
            'status'           => 'success',
            'message'          => "Pincode {$charge->pincode} is now {$codLabel}.",
            'is_cod_available' => $charge->is_cod_available == 1,
            'new_cod'          => $charge->is_cod_available
        ]);
    }

    /**
     * Toggle the enabled/disabled status of a shipping charge.
     */
    public function toggleStatus($id)
    {
        $charge = ShippingCharge::findOrFail($id);
        $charge->status = $charge->status == 1 ? 0 : 1;
        $charge->save();

        $statusLabel = $charge->status == 1 ? 'Enabled' : 'Disabled';

        return response()->json([
            'status'    => 'success',
            'message'   => "Pincode {$charge->pincode} is now {$statusLabel}.",
            'is_active' => $charge->status == 1,
            'new_status'=> $charge->status
        ]);
    }

    /**
     * Remove the specified shipping charge from storage.
     */
    public function destroy($id)
    {
        $charge = ShippingCharge::findOrFail($id);
        $pincode = $charge->pincode;
        $charge->delete();

        return response()->json([
            'status'  => 'success',
            'message' => "Shipping charge for pincode {$pincode} deleted successfully."
        ]);
    }
}
