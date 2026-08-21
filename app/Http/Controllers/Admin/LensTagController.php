<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LensPackageTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class LensTagController extends Controller
{
    public function index()
    {
        $setting['page_title'] = 'Lens Tags';
        return view('admin.lens_tags.list', $setting);
    }

    public function data()
    {
        $tags = LensPackageTag::withCount('lensPackages')->orderBy('sort_order');

        return DataTables::of($tags)
            ->addColumn('slug_badge', function ($row) {
                return '<code class="bg-light px-2 py-1 rounded">' . e($row->slug) . '</code>';
            })
            ->addColumn('packages_count', function ($row) {
                $count = $row->lens_packages_count;
                $badge = $count > 0 ? 'badge-primary' : 'badge-light text-muted';
                return '<span class="badge ' . $badge . '">' . $count . '</span>';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="tag_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="tag_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-tag"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#tagModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-tag"
                                data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['slug_badge', 'packages_count', 'is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if (!$request->filled('slug') && $request->filled('name')) {
            $request->merge(['slug' => Str::slug($request->input('name'), '_')]);
        } elseif ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('slug'), '_')]);
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'slug'       => 'required|string|max:100|unique:lens_package_tags,slug',
            'icon_url'   => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '_');
        $validated['is_active'] = $request->boolean('is_active', true);

        $tag = LensPackageTag::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tag <strong>' . e($tag->name) . '</strong> added successfully.',
            'data'    => $tag,
        ]);
    }

    public function edit($id)
    {
        $tag = LensPackageTag::findOrFail($id);
        return response()->json($tag);
    }

    public function update(Request $request, $id)
    {
        $tag = LensPackageTag::findOrFail($id);

        if (!$request->filled('slug') && $request->filled('name')) {
            $request->merge(['slug' => Str::slug($request->input('name'), '_')]);
        } elseif ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('slug'), '_')]);
        }

        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'slug'       => 'required|string|max:100|unique:lens_package_tags,slug,' . $id,
            'icon_url'   => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active'  => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '_');
        $validated['is_active'] = $request->boolean('is_active', true);

        $tag->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tag <strong>' . e($tag->name) . '</strong> updated successfully.',
            'data'    => $tag,
        ]);
    }

    public function toggleStatus($id)
    {
        $tag            = LensPackageTag::findOrFail($id);
        $tag->is_active = !$tag->is_active;
        $tag->save();

        return response()->json([
            'success' => true,
            'message' => e($tag->name) . ' is now ' . ($tag->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $tag->is_active,
        ]);
    }

    public function destroy($id)
    {
        $tag = LensPackageTag::findOrFail($id);

        $packageCount = $tag->lensPackages()->count();
        if ($packageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete — ' . $packageCount . ' lens package(s) are using this tag.',
            ], 422);
        }

        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tag deleted successfully.',
        ]);
    }
}
