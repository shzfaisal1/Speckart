<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PowerTypeController extends Controller
{
    public function index()
    {
        $setting['page_title'] = 'Power Types';
        return view('admin.powerType.index', $setting);
    }

    public function data()
    {
        $types = PowerType::query()->latest('id');

        return DataTables::of($types)
            ->addColumn('images', function ($row) {
                if ($row->images) {
                    return '<img src="' . asset('website/uploads/power_types/' . $row->images) . '" width="50" class="img-thumbnail">';
                }
                return '<span class="badge badge-light text-muted">No Image</span>';
            })
            ->addColumn('tag', function ($row) {
                if ($row->tag) {
                    return '<span class="badge badge-primary">' . htmlspecialchars($row->tag) . '</span>';
                }
                return '<span class="text-muted">—</span>';
            })
            ->addColumn('is_active', function ($row) {
                $checked = $row->is_active == '1' ? 'checked' : '';
                return '
                    <div class="toggle-btn">
                        <input type="checkbox" id="pt_' . $row->id . '" class="toggle-switch toggle-status"
                               data-id="' . $row->id . '" ' . $checked . '>
                        <label for="pt_' . $row->id . '">Toggle</label>
                    </div>';
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex" style="gap: 8px;">
                        <button class="btn btn-sm btn-success btn-edit"
                                data-id="' . $row->id . '"
                                data-toggle="modal"
                                data-target="#powerTypeModal"
                                onclick="openModal(\'edit\')">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete"
                                data-id="' . $row->id . '"
                                data-name="' . htmlspecialchars($row->description, ENT_QUOTES) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>';
            })
            ->rawColumns(['images', 'tag', 'is_active', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tag'         => 'nullable|string|max:100',
            'is_active'   => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active') ? '1' : '0';

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('website/uploads/power_types'), $filename);
            $validated['images'] = $filename;
        }
        unset($validated['image']);

        $powerType = PowerType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Power Type added successfully.',
            'data'    => $powerType,
        ]);
    }

    public function edit($id)
    {
        $powerType = PowerType::findOrFail($id);

        return response()->json([
            'id'          => $powerType->id,
            'description' => $powerType->description,
            'image'       => $powerType->images,
            'image_url'   => $powerType->images ? asset('website/uploads/power_types/' . $powerType->images) : null,
            'tag'         => $powerType->tag,
            'is_active'   => $powerType->is_active,
        ]);
    }

    public function update(Request $request, $id)
    {
        $powerType = PowerType::findOrFail($id);

        $validated = $request->validate([
            'description' => 'required|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tag'         => 'nullable|string|max:100',
            'is_active'   => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->has('is_active') ? '1' : '0';

        if ($request->hasFile('image')) {
            if ($powerType->images && file_exists(public_path('website/uploads/power_types/' . $powerType->images))) {
                @unlink(public_path('website/uploads/power_types/' . $powerType->images));
            }

            $file     = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('website/uploads/power_types'), $filename);
            $validated['images'] = $filename;
        }
        unset($validated['image']);

        $powerType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Power Type updated successfully.',
            'data'    => $powerType,
        ]);
    }

    public function toggleStatus($id)
    {
        $powerType            = PowerType::findOrFail($id);
        $powerType->is_active = $powerType->is_active == '1' ? '0' : '1';
        $powerType->save();

        return response()->json([
            'success' => true,
            'message' => 'Power Type is now ' . ($powerType->is_active == '1' ? 'Active' : 'Inactive') . '.',
            'status'  => $powerType->is_active,
        ]);
    }

    public function destroy($id)
    {
        $powerType = PowerType::findOrFail($id);

        if ($powerType->images && file_exists(public_path('website/uploads/power_types/' . $powerType->images))) {
            @unlink(public_path('website/uploads/power_types/' . $powerType->images));
        }

        $powerType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Power Type deleted successfully.',
        ]);
    }
}
