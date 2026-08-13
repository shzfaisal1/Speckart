<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SubcategoryController extends Controller
{
    // ── List page ────────────────────────────────────────────────
    public function index()
    {
        $setting['page_title']  = 'Subcategories';
        $setting['categories']  = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.subcategories.list', $setting);
    }

    // ── DataTable AJAX ───────────────────────────────────────────
    public function data(Request $request)
    {
        $subcategories = Subcategory::with('category')->latest();

        if ($request->filled('category_id')) {
            $subcategories->where('category_id', $request->category_id);
        }

        return DataTables::of($subcategories)
            ->addColumn('category_name', function ($row) {
                return $row->category ? $row->category->name : '—';
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
                    return '<img src="' . asset($row->image) . '" class="preview-image" width="50" height="50" style="object-fit:cover; border-radius:4px; cursor:pointer;" title="Click to view full image">';
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
                        <input type="checkbox" id="sub_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="sub_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-subcategory"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#subcategoryModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-subcategory"
                                data-id="' . $row->id . '"
                                data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['image', 'store_name', 'created_at_formatted', 'is_active', 'action'])
            ->make(true);
    }

    // ── Store (POST) ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:subcategories,slug',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '-');
        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['added_by']  = auth()->user()->id;
        $validated['store_id']  = auth()->user()->store_id;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $validated['image'] = 'uploads/subcategories/' . $imageName;
        }

        $subcategory = Subcategory::create($validated);
        $subcategory->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Subcategory <strong>' . htmlspecialchars($subcategory->name) . '</strong> added successfully.',
            'data'    => $subcategory,
        ]);
    }

    // ── Edit — return JSON for modal pre-fill ────────────────────
    public function edit($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    // ── Update (PUT) ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:subcategories,slug,' . $id,
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:500',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['slug']      = Str::slug($validated['slug'], '-');
        $validated['is_active'] = $request->boolean('is_active', false);

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/subcategories'), $imageName);
            $validated['image'] = 'uploads/subcategories/' . $imageName;
        }

        $subcategory->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subcategory <strong>' . htmlspecialchars($subcategory->name) . '</strong> updated successfully.',
            'data'    => $subcategory,
        ]);
    }

    // ── Toggle status (PATCH) ─────────────────────────────────────
    public function toggleStatus($id)
    {
        $subcategory            = Subcategory::findOrFail($id);
        $subcategory->is_active = !$subcategory->is_active;
        $subcategory->save();

        return response()->json([
            'success' => true,
            'message' => $subcategory->name . ' is now ' . ($subcategory->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $subcategory->is_active,
        ]);
    }

    // ── Destroy (DELETE) ─────────────────────────────────────────
    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subcategory deleted successfully.',
        ]);
    }
}
