<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    // ── List page ────────────────────────────────────────────────
    public function index()
    {
        $setting['page_title'] = 'Categories';
        return view('admin.categories.list', $setting);
    }

    // ── DataTable AJAX ───────────────────────────────────────────
    public function data()
    {
        $categories = Category::withCount('subcategories')->latest();

        return DataTables::of($categories)
            ->addColumn('subcategories_count', function ($row) {
                return '<span class="badge badge-info">' . $row->subcategories_count . '</span>';
            })
            ->addColumn('store_name', function ($row) {
                if ($row->added_by) {
                    $created_by = User::find($row->added_by);
                    if ($row->store_id == '0' || $row->store_id == null) {
                        return $created_by ? $created_by->user_type : '—';
                    } else {
                        $store = Store::find($row->store_id);
                        return $store ? $store->store_id : '—';
                    }
                }
                return '—';
            })
            ->addColumn('image', function ($row) {
                if ($row->image) {
                    return '<img src="' . asset($row->image) . '" class="preview-image" width="30" height="30" style="object-fit:cover; border-radius:4px; cursor:pointer;" title="Click to view full image">';
                }
                return '—';
            })
            ->addColumn('created_at_formatted', function ($row) {
                $created_by_name = '';
                if ($row->added_by) {
                    $created_by = User::find($row->added_by);
                    $created_by_name = $created_by ? $created_by->name : '';
                }
                return date('d M, Y h:i A', strtotime($row->created_at)) . '<br>(' . $created_by_name . ')';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="cat_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="cat_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-category"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#categoryModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-category"
                                data-id="' . $row->id . '"
                                data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['image', 'subcategories_count', 'store_name', 'created_at_formatted', 'is_active', 'action'])
            ->make(true);
    }

    // ── Store (POST) ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:100|unique:categories,name',
            'slug'            => 'required|string|max:100|unique:categories,slug',
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'     => 'nullable|string|max:500',
            'is_active'       => 'nullable|boolean',
            'allowed_filters' => 'nullable|array',
        ]);

        $validated['slug']            = Str::slug($validated['slug'], '-');
        $validated['is_active']       = $request->boolean('is_active', false);
        $validated['allowed_filters'] = $request->input('allowed_filters', []);
        $validated['added_by']        = auth()->user()->id;
        $validated['store_id']        = auth()->user()->store_id;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $validated['image'] = 'uploads/categories/' . $imageName;
        }

        $category = Category::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category <strong>' . htmlspecialchars($category->name) . '</strong> added successfully.',
            'data'    => $category,
        ]);
    }

    // ── Edit — return JSON for modal pre-fill ────────────────────
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    // ── Update (PUT) ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'required|string|max:100|unique:categories,name,' . $id,
            'slug'            => 'required|string|max:100|unique:categories,slug,' . $id,
            'image'           => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'     => 'nullable|string|max:500',
            'is_active'       => 'nullable|boolean',
            'allowed_filters' => 'nullable|array',
        ]);

        $validated['slug']            = Str::slug($validated['slug'], '-');
        $validated['is_active']       = $request->boolean('is_active', false);
        $validated['allowed_filters'] = $request->input('allowed_filters', []);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/categories'), $imageName);
            $validated['image'] = 'uploads/categories/' . $imageName;
        }

        $category->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category <strong>' . htmlspecialchars($category->name) . '</strong> updated successfully.',
            'data'    => $category,
        ]);
    }

    // ── Toggle status (PATCH) ─────────────────────────────────────
    public function toggleStatus($id)
    {
        $category            = Category::findOrFail($id);
        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'success' => true,
            'message' => $category->name . ' is now ' . ($category->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $category->is_active,
        ]);
    }

    // ── Destroy (DELETE) ─────────────────────────────────────────
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->subcategories()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category. It has associated subcategories. Please remove them first.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }

    // ── Dropdown list for subcategory form ───────────────────────
    public function dropdown()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        return response()->json($categories);
    }
}
