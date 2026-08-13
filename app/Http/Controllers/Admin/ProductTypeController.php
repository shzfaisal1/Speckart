<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductType as ProductTypeMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductTypeController extends Controller
{
    // ── List page ────────────────────────────────────────────────
    public function index()
    {
        $setting['page_title'] = 'Product Types';
        return view('admin.product_types.list', $setting);
    }

    // ── DataTable AJAX ───────────────────────────────────────────
    public function data()
    {
        $types = ProductTypeMaster::query()->latest();

        return DataTables::of($types)
            ->addColumn('has_power', function ($row) {
                return $row->has_power
                    ? '<span class="badge bg-primary-transparent">Yes</span>'
                    : '<span class="badge bg-light text-muted">No</span>';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="type_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="type_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-type"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#productTypeModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-type"
                                data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['has_power', 'is_active', 'action'])
            ->make(true);
    }

    // ── Store (POST) ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'slug'           => 'required|string|max:100|unique:product_type_masters,slug',
            'subtitle'       => 'nullable|string|max:150',
            'icon'           => 'nullable|string|max:20',
            'has_power'      => 'nullable|boolean',
            'default_powers' => 'nullable|string',   // comes as JSON string from hidden input
            'is_active'      => 'nullable|boolean',
        ]);

        // decode powers JSON string → array (store as array, Laravel casts to JSON)
        $validated['default_powers'] = $this->parsePowers($request->default_powers, $request->has_power);

        // slug: lowercase + underscores only
        $validated['slug']      = Str::slug($validated['slug'], '_');
        $validated['is_active'] = $request->boolean('is_active', false);  // unchecked checkbox = not sent = false
        $validated['has_power'] = $request->boolean('has_power', false);

        $type = ProductTypeMaster::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product type <strong>' . $type->name . '</strong> added successfully.',
            'data'    => $type,
        ]);
    }

    // ── Edit — return JSON for modal pre-fill ────────────────────
    public function edit($id)
    {
        $type = ProductTypeMaster::findOrFail($id);

        return response()->json($type);
    }

    // ── Update (PUT) ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $type = ProductTypeMaster::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'slug'           => 'required|string|max:100|unique:product_type_masters,slug,' . $id,
            'subtitle'       => 'nullable|string|max:150',
            'icon'           => 'nullable|string|max:20',
            'has_power'      => 'nullable|boolean',
            'default_powers' => 'nullable|string',
            'is_active'      => 'nullable|boolean',
        ]);

        $validated['default_powers'] = $this->parsePowers($request->default_powers, $request->has_power);
        $validated['slug']           = Str::slug($validated['slug'], '_');
        $validated['is_active']      = $request->boolean('is_active', false);  // unchecked checkbox = not sent = false
        $validated['has_power']      = $request->boolean('has_power', false);

        $type->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product type <strong>' . $type->name . '</strong> updated successfully.',
            'data'    => $type,
        ]);
    }

    // ── Toggle status (PATCH) ─────────────────────────────────────
    public function toggleStatus($id)
    {
        $type            = ProductTypeMaster::findOrFail($id);
        $type->is_active = !$type->is_active;
        $type->save();

        return response()->json([
            'success' => true,
            'message' => $type->name . ' is now ' . ($type->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $type->is_active,
        ]);
    }

    // ── Destroy (DELETE) ─────────────────────────────────────────
    public function destroy($id)
    {
        $type = ProductTypeMaster::findOrFail($id);

        // safety check — don't delete if products are using it
        // if ($type->productOverrides()->count() > 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Cannot delete — ' . $type->productOverrides()->count() . ' product(s) are using this type.',
        //     ], 422);
        // }

        $type->delete();   // soft delete

        return response()->json([
            'success' => true,
            'message' => 'Product type deleted successfully.',
        ]);
    }

    // ── Private helper ────────────────────────────────────────────
    /**
     * Decode the JSON string coming from the hidden input.
     * Returns null when has_power is false (no powers needed).
     */
    private function parsePowers(?string $powersJson, $hasPower): ?array
    {
        if (!$hasPower) {
            return null;
        }

        if (empty($powersJson)) {
            return [];
        }

        $decoded = json_decode($powersJson, true);

        return is_array($decoded) ? $decoded : [];
    }
}