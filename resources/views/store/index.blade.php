@extends('layouts.master')
@section('styles')
  
    
<style>
.ms-auto {
    margin-left: auto !important;
}
.spinner-grow {
    animation: spinner-grow .75s linear infinite;
    background-color: currentColor;
    border-radius: 50%;
    display: inline-block;
    height: 2rem;
    opacity: 0;
    vertical-align: -.125em;
    width: 2rem;
}
.spinner-border {
    animation: spinner-border .75s linear infinite;
    border: .25em solid;
    border-radius: 50%;
    border-right: .25em solid transparent;
    display: inline-block;
    height: 2rem;
    vertical-align: -.125em;
    width: 2rem;
}


.tooltip {
  position: relative;
  display: inline-block;
}

/* Tooltip text */
.tooltip .tooltiptext {
  visibility: hidden;
    width: 200px;
    background-color: black;
    color: #fff;
    /* text-align: center; */
    padding: 5px 0;
    border-radius: 6px;
    position: absolute;
    /* z-index: 1; */
    font-size: 10px !important;
}

/* Show the tooltip text when you mouse over the tooltip container */
.tooltip:hover .tooltiptext {
  visibility: visible;
}

.select2-container--default .select2-selection--multiple {
    background-color: #01a490 !important;
}
</style>
@endsection
@section('content')
 @php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="domestic-orders-header">
                    <h3>Store Details</h3>
                    @if ($usr->can('Store-Create'))
                    <a href="{{route('admin.store-add-page')}}" class=" btn">
                        <span><i class="fa fa-plus" title="" data-original-title="fa fa-plus"></i></span>
                        Create New Store
                    </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="card" style="margin-top:10px">
            <div class="card-body" style="padding: 5px 10px;">
               <div class="row mb-3">
                 <div class="col-lg-10">
                <div class="domestic-orders-date">
                    <div class="form-group">
                        <input type="text" class="form-control input" placeholder="Search by store id,store name" id="search" name="search" style="width: 300px;">
                    </div>
                </div>
            </div>
               </div>
                <div class="row">
                    <div class="col-12">
                        <div class="domestic-orders-table">
                            <div id="processingLoader" class="processing-loader" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <strong class="text-success">Please wait...</strong>
                                            <div class="spinner-border ms-auto text-success spinner-grow" role="status"
                                                aria-hidden="true"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table class="table datatables-basic  table-bordered">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">Store ID</th>
                                        <th class="wd-15p">Store Name</th>
                                        <th class="wd-15p">Contact No</th>
                                        <th class="wd-15p">Email</th>
                                        <th class="wd-15p">GST NO</th>
                                        <th class="wd-15p">City</th>
                                        <th class="wd-15p">Status</th>
                                        <th class="wd-15p">Created AT</th>
                                        <th class="wd-15p">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</section>

@endsection

@section('scripts')
<script>
    let dataListView = $('.datatables-basic')
        .on('preXhr.dt', function() {
            $('#processingLoader').show();
        })
        .on('draw.dt', function() {
            $('#processingLoader').hide();
        }).DataTable({

            "processing": true,
            "serverSide": true,
            "bFilter": false,
            "ajax": {
                "url": "{{ route('admin.store-data') }}",
                "dataType": "json",
                "type": "POST",
                "data": function(d) {
                    d.search1 = $('#search').val(),
                    d._token = "{{ csrf_token() }}";
                }
            },
            "columns": [
                {
                   "data": 'store_id',
                    orderable: false,
                },
                {
                    "data": "store_name",
                    orderable: false,
                },
                {
                    "data": "contact_no",
                    orderable: false,
                },
                {
                    "data": "email_id",
                    orderable: false,
                },
                {
                    "data": "gst_no",
                    orderable: false,
                },
                {
                    "data": "city_id",
                    orderable: false,
                },
                {
                    "data": "status",
                    orderable: false,
                },
                {
                    "data": "created_at",
                    orderable: false,
                },
                {
                     "data": "action",
                     orderable: false,
                    searchable: false
                 }
      
            ],

            searchDelay: 1500,
            columnDefs: [{
                    // For Responsive
                    className: 'control',
                    orderable: false,
                    responsivePriority: 2,
                    targets: 0
                },
                
            ],
            dom: '<"d-flex justify-content-between align-items-center mx-0 row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',

            language: {
                paginate: {
                    // remove previous & next text from pagination
                    previous: '&nbsp;',
                    next: '&nbsp;'
                },
                sLengthMenu: "_MENU_",
                sZeroRecords: "{{ __('No results available') }}",
                sSearch: "{{ __('search') }}",
                sProcessing: "{{ __('processing') }}",
                sInfo: "{{ __('Showing :start to :end of :total entries', ['start' => '_START_', 'end' => '_END_', 'total' => '_TOTAL_']) }}",
                sInfoFiltered: "" // Removes the "(filtered from xxx total entries)" text
            },
            responsive: {
                details: {
                    type: 'column',
                    renderer: function(api, rowIdx, columns) {
                        let data = $.map(columns, function(col) {
                            return col.title !==
                                '' // ? Do not show row in modal popup if title is blank (for check box)
                                ?
                                '<tr data-dt-column="' +
                                col.columnIndex +
                                '">' +
                                '<td>' +
                                col.title +
                                ':' +
                                '</td> ' +
                                '<td>' +
                                col.data +
                                '</td>' +
                                '</tr>' :
                                '';
                        }).join('');

                        return data ? $('<table class="table"/>').append('<tbody>' + data +
                            '</tbody>') : false;
                    }
                }
            },

            aLengthMenu: [
                [10, 20, 50, 100],
                [10, 20, 50, 100]
            ],
            select: {
                style: "multi"
            },
            order: [
                [2, "desc"]
            ],
            displayLength: 10,
        });
    let debounceTimer;
    $('.input').on('keyup', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function() {
            const column = dataListView.column($(this).attr('name'));
            column.search($(this).val()).draw();
        }.bind(this), 500);
    });


    $(document).on('change', '.toggle-switch', function(){
    let checkbox = $(this); 
    let id = checkbox.data('id');
    let field = checkbox.data('field');
    let value = checkbox.prop('checked') ? 1 : 0;
    $.ajax({
        url: '{{ route("admin.store.update.toggle") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            id: id,
            field: field,
            value: value
        },
        success: function(response) {
            if (response.success) {
                $.toaster({ priority : 'success', title : 'Success!!', message : response.message });
            } else {
                $.toaster({ priority : 'danger', title : 'Error!!', message : 'Failed to update. Please try again.' });
                checkbox.prop('checked', !value); 
            }
        },
        error: function() {
            $.toaster({ priority : 'danger', title : 'Error!!', message : "Something went wrong! Please try again." });
            checkbox.prop('checked', !value); 
        }
    });

});



</script>

@endsection
