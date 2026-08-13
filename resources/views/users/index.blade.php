@extends('layouts.master')

@section('styles')
@endsection

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <p class="float-right mb-2">
                            <a class="btn btn-primary text-white" href="{{ url('users/create') }}">Create New
                                User</a>
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('layouts.partials.messages')
                            <div class="domestic-orders-table">
                             <table class="table datatables-basic  table-bordered" id="dataTable" >
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Roles</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @foreach ($users as $user)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <div class="toggle-btn">
                                                    <input type="checkbox" id="user_{{ $user->id }}" class="toggle-switch" data-id="{{ $user->id }}" data-field="status" {{ $user->status ? 'checked' : '' }}>

                                                    <label for="user_{{$user->id }}">Toggle</label>
                                                </div>
                                            </td>
                                            <td>
                                                @foreach ($user->roles as $role)
                                                    <span class="badge bg-primary mr-1">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </td>
                                            <td>
                                               
                                                @if ($user->user_type != 'Admin')
                                                    <a class="btn btn-success text-white" href="{{ route('admin.users.edit', $user->id) }}">Edit</a>
                                                @else
                                                    <button class="btn btn-success text-white" disabled>Edit</button>
                                                @endif

                                                @can('role-delete')
                                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                                    style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                     @if ($user->user_type != 'Admin')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>Delete</button>
                                                    @else
                                                        <button  class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i>Delete</button>
                                                    @endif
                                                    
                                                </form>
                                            @endcan
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>

    {{-- @session('success')
        <div class="alert alert-success" role="alert">
            {{ $value }}
        </div>
    @endsession --}}

    {!! $users->links('pagination::bootstrap-5') !!}
@endsection


@section('scripts')
<script>
    /*================================
                                    datatable active
                                    ==================================*/
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            "responsive": true,
            'lengthMenu': [
                [10, 10, 25, 50, -1],
                [10, 10, 25, 50, 'All']
            ],
            "lengthChange": false,
            "autoWidth": false,
            dom: '<""<"row"<"col"B><"col"f>>>rtip',
            "buttons": ['pageLength', "copy", "csv", "excel", "pdf", "print", "colvis"],
        });
    }
    
    
</script>
@endsection
