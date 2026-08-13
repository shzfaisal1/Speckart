<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners.
     */
    public function index()
    {
        $setting['page_title'] = 'Banners';
        
        // Retrieve categories, brands, and active offers for dropdowns in modal
        $setting['categories'] = DB::table('categories')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();

        $setting['brands'] = DB::table('tbl_brand')
            ->where('status', 1)
            ->orderBy('brand_name')
            ->get();

        $setting['offers'] = DB::table('offers')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.banners.index', $setting);
    }

    /**
     * Fetch banner data for Datatables.
     */
    public function data()
    {
        $banners = DB::table('banners')
            ->select('id', 'title', 'image_path', 'link_type', 'link_id', 'custom_url', 'position', 'sort_order', 'is_active')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc');

        return DataTables::of($banners)
            ->addColumn('image', function ($row) {
                if ($row->image_path) {
                    return '<img src="' . asset($row->image_path) . '" class="preview-image" width="100" height="40" style="object-fit:cover; border-radius:4px;" title="Click to view full image">';
                }
                return '—';
            })
            ->addColumn('link_info', function ($row) {
                if ($row->link_type === 'offer') {
                    $offer = DB::table('offers')->where('id', $row->link_id)->first();
                    return '<span class="badge badge-primary">Offer: ' . ($offer ? e($offer->name) : 'Unknown (ID: ' . $row->link_id . ')') . '</span>';
                } elseif ($row->link_type === 'category') {
                    $category = DB::table('categories')->where('id', $row->link_id)->first();
                    return '<span class="badge badge-info">Category: ' . ($category ? e($category->name) : 'Unknown (ID: ' . $row->link_id . ')') . '</span>';
                } elseif ($row->link_type === 'product') {
                    $product = DB::table('tbl_product_code')->where('id', $row->link_id)->first();
                    return '<span class="badge badge-success">Product: ' . ($product ? e($product->product_name) : 'Unknown (ID: ' . $row->link_id . ')') . '</span>';
                } else {
                    return '<span class="badge badge-secondary">URL: ' . e($row->custom_url) . '</span>';
                }
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="ban_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="ban_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-banner"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#bannerModal">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-banner"
                                data-id="' . $row->id . '"
                                data-title="' . htmlspecialchars($row->title ?? 'Banner #' . $row->id, ENT_QUOTES) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['image', 'link_info', 'is_active', 'action'])
            ->make(true);
    }

    /**
     * Store a newly created banner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_type'   => 'required|in:offer,category,product,custom_url',
            'link_id'     => 'nullable|integer',
            'custom_url'  => 'nullable|string|max:1000',
            'position'    => 'required|string|max:100',
            'sort_order'  => 'required|integer',
            'is_active'   => 'nullable|boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title'       => $validated['title'],
            'link_type'   => $validated['link_type'],
            'link_id'     => $validated['link_id'] ?? null,
            'custom_url'  => $validated['custom_url'] ?? null,
            'position'    => $validated['position'],
            'sort_order'  => $validated['sort_order'],
            'is_active'   => $request->boolean('is_active', true),
            'store_id'    => auth()->user() ? auth()->user()->store_id : null,
            'added_by'    => auth()->user() ? auth()->user()->id : null,
            'start_date'  => $validated['start_date'] ?? null,
            'end_date'    => $validated['end_date'] ?? null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $data['image_path'] = 'uploads/banners/' . $imageName;
        }

        $id = DB::table('banners')->insertGetId($data);

        return response()->json([
            'success' => true,
            'message' => 'Banner added successfully.',
            'id' => $id
        ]);
    }

    /**
     * Show the banner details for editing.
     */
    public function edit($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        
        if (!$banner) {
            return response()->json(['error' => 'Banner not found.'], 404);
        }

        return response()->json($banner);
    }

    /**
     * Update the banner in storage.
     */
    public function update(Request $request, $id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner not found.'], 404);
        }

        $validated = $request->validate([
            'title'       => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_type'   => 'required|in:offer,category,product,custom_url',
            'link_id'     => 'nullable|integer',
            'custom_url'  => 'nullable|string|max:1000',
            'position'    => 'required|string|max:100',
            'sort_order'  => 'required|integer',
            'is_active'   => 'nullable|boolean',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title'       => $validated['title'],
            'link_type'   => $validated['link_type'],
            'link_id'     => $validated['link_id'] ?? null,
            'custom_url'  => $validated['custom_url'] ?? null,
            'position'    => $validated['position'],
            'sort_order'  => $validated['sort_order'],
            'is_active'   => $request->boolean('is_active', true),
            'start_date'  => $validated['start_date'] ?? null,
            'end_date'    => $validated['end_date'] ?? null,
            'updated_at'  => now(),
        ];

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($banner->image_path && file_exists(public_path($banner->image_path))) {
                @unlink(public_path($banner->image_path));
            }

            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/banners'), $imageName);
            $data['image_path'] = 'uploads/banners/' . $imageName;
        }

        DB::table('banners')->where('id', $id)->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully.'
        ]);
    }

    /**
     * Toggle banner active status.
     */
    public function toggleStatus($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner not found.'], 404);
        }

        $newStatus = !$banner->is_active;
        DB::table('banners')->where('id', $id)->update([
            'is_active' => $newStatus,
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner status updated.',
            'status' => $newStatus
        ]);
    }

    /**
     * Remove the specified banner.
     */
    public function destroy($id)
    {
        $banner = DB::table('banners')->where('id', $id)->first();
        if (!$banner) {
            return response()->json(['success' => false, 'message' => 'Banner not found.'], 404);
        }

        // Delete image file
        if ($banner->image_path && file_exists(public_path($banner->image_path))) {
            @unlink(public_path($banner->image_path));
        }

        DB::table('banners')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully.'
        ]);
    }

    /**
     * AJAX search products for link selection.
     */
    public function searchProducts(Request $request)
    {
        $search = $request->input('search');
        $query = DB::table('tbl_product_code')
            ->where('status', 1);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->select('id', 'product_name', 'product_code')
            ->limit(30)
            ->get();

        return response()->json($products);
    }
}
