<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LensBenefit;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class LensBenefitController extends Controller
{
    public function index()
    {
        $setting['page_title'] = 'Lens Benefits';
        return view('admin.lens_benefits.list', $setting);
    }

    public function data()
    {
        $benefits = LensBenefit::withCount('lensPackages')->latest('id');

        return DataTables::of($benefits)
            ->addColumn('icon_image', function ($row) {
                if ($row->icon_image) {
                    $url = asset('website/uploads/lens_benefits/' . $row->icon_image);
                    return '<img src="' . $url . '" alt="' . e($row->name) . '" width="40" height="40" class="rounded">';
                }
                return '<span class="text-muted">No Image</span>';
            })
            ->addColumn('description', function ($row) {
                return $row->description ? Str::limit($row->description, 60) : '—';
            })
            ->addColumn('lens_packages_count', function ($row) {
                $count = $row->lens_packages_count;
                $badge = $count > 0 ? 'badge-primary' : 'badge-light text-muted';
                return '<span class="badge ' . $badge . '">' . $count . '</span>';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="benefit_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="benefit_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit-benefit"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#benefitModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete-benefit"
                                data-id="' . $row->id . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['icon_image', 'lens_packages_count', 'is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon_emoji'  => 'nullable|string|max:20',
            'icon_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('icon_image')) {
            $file     = $request->file('icon_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('website/uploads/lens_benefits'), $filename);
            $validated['icon_image'] = $filename;
        } else {
            unset($validated['icon_image']);
        }

        $benefit = LensBenefit::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lens benefit <strong>' . e($benefit->name) . '</strong> added successfully.',
            'data'    => $benefit,
        ]);
    }

    public function edit($id)
    {
        $benefit = LensBenefit::findOrFail($id);

        $benefit->icon_image_url = $benefit->icon_image
            ? asset('website/uploads/lens_benefits/' . $benefit->icon_image)
            : null;

        return response()->json($benefit);
    }

    public function update(Request $request, $id)
    {
        $benefit = LensBenefit::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string',
            'icon_emoji'  => 'nullable|string|max:20',
            'icon_image'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('icon_image')) {
            if ($benefit->icon_image && file_exists(public_path('website/uploads/lens_benefits/' . $benefit->icon_image))) {
                @unlink(public_path('website/uploads/lens_benefits/' . $benefit->icon_image));
            }
            $file     = $request->file('icon_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('website/uploads/lens_benefits'), $filename);
            $validated['icon_image'] = $filename;
        } else {
            unset($validated['icon_image']);
        }

        $benefit->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Lens benefit <strong>' . e($benefit->name) . '</strong> updated successfully.',
            'data'    => $benefit,
        ]);
    }

    public function toggleStatus($id)
    {
        $benefit            = LensBenefit::findOrFail($id);
        $benefit->is_active = !$benefit->is_active;
        $benefit->save();

        return response()->json([
            'success' => true,
            'message' => e($benefit->name) . ' is now ' . ($benefit->is_active ? 'Active' : 'Inactive') . '.',
            'status'  => $benefit->is_active,
        ]);
    }

    public function destroy($id)
    {
        $benefit = LensBenefit::findOrFail($id);

        $packageCount = $benefit->lensPackages()->count();
        if ($packageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete — ' . $packageCount . ' lens package(s) are using this benefit.',
            ], 422);
        }

        if ($benefit->icon_image && file_exists(public_path('website/uploads/lens_benefits/' . $benefit->icon_image))) {
            @unlink(public_path('website/uploads/lens_benefits/' . $benefit->icon_image));
        }

        $benefit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lens benefit deleted successfully.',
        ]);
    }
}
