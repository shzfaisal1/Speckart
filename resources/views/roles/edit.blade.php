@extends('layouts.master')

@section('styles')
    <style>
        .container {
            display: flex;
            gap: 16px;
            /* Space between columns */
            padding: 16px;
        }

        .category {
            flex: 1;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 4px;
            background-color: #f9f9f9;
        }

        .category h2 {
            margin-top: 0;
            font-size: 1.2em;
        }

        .action-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .action-list li {
            padding: 4px 0;
        }
    </style>
@endsection

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header btn-primary">
                        <h5>{{ $page_title }}</h5>
                    </div>
                    <div class="card-body">
                        @include('layouts.partials.messages')
                        <div class="col-lg-12 margin-tb">
                            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                                @csrf
                                @method('PUT')
                        
                                <div class="row">
                        
                                    {{-- Role Name --}}
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Name:</strong>
                                            <input type="text" name="name" placeholder="Name" class="form-control"
                                                value="{{ $role->name }}">
                                        </div>
                                    </div>
                        
                                    {{-- Permissions Table --}}
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="form-group">
                                            <strong>Permission:</strong>
                        
                                            @php
                                                $grouped = [];
                                                foreach ($permission as $action) {
                                                    [$category, $specificAction] = array_pad(explode('-', $action->name, 2), 2, '');
                                                    if (!isset($grouped[$category])) {
                                                        $grouped[$category] = [];
                                                    }
                                                    $grouped[$category][] = [
                                                        'id' => $action->id,
                                                        'name' => $action->name,
                                                    ];
                                                }
                                            @endphp
                        
                                            <div class="table-responsive mt-2">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="table-dark text-center">
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Permissions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($grouped as $category => $actions)
                                                            <tr>
                                                                <td style="vertical-align: top; font-weight: 600;">
                                                                    {{ ucfirst($category) }}
                                                                </td>
                                                                <td>
                                                                    @foreach ($actions as $action)
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input"
                                                                                   type="checkbox"
                                                                                   name="permission[{{ $action['id'] }}]"
                                                                                   value="{{ $action['id'] }}"
                                                                                   id="perm_{{ $action['id'] }}"
                                                                                   {{ in_array($action['id'], $rolePermissions) ? 'checked' : '' }}>
                                                                            <label class="form-check-label"
                                                                                   for="perm_{{ $action['id'] }}">
                                                                                {{ $action['name'] }}
                                                                            </label>
                                                                        </div>
                                                                    @endforeach
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                        
                                    {{-- Submit Button --}}
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <button type="submit" class="btn btn-primary mb-3">
                                            <i class="fa-solid fa-floppy-disk"></i> Update Role
                                        </button>
                                    </div>
                        
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>
@endsection
