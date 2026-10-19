<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LensPackage;
use App\Models\LensPackageMedia;
use App\Models\LensPackageTag;
use App\Models\LensBenefit;
use App\Models\Coupon;
use App\Models\PowerType;
use App\Models\ProductType;
use App\Models\FrameType;
use App\Models\FrameShape;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class LensPackageController extends Controller
{
    public function index()
    {
        $setting['page_title'] = 'Lens Packages';
        $setting['tags']       = LensPackageTag::where('is_active', true)->orderBy('sort_order')->get();
        $setting['benefits']   = LensBenefit::where('is_active', true)->get();
        // Only load actual lens prescriptions (exclude 'Frame Only' which is handled at Step 1)
        $setting['powerTypes'] = PowerType::where('is_active', '1')
            ->where('description', 'NOT LIKE', '%Frame Only%')
            ->get();
        $setting['categories'] = ProductType::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.lens_packages.list', $setting);
    }

    public function data()
    {
        $packages = LensPackage::with(['tags', 'media', 'powerTypes'])->latest();

        return DataTables::of($packages)
            ->editColumn('name', function ($row) {
                $primaryMedia = $row->media->first();
                $imgUrl = $primaryMedia ? asset($primaryMedia->url) : 'https://static5.lenskart.com/media/uploads/Antiglare_1_updated.png';
                $powerTypesText = $row->powerTypes->isNotEmpty()
                    ? '<small class="text-muted d-block" style="font-size:11px;">' . e($row->powerTypes->pluck('description')->implode(', ')) . '</small>'
                    : '';

                $powerRangeBadge = $row->power_range_text
                    ? '<small class="text-primary d-block mt-1" style="font-size:11px; font-weight:600;"><i class="fa fa-sliders me-1"></i>' . e($row->power_range_text) . '</small>'
                    : '';

                return '
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <img src="' . $imgUrl . '" alt="' . e($row->name) . '" class="rounded border" style="width: 40px; height: 40px; object-fit: cover; flex-shrink: 0; background: #f8f9fa;">
                        <div>
                            <strong class="text-dark d-block" style="font-size:13px;">' . e($row->name) . '</strong>
                            ' . $powerTypesText . '
                            ' . $powerRangeBadge . '
                        </div>
                    </div>';
            })
            ->addColumn('product_code', function ($row) {
                $invBadge = ((int)($row->allow_negative_inventory ?? 1) === 0)
                    ? '<br><span class="badge badge-warning text-dark mt-1" style="font-size:10px; font-weight:600;"><i class="fa fa-lock me-1"></i>Strict Stock</span>'
                    : '<br><span class="badge badge-light border text-muted mt-1" style="font-size:10px;"><i class="fa fa-infinity me-1"></i>On-Demand</span>';

                if (!empty($row->product_code)) {
                    return '<span class="badge badge-light border text-dark" style="font-family:monospace; font-size:11px; font-weight:600; padding:4px 8px; letter-spacing:0.5px;">' . e($row->product_code) . '</span>' . $invBadge;
                }
                return '<button type="button" class="btn btn-xs btn-outline-primary py-0 px-2 btn-generate-table" data-id="' . $row->id . '" data-name="' . e($row->name) . '" style="font-size:11px; border-radius:4px; font-weight:600;" title="Auto-generate physical product in product master">
                            <i class="fa fa-magic me-1"></i>Generate Product
                        </button>' . $invBadge;
            })
            ->addColumn('price', function ($row) {
                $current  = '₹' . number_format($row->current_price, 2);
                $original = $row->original_price
                    ? ' <small class="text-muted">/ <s>₹' . number_format($row->original_price, 2) . '</s></small>'
                    : '';
                return '<span class="fw-bold text-success">' . $current . '</span>' . $original;
            })
            ->addColumn('package_mode', function ($row) {
                $type = $row->package_type ?? ($row->is_free_lens ? 'free_lens' : 'frame_and_lens');
                switch ($type) {
                    case 'free_lens':
                        return '<span class="badge badge-success px-2 py-1" style="background-color:#198754; color:#fff; font-size:11px;"><i class="fa fa-check-circle me-1"></i>Free Lens</span>';
                    case 'free_frame':
                        return '<span class="badge badge-purple px-2 py-1" style="background-color:#6f42c1; color:#fff; font-size:11px;"><i class="fa fa-gift me-1"></i>Free Frame</span>';
                    case 'frame_and_lens':
                        return '<span class="badge badge-primary px-2 py-1" style="background-color:#0d6efd; color:#fff; font-size:11px;"><i class="fa fa-cubes me-1"></i>Frame & Lens Paid</span>';
                    case 'lens_only':
                        return '<span class="badge badge-info px-2 py-1" style="font-size:11px;"><i class="fa fa-circle me-1"></i>Lens Only</span>';
                    default:
                        return '<span class="badge badge-primary px-2 py-1" style="background-color:#0d6efd; color:#fff; font-size:11px;"><i class="fa fa-cubes me-1"></i>Frame & Lens Paid</span>';
                }
            })
            ->addColumn('tags_list', function ($row) {
                if ($row->tags->isEmpty()) {
                    return '<span class="text-muted" style="font-size:11px;">—</span>';
                }
                return $row->tags->map(function ($tag) {
                    return '<span class="badge badge-secondary me-1" style="font-size:11px;">' . e($tag->name) . '</span>';
                })->implode(' ');
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="package_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="package_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 6px;">
                        <button class="btn btn-sm btn-success btn-edit-package"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#lensPackageModal"
                                onclick="openModal(\'edit\')"
                                title="Edit Package">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-package"
                                data-id="' . $row->id . '"
                                title="Delete Package">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['name', 'product_code', 'price', 'package_mode', 'tags_list', 'is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        if ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('slug'), '_')]);
        } elseif ($request->filled('name')) {
            $request->merge(['slug' => Str::slug($request->input('name'), '_')]);
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'slug'              => 'required|string|max:150|unique:lens_packages,slug',
            'product_code'      => 'nullable|string|max:100',
            'company'           => 'nullable|string|max:100',
            'quality'           => 'nullable|string|max:100',
            'lens_index'        => 'nullable|string|max:50',
            'coating'           => 'nullable|string|max:100',
            'material'          => 'nullable|string|max:100',
            'design'            => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'purchase_price'    => 'nullable|numeric|min:0',
            'current_price'     => 'required|numeric|min:0',
            'original_price'    => 'nullable|numeric|min:0',
            'warranty_months'   => 'nullable|integer|min:0',
            'min_sph'           => 'nullable|numeric|between:-25,25',
            'max_sph'           => 'nullable|numeric|between:-25,25',
            'min_cyl'           => 'nullable|numeric|between:-15,15',
            'max_cyl'           => 'nullable|numeric|between:-15,15',
            'min_axis'                 => 'nullable|integer|between:1,180',
            'max_axis'                 => 'nullable|integer|between:1,180',
            'track_inventory'          => 'nullable|in:0,1',
            'allow_negative_inventory' => 'nullable|in:0,1',
            'is_free_lens'             => 'nullable|boolean',
            'package_type'             => 'nullable|string|in:frame_and_lens,free_lens,free_frame,lens_only,frame_only',
            'is_active'                => 'nullable|boolean',
            'sort_order'               => 'nullable|integer|min:0',
        ]);

        $validated['product_code']             = $request->filled('product_code') 
            ? trim($request->input('product_code')) 
            : $this->makeUniqueProductCode(
                $request->input('name', ''),
                $request->input('company', ''),
                $request->input('lens_index', ''),
                $request->input('coating', '')
            );
        $validated['company']                  = $request->filled('company') ? trim($request->input('company')) : null;
        $validated['quality']                  = $request->filled('quality') ? trim($request->input('quality')) : null;
        $validated['lens_index']               = $request->filled('lens_index') ? trim($request->input('lens_index')) : null;
        $validated['coating']                  = $request->filled('coating') ? trim($request->input('coating')) : null;
        $validated['material']                 = $request->filled('material') ? trim($request->input('material')) : null;
        $validated['design']                   = $request->filled('design') ? trim($request->input('design')) : null;
        $validated['min_sph']                  = $request->filled('min_sph') ? (float)$request->input('min_sph') : null;
        $validated['max_sph']                  = $request->filled('max_sph') ? (float)$request->input('max_sph') : null;
        $validated['min_cyl']                  = $request->filled('min_cyl') ? (float)$request->input('min_cyl') : null;
        $validated['max_cyl']                  = $request->filled('max_cyl') ? (float)$request->input('max_cyl') : null;
        $validated['min_axis']                 = $request->filled('min_axis') ? (int)$request->input('min_axis') : null;
        $validated['max_axis']                 = $request->filled('max_axis') ? (int)$request->input('max_axis') : null;
        $validated['purchase_price']           = $request->filled('purchase_price') ? (float)$request->input('purchase_price') : 0;
        $validated['track_inventory']          = ($request->has('track_inventory') && $request->input('track_inventory') !== null) ? (int)$request->input('track_inventory') : 1;
        $validated['allow_negative_inventory'] = ($request->has('allow_negative_inventory') && $request->input('allow_negative_inventory') !== null) ? (int)$request->input('allow_negative_inventory') : 1;
        $validated['is_active']                = $request->boolean('is_active', false);
        $validated['package_type']    = $request->input('package_type', 'frame_and_lens');
        // Derive is_free_lens from package_type — single source of truth
        $validated['is_free_lens']    = ($validated['package_type'] === 'free_lens') ? 1 : 0;
        $validated['warranty_months'] = $validated['warranty_months'] ?? 0;
        $validated['sort_order']      = $validated['sort_order'] ?? 0;

        return DB::transaction(function () use ($validated, $request) {
            $package = LensPackage::create($validated);

            // Sync with physical Glass product master tbl_product_code
            $this->syncWithProductMaster($package);

            // Sync tags
            $package->tags()->sync($request->input('tags', []));

            // Sync benefits
            $benefitsInput = [];
            if ($request->has('benefits_json')) {
                $benefitsInput = json_decode($request->input('benefits_json'), true) ?? [];
            }
            $this->syncBenefits($package, $benefitsInput);

            // Sync coupons
            $package->coupons()->sync($request->input('coupons', []));

            // Sync power types
            $package->powerTypes()->sync($request->input('power_types', []));

            // Sync categories, frame types, frame shapes, brands
            $package->categories()->sync($request->input('categories', []));
            $package->frameTypes()->sync($request->input('frame_types', []));
            $package->frameShapes()->sync($request->input('frame_shapes', []));
            $package->brands()->sync($request->input('brands', []));

            // Replace badges
            $badgesInput = [];
            if ($request->has('badges_json')) {
                $badgesInput = $request->input('badges_json');
            }
            $this->replaceBadges($package, $badgesInput);

            return response()->json([
                'success' => true,
                'message' => 'Lens package <strong>' . e($package->name) . '</strong> added successfully.',
                'data'    => $package->load(['tags', 'benefits', 'badges', 'coupons']),
            ]);
        });
    }

    public function edit($id)
    {
        $package = LensPackage::with([
            'tags',
            'benefits' => function ($q) {
                $q->withPivot('sort_order', 'is_highlighted');
            },
            'badges',
            'coupons',
            'powerTypes',
            'categories',
            'frameTypes',
            'frameShapes',
            'brands',
            'media' => function ($q) {
                $q->orderBy('sort_order');
            },
        ])->findOrFail($id);

        return response()->json($package);
    }

    public function update(Request $request, $id)
    {
        $package = LensPackage::findOrFail($id);

        if ($request->filled('slug')) {
            $request->merge(['slug' => Str::slug($request->input('slug'), '_')]);
        } elseif ($request->filled('name')) {
            $request->merge(['slug' => Str::slug($request->input('name'), '_')]);
        }

        $validated = $request->validate([
            'name'              => 'required|string|max:150',
            'slug'              => 'required|string|max:150|unique:lens_packages,slug,' . $id,
            'product_code'      => 'nullable|string|max:100',
            'company'           => 'nullable|string|max:100',
            'quality'           => 'nullable|string|max:100',
            'lens_index'        => 'nullable|string|max:50',
            'coating'           => 'nullable|string|max:100',
            'material'          => 'nullable|string|max:100',
            'design'            => 'nullable|string|max:100',
            'short_description' => 'nullable|string',
            'purchase_price'    => 'nullable|numeric|min:0',
            'current_price'     => 'required|numeric|min:0',
            'original_price'    => 'nullable|numeric|min:0',
            'warranty_months'   => 'nullable|integer|min:0',
            'min_sph'           => 'nullable|numeric|between:-25,25',
            'max_sph'           => 'nullable|numeric|between:-25,25',
            'min_cyl'           => 'nullable|numeric|between:-15,15',
            'max_cyl'           => 'nullable|numeric|between:-15,15',
            'min_axis'                 => 'nullable|integer|between:1,180',
            'max_axis'                 => 'nullable|integer|between:1,180',
            'track_inventory'          => 'nullable|in:0,1',
            'allow_negative_inventory' => 'nullable|in:0,1',
            'is_free_lens'             => 'nullable|boolean',
            'package_type'             => 'nullable|string|in:frame_and_lens,free_lens,free_frame,lens_only,frame_only',
            'is_active'                => 'nullable|boolean',
            'sort_order'               => 'nullable|integer|min:0',
        ]);

        $validated['product_code']             = $request->filled('product_code') 
            ? trim($request->input('product_code')) 
            : ($package->product_code ?: $this->makeUniqueProductCode(
                $request->input('name', $package->name),
                $request->input('company', $package->company),
                $request->input('lens_index', $package->lens_index),
                $request->input('coating', $package->coating)
            ));
        $validated['company']                  = $request->filled('company') ? trim($request->input('company')) : null;
        $validated['quality']                  = $request->filled('quality') ? trim($request->input('quality')) : null;
        $validated['lens_index']               = $request->filled('lens_index') ? trim($request->input('lens_index')) : null;
        $validated['coating']                  = $request->filled('coating') ? trim($request->input('coating')) : null;
        $validated['material']                 = $request->filled('material') ? trim($request->input('material')) : null;
        $validated['design']                   = $request->filled('design') ? trim($request->input('design')) : null;
        $validated['min_sph']                  = $request->filled('min_sph') ? (float)$request->input('min_sph') : null;
        $validated['max_sph']                  = $request->filled('max_sph') ? (float)$request->input('max_sph') : null;
        $validated['min_cyl']                  = $request->filled('min_cyl') ? (float)$request->input('min_cyl') : null;
        $validated['max_cyl']                  = $request->filled('max_cyl') ? (float)$request->input('max_cyl') : null;
        $validated['min_axis']                 = $request->filled('min_axis') ? (int)$request->input('min_axis') : null;
        $validated['max_axis']                 = $request->filled('max_axis') ? (int)$request->input('max_axis') : null;
        $validated['purchase_price']           = $request->filled('purchase_price') ? (float)$request->input('purchase_price') : 0;
        $validated['track_inventory']          = ($request->has('track_inventory') && $request->input('track_inventory') !== null) ? (int)$request->input('track_inventory') : ($package->track_inventory ?? 1);
        $validated['allow_negative_inventory'] = ($request->has('allow_negative_inventory') && $request->input('allow_negative_inventory') !== null) ? (int)$request->input('allow_negative_inventory') : ($package->allow_negative_inventory ?? 1);
        $validated['is_active']                = $request->boolean('is_active', false);
        $validated['package_type']    = $request->input('package_type', 'frame_and_lens');
        // Derive is_free_lens from package_type — single source of truth
        $validated['is_free_lens']    = ($validated['package_type'] === 'free_lens') ? 1 : 0;
        $validated['warranty_months'] = $validated['warranty_months'] ?? 0;
        $validated['sort_order']      = $validated['sort_order'] ?? 0;

        return DB::transaction(function () use ($package, $validated, $request) {
            $package->update($validated);

            // Sync with physical Glass product master tbl_product_code
            $this->syncWithProductMaster($package);

            // Sync tags
            $package->tags()->sync($request->input('tags', []));

            // Sync benefits
            $benefitsInput = [];
            if ($request->has('benefits_json')) {
                $benefitsInput = json_decode($request->input('benefits_json'), true) ?? [];
            }
            $this->syncBenefits($package, $benefitsInput);

            // Sync coupons
            $package->coupons()->sync($request->input('coupons', []));

            // Sync power types
            $package->powerTypes()->sync($request->input('power_types', []));

            // Sync categories, frame types, frame shapes, brands
            $package->categories()->sync($request->input('categories', []));
            $package->frameTypes()->sync($request->input('frame_types', []));
            $package->frameShapes()->sync($request->input('frame_shapes', []));
            $package->brands()->sync($request->input('brands', []));

            // Replace badges
            $badgesInput = [];
            if ($request->has('badges_json')) {
                $badgesInput = $request->input('badges_json');
            }
            $this->replaceBadges($package, $badgesInput);

            return response()->json([
                'success' => true,
                'message' => 'Lens package <strong>' . e($package->name) . '</strong> updated successfully.',
                'data'    => $package->load(['tags', 'benefits', 'badges', 'coupons']),
            ]);
        });
    }

    public function toggleStatus($id)
    {
        $package            = LensPackage::findOrFail($id);
        $package->is_active = !$package->is_active;
        $package->save();

        return response()->json([
            'success' => true,
            'message' => e($package->name) . ' is now ' . ($package->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $package->is_active,
        ]);
    }

    public function destroy($id)
    {
        $package = LensPackage::findOrFail($id);
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lens package deleted successfully.',
        ]);
    }

    /**
     * Upload multiple images for a lens package.
     */
    public function uploadMedia(Request $request, $id)
    {
        $package = LensPackage::findOrFail($id);

        $request->validate([
            'images'   => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ]);

        $uploaded = [];
        $destinationPath = public_path('website/uploads/lens_packages');

        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $currentMaxSort = $package->media()->max('sort_order') ?? -1;

        foreach ($request->file('images') as $index => $file) {
            $fileName = 'lp_' . $package->id . '_' . time() . '_' . $index . '.' . $file->getClientOriginalExtension();
            $file->move($destinationPath, $fileName);

            $media = $package->media()->create([
                'media_type' => 'image',
                'url'        => 'website/uploads/lens_packages/' . $fileName,
                'alt_text'   => $package->name,
                'sort_order' => ++$currentMaxSort,
            ]);

            $uploaded[] = $media;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploaded) . ' image(s) uploaded successfully.',
            'data'    => $uploaded,
        ]);
    }

    /**
     * Delete a single media item.
     */
    public function deleteMedia($mediaId)
    {
        $media = LensPackageMedia::findOrFail($mediaId);

        // Delete the physical file
        $filePath = public_path($media->url);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.',
        ]);
    }

    private function syncBenefits(LensPackage $package, ?array $benefits): void
    {
        if (is_null($benefits)) {
            return;
        }

        $syncData = [];
        foreach ($benefits as $index => $entry) {
            if (is_array($entry)) {
                $benefitId = $entry['id'] ?? $entry['benefit_id'] ?? null;
                if (!$benefitId) {
                    continue;
                }
                $syncData[$benefitId] = [
                    'sort_order'     => $entry['sort_order'] ?? $index,
                    'is_highlighted' => !empty($entry['is_highlighted']),
                ];
            } else {
                $syncData[$entry] = [
                    'sort_order'     => $index,
                    'is_highlighted' => false,
                ];
            }
        }
        $package->benefits()->sync($syncData);
    }

    private function replaceBadges(LensPackage $package, $badges): void
    {
        if (is_null($badges)) {
            return;
        }

        if (is_string($badges)) {
            $badges = json_decode($badges, true);
        }

        if (!is_array($badges)) {
            return;
        }

        $package->badges()->delete();

        foreach ($badges as $index => $badge) {
            if (empty($badge['label'])) {
                continue;
            }
            $package->badges()->create([
                'label'      => $badge['label'],
                'bg_color'   => $badge['bg_color']   ?? '#6c757d',
                'text_color' => $badge['text_color'] ?? '#ffffff',
                'sort_order' => $badge['sort_order'] ?? $index,
            ]);
        }
    }

    /**
     * Auto-sync LensPackage with ERP physical product master (tbl_product_code)
     * under product_type = 'Glass' so purchases and inventory tracking work seamlessly.
     */
    public function syncWithProductMaster(LensPackage $package): void
    {
        if (empty($package->product_code)) {
            return;
        }

        $detailsParts = array_filter([
            $package->name,
            $package->company,
            $package->quality,
            $package->material,
            $package->coating,
            $package->lens_index,
        ]);
        $productDetails = implode(' - ', $detailsParts);
        if (empty($productDetails)) {
            $productDetails = $package->name ?: $package->product_code;
        }

        $existing = DB::table('tbl_product_code')
            ->where('product_code', $package->product_code)
            ->first();

        if ($existing) {
            DB::table('tbl_product_code')
                ->where('id', $existing->id)
                ->update([
                    'product_name'        => $package->name,
                    'productdetails'      => $productDetails,
                    'product_type'        => 'Glass',
                    'Company'             => $package->company ?? $existing->Company,
                    'Quality'             => $package->quality ?? $existing->Quality,
                    'Index'               => $package->lens_index ?? $existing->Index,
                    'Coating'             => $package->coating ?? $existing->Coating,
                    'Material'            => $package->material ?? $existing->Material,
                    'Design'              => $package->design ?? $existing->Design,
                    'Retail_Price'             => (float) $package->current_price,
                    'Purchase_Price'           => (float) ($package->purchase_price ?? $existing->Purchase_Price ?? 0),
                    'Purchase_Base_Price'      => (float) ($package->purchase_price ?? $existing->Purchase_Base_Price ?? 0),
                    'Track_Inventory'          => (int) ($package->track_inventory ?? 1),
                    'Allow_Negative_Inventory' => (int) ($package->allow_negative_inventory ?? 1),
                    'updated_at'               => now(),
                ]);
        } else {
            do {
                $newProductId = random_int(100000, 999999);
            } while (DB::table('tbl_product_code')->where('product_id', $newProductId)->exists());

            DB::table('tbl_product_code')->insert([
                'product_id'               => $newProductId,
                'product_code'             => $package->product_code,
                'product_name'             => $package->name,
                'productdetails'           => $productDetails,
                'product_type'             => 'Glass',
                'Company'                  => $package->company,
                'Quality'                  => $package->quality,
                'Index'                    => $package->lens_index,
                'Coating'                  => $package->coating,
                'Material'                 => $package->material,
                'Design'                   => $package->design,
                'Retail_Price'             => (float) $package->current_price,
                'Purchase_Price'           => (float) ($package->purchase_price ?? 0),
                'Purchase_Base_Price'      => (float) ($package->purchase_price ?? 0),
                'Track_Inventory'          => (int) ($package->track_inventory ?? 1),
                'Allow_Negative_Inventory' => (int) ($package->allow_negative_inventory ?? 1),
                'status'                   => 1,
                'stock_status'             => 'in_stock',
                'stock_quantity'           => 0,
                'created_at'               => now(),
                'updated_at'               => now(),
            ]);
        }
    }

    /**
     * AJAX endpoint to auto-generate a unique Product Code / SKU.
     */
    public function generateProductCode(Request $request)
    {
        $code = $this->makeUniqueProductCode(
            $request->input('name', ''),
            $request->input('company', ''),
            $request->input('lens_index', ''),
            $request->input('coating', '')
        );

        return response()->json([
            'success'      => true,
            'product_code' => $code,
        ]);
    }

    /**
     * One-click generation of product code and sync with physical product master.
     */
    public function generateProductForPackage($id)
    {
        $package = LensPackage::findOrFail($id);

        if (empty($package->product_code)) {
            $package->product_code = $this->makeUniqueProductCode(
                $package->name,
                $package->company,
                $package->lens_index,
                $package->coating
            );
            $package->save();
        }

        $this->syncWithProductMaster($package);

        return response()->json([
            'success'      => true,
            'message'      => 'Physical ERP Product successfully generated for <strong>' . e($package->name) . '</strong> (Code: <code>' . e($package->product_code) . '</code>).',
            'product_code' => $package->product_code,
        ]);
    }

    /**
     * Generate a unique SKU / Product Code for Lens Packages.
     */
    public function makeUniqueProductCode(?string $name = '', ?string $company = '', ?string $index = '', ?string $coating = ''): string
    {
        $parts = ['GLS'];

        if (!empty($company)) {
            $compClean = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $company));
            if (strlen($compClean) >= 2) {
                $parts[] = substr($compClean, 0, 3);
            }
        } elseif (!empty($coating)) {
            $coatClean = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $coating));
            if (strlen($coatClean) >= 2) {
                $parts[] = substr($coatClean, 0, 3);
            }
        } elseif (!empty($name)) {
            $nameClean = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name));
            if (strlen($nameClean) >= 2) {
                $parts[] = substr($nameClean, 0, 3);
            }
        }

        if (!empty($index)) {
            $idxClean = preg_replace('/[^0-9]/', '', $index);
            if (strlen($idxClean) >= 2) {
                $parts[] = substr($idxClean, 0, 3);
            }
        }

        $basePrefix = implode('-', $parts);

        $attempts = 0;
        do {
            $rand = random_int(100, 9999);
            $code = $basePrefix . '-' . sprintf('%03d', $rand);

            $existsInProducts = DB::table('tbl_product_code')
                ->where('product_code', $code)
                ->exists();
            $existsInPackages = DB::table('lens_packages')
                ->where('product_code', $code)
                ->exists();

            $exists = $existsInProducts || $existsInPackages;
            $attempts++;
        } while ($exists && $attempts < 100);

        return $code;
    }
}
