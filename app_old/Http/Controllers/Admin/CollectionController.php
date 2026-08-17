<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CollectionController extends Controller
{
    // ── List page ────────────────────────────────────────────────
    public function index()
    {
        $setting['page_title'] = 'Collections';
        return view('admin.collections.list', $setting);
    }

    // ── DataTable AJAX ───────────────────────────────────────────
    public function data()
    {
        $collections = Collection::query()->latest();

        return DataTables::of($collections)
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="coll_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="coll_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-collection"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#collectionModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-collection"
                                data-id="' . $row->id . '"
                                data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['is_active', 'action'])
            ->make(true);
    }

    // ── Store (POST) ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:collections,name',
            'slug'      => 'required|string|max:100|unique:collections,slug',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '-');
        $validated['is_active'] = $request->boolean('is_active', false);

        $collection = Collection::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Collection <strong>' . htmlspecialchars($collection->name) . '</strong> added successfully.',
            'data'    => $collection,
        ]);
    }

    // ── Edit — return JSON for modal pre-fill ────────────────────
    public function edit($id)
    {
        $collection = Collection::findOrFail($id);
        return response()->json($collection);
    }

    // ── Update (PUT) ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'required|string|max:100|unique:collections,name,' . $id,
            'slug'      => 'required|string|max:100|unique:collections,slug,' . $id,
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '-');
        $validated['is_active'] = $request->boolean('is_active', false);

        $collection->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Collection <strong>' . htmlspecialchars($collection->name) . '</strong> updated successfully.',
            'data'    => $collection,
        ]);
    }

    // ── Toggle status (PATCH) ─────────────────────────────────────
    public function toggleStatus($id)
    {
        $collection            = Collection::findOrFail($id);
        $collection->is_active = !$collection->is_active;
        $collection->save();

        return response()->json([
            'success' => true,
            'message' => $collection->name . ' is now ' . ($collection->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $collection->is_active,
        ]);
    }

    // ── Destroy (DELETE) ─────────────────────────────────────────
    public function destroy($id)
    {
        $collection = Collection::findOrFail($id);
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted successfully.',
        ]);
    }
}
