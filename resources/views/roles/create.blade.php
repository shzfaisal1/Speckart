@extends('layouts.master')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">

                    <div class="card-header btn-primary">
                        <h5 class="">{{ $page_title }}</h5>
                    </div>
                    <div class="card-body">
                        @include('layouts.partials.messages')
                        <div class="col-lg-12 margin-tb">
                            <form method="POST" action="{{ route('admin.roles.store') }}">
                                @csrf
                        
                                @php
                                    // Group permissions by category
                                    $grouped = [];
                                    foreach ($permission as $action) {
                                        [$category, $specificAction] = array_pad(explode('-', $action->name, 2), 2, '');
                                        $grouped[$category][] = [
                                            'id' => $action->id,
                                            'name' => $action->name
                                        ];
                                    }
                                @endphp
                        
                                <div class="form-group mb-3">
                                    <strong>Name:</strong>
                                    <input type="text" name="name" placeholder="Role Name" class="form-control">
                                </div>
                        
                                <div class="table-responsive">
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
                                                                       id="perm_{{ $action['id'] }}">
                                                                <label class="form-check-label" for="perm_{{ $action['id'] }}">
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
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                        <button type="submit" class="btn btn-primary mb-3"><i
                                                class="fa-solid fa-floppy-disk"></i>
                                            Create Role</button>
                                    </div>
                                    
                            </form>        
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>
@endsection
