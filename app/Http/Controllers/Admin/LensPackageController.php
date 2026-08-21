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

                return '
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <img src="' . $imgUrl . '" alt="' . e($row->name) . '" class="rounded border" style="width: 40px; height: 40px; object-fit: cover; flex-shrink: 0; background: #f8f9fa;">
                        <div>
                            <strong class="text-dark d-block" style="font-size:13px;">' . e($row->name) . '</strong>
                            ' . $powerTypesText . '
                        </div>
                    </div>';
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
                        return '<span class="badge badge-success px-2 py-1" style="background-color:#198754; color:#fff; font-size:11px;">🟢 Free Lens</span>';
                    case 'free_frame':
                        return '<span class="badge badge-purple px-2 py-1" style="background-color:#6f42c1; color:#fff; font-size:11px;">🟣 Free Frame</span>';
                    case 'lens_only':
                        return '<span class="badge badge-info px-2 py-1" style="font-size:11px;">Lens Only</span>';
                    default:
                        return '<span class="badge badge-primary px-2 py-1" style="background-color:#0d6efd; color:#fff; font-size:11px;">🔵 Paid Combo</span>';
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
            ->rawColumns(['name', 'price', 'package_mode', 'tags_list', 'is_active', 'action'])
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
            'short_description' => 'nullable|string',
            'current_price'     => 'required|numeric|min:0',
            'original_price'    => 'nullable|numeric|min:0',
            'warranty_months'   => 'nullable|integer|min:0',
            'is_free_lens'      => 'nullable|boolean',
            'package_type'      => 'nullable|string|in:frame_and_lens,free_lens,free_frame,lens_only,frame_only',
            'is_active'         => 'nullable|boolean',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $validated['is_active']       = $request->boolean('is_active', false);
        $validated['package_type']    = $request->input('package_type', 'frame_and_lens');
        // Derive is_free_lens from package_type — single source of truth
        $validated['is_free_lens']    = ($validated['package_type'] === 'free_lens') ? 1 : 0;
        $validated['warranty_months'] = $validated['warranty_months'] ?? 0;
        $validated['sort_order']      = $validated['sort_order'] ?? 0;

        return DB::transaction(function () use ($validated, $request) {
            $package = LensPackage::create($validated);

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
            'short_description' => 'nullable|string',
            'current_price'     => 'required|numeric|min:0',
            'original_price'    => 'nullable|numeric|min:0',
            'warranty_months'   => 'nullable|integer|min:0',
            'is_free_lens'      => 'nullable|boolean',
            'package_type'      => 'nullable|string|in:frame_and_lens,free_lens,free_frame,lens_only,frame_only',
            'is_active'         => 'nullable|boolean',
            'sort_order'        => 'nullable|integer|min:0',
        ]);

        $validated['is_active']       = $request->boolean('is_active', false);
        $validated['package_type']    = $request->input('package_type', 'frame_and_lens');
        // Derive is_free_lens from package_type — single source of truth
        $validated['is_free_lens']    = ($validated['package_type'] === 'free_lens') ? 1 : 0;
        $validated['warranty_months'] = $validated['warranty_months'] ?? 0;
        $validated['sort_order']      = $validated['sort_order'] ?? 0;

        return DB::transaction(function () use ($package, $validated, $request) {
            $package->update($validated);

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
}
