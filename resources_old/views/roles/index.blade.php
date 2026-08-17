@extends('layouts.master')

@section('content')
    <div class="main-content-inner">
        <div class="row">
            <!-- data table start -->
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-body">
                        <p class="float-right mb-2">
                                <a class="btn btn-primary btn-sm mb-2" href="{{ route('admin.roles.create') }}"><i
                                        class="fa fa-plus"></i> Create
                                    New
                                    Role
                                </a>
                        </p>
                        <div class="clearfix"></div>
                        <div class="data-tables">
                            @include('layouts.partials.messages')
                            <table class="table datatables-basic w-100" id="dataTable">
                                <thead>
                                    <tr>
                                        <th width="100px">No</th>
                                        <th>Name</th>
                                        <th width="280px">Action</th>
                                    </tr>
                                </thead>
                                @foreach ($roles as $key => $role)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $role->name }}</td>
                                        <td> 
                                               @if ($role->name != 'Admin' && $role->name != 'Warehouse')
                                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.roles.edit', $role->id) }}"><i class="fa fa-pencil"></i> Edit</a>
                                                @else
                                                    <a class="btn btn-primary btn-sm" href="{{ route('admin.roles.edit', $role->id) }}"><i class="fa fa-pencil"></i> Edit</a>
                                                @endif
     
                                                
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                                    style="display:inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    @if ($role->name != 'Admin' && $role->name != 'Warehouse')
                                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i>Delete</button>
                                                    @else
                                                        <button class="btn btn-danger btn-sm" disabled><i class="fa fa-trash"></i>Delete</button>
                                                    @endif
                                                    
                                                </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>

                            {!! $roles->links('pagination::bootstrap-5') !!}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- data table end -->

        </div>
    </div>
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
