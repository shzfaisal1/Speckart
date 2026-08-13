@extends('layouts.master')
@section('styles')
    <style>
        body {
            margin: 0;
            padding: 0;
        }
        @media print {
            @page {
                margin: 0;
            }
            #print-container {
                margin: 0;
                padding: 0;
            }

            .no-print { display: none !important; }

            body * {
                visibility: hidden !important;
            }

            #printSection, #printSection * {
                visibility: visible !important;
            }

            #printSection {
                margin: 0;
                padding: 0;
                z-index: 999;
            }

            /* === Receipt Formats (print view) === */
            .format-a4 {
                position: relative;
                border: none !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            .watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: 60pt;
                color: rgba(0, 0, 0, 0.15);
                font-weight: bold;
                pointer-events: none;
                z-index: 0;
            }
            .watermark-order-formAR {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%) rotate(-45deg);
                font-size: pt;
                color: rgba(0, 0, 0, 0.15);
                font-weight: bold;
                pointer-events: none;
                z-index: 0;
            }
            .page-break {
                border: none !important;
                margin: 0 !important;
                page-break-before: always; /* Older browsers */
                break-before: page;        /* Modern browsers */
            }

            table.print-table, .print-table th, .print-table td {
                border: 1px solid black;
            }

            .print-table th, .print-table td {
                padding: 3px;
            }

            .print-td-table {
                border: 1px solid black;
            }
            #print-order-form {
                position: relative;
            }
            td {
                white-space: normal; /* allow wrapping */
                word-wrap: break-word;
            }
        }

        @-moz-document url-prefix() {
            @media print {
                table.print-table, .print-table th, .print-table td {
                    box-shadow: 0 0 0 1px black;
                }
                .print-td-table {
                    box-shadow: 0 0 0 1px black;
                }
            }
        }

        .print-td-table {
            border: 1px solid black;
        }

        table.print-table, .print-table th, .print-table td {
            border: 1px solid black;
        }

        .print-table th, .print-table td {
            padding: 3px;
        }

        .arial { font-family: Arial, sans-serif; }
        .verdana { font-family: Verdana, sans-serif; }
        .times { font-family: 'Times New Roman', serif; }
        .dejavusans {
            font-family: 'DejaVuSans', sans-serif;
        }
        .arabic {
            font-family: 'DejaVuSans', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
        }

        /* === Receipt Formats === */

        .format-a4 {
            position: relative;
            border-bottom : 1px solid #000;
            padding-bottom:10px;
            margin-top: 5mm;
            margin-left: 5mm;
            margin-right: 5mm;

        }

        .main-wrapper {
            margin: 0 auto;
            position: relative;
            width: 200mm;
            box-sizing: border-box;
            border: 1px solid #000; /* visible only on screen */
        }
        .watermark {
            position: absolute;
            top: 45%;
            left: 45%;
            transform: translate(-45%, -45%) rotate(-45deg);
            font-size: 60pt;
            color: rgba(0, 0, 0, 0.15);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        .watermark-order-formAR {
            position: absolute;
            top: 45%;
            left: 45%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: pt;
            color: rgba(0, 0, 0, 0.15);
            font-weight: bold;
            pointer-events: none;
            z-index: 0;
        }
        .page-break {
            margin: 15px;
        }
        .print-btn, .download-btn {
            background-color: #f44547;
            color: white;
            border: none;
            padding: 10px 18px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 3px 6px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }

        .print-btn:hover, .download-btn:hover {
            background-color: #ff4b4b;
        }

        .print-btn i, .download-btn i {
            margin-right: 2px;
        }
        .action-buttons {
            position: fixed;
            top: 15px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .ltr-fix {
            direction: ltr;
            unicode-bidi: embed; /* or isolate for newer browsers */
            display: inline-block;
        }
        .rtl-fix {
        }
        .full-line {
            border-top: 2px solid #000;
            position: absolute;
            left: 0;
            right: 0;
        }
        #separator-line {
            border-top: 0.5px dashed rgb(190, 190, 190);
            margin: 10px 0;
        }
        #print-order-form {
            position: relative;
        }
        td {
            white-space: normal; /* allow wrapping */
            word-wrap: break-word;
            vertical-align: top;
        }
        .print-header-table {
            margin-bottom : 5px;
        }
    </style>

@endsection
@section('content')
@php
     $usr = Auth::guard()->user();
 @endphp
<section class="domestic-orders mt-0">
    <div class="container-fluid">
        <div class="card">
            <div class="row">
                <div class="col-lg-12">
                    <div class="domestic-orders-header">
                        <h3>Daily Statement</h3>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                 <div class="col-md-4">
                    <label for="">Select  Date <span class="text-danger">*</span></label>
                    <div id="reportrange" class="pull-left"
                        style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                        <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                        <span></span> <b class="caret"></b>
                    </div>
                    <input type="hidden" class="form-control" id="date_from" name="date_from">
                    <input type="hidden" class="form-control" id="date_to" name="date_to">
                </div> 
                <div class="col-lg-4">
                    <label for="">Select Store <span class="text-danger">*</span></label>
                    <select class="form-control select" style="height: 32px !important" id="store_id" name="store_id" >
                        <option value="">Select  Store</option>
                      <?php $tbl_store =  DB::table("tbl_store")->where('status',1)->get();  ?>
                       @foreach($tbl_store as $tbl_store)
                        <option value="{{$tbl_store->id}}">{{$tbl_store->store_name}} / ({{$tbl_store->store_id}})</option>
                      @endforeach
                    </select>
                    <span class="error badge text-danger" id="store_idError"></span>
                </div> 
                <div class="col-lg-3">
                    <label for="">Sales Type <span class="text-danger">*</span></label><br>
                    <select class="form-control select" style="height: 32px !important;" id="sales_type" name="sales_type">
                      <option value="">Select Sales Type</option>    
                      <option value="0">B2C</option>    
                      <option value="1">B2B</option>
                    </select>
                    <span class="error badge text-danger" id="sales_typeError"></span>
                    
                </div>
            </div>
            <div class="form-group">
                <button class="btn btn-gradient" id="daily" type="button">Search</button>
            </div> 
            <br>
            <div id="salesstatement"></div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
   $(document).ready(function() {
        $('.select').select2({
          allowClear: true
        });
    });
    
    var start = moment(); // Lifetime start date
    var end = moment(); // Today
    
    function isCurrentMonth(date) {
        return date.month() === moment().month() && date.year() === moment().year();
    }
    
    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $('#date_from').val(start.format('YYYY-MM-DD'));
        $('#date_to').val(end.format('YYYY-MM-DD'));
    
        if (isCurrentMonth(start) || isCurrentMonth(end)) {
            console.log("Start or end date is in the current month.");
        } else {
            console.log("Neither date is in the current month.");
        }
    

    }
    
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        autoUpdateInput: false,
        showDropdowns: true,
        maxDate: moment(),
        ranges: {
            'Today': [moment(), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
            
        }
    }, function(start, end) {
        cb(start, end);
    });
    
    // Update on apply
    $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
        cb(picker.startDate, picker.endDate);
    });
    
    // Set initial range to Lifetime on load
    cb(start, end);

    $("#daily").on('click', function () {
        let store_id = $("#store_id").val().trim();
        let sales_type = $("#sales_type").val().trim();
        let date_from = $("#date_from").val();
        let date_to = $("#date_to").val();
    
        let hasError = false;
    
        if (!store_id) {
            $("#store_idError").text("Please select store.");
            hasError = true;
        }
    
        if (!sales_type) {
            $("#sales_typeError").text("Please select sales type.");
            hasError = true;
        }
    
        if (hasError) return;
    
        $('#salesstatement').empty();
    
        $.ajax({
            url: "{{ route('admin.sale-statement-record') }}",
            type: "GET",
            data: {
                store_id: store_id,
                sales_type: sales_type,
                date_from: date_from,
                date_to: date_to
            },
            beforeSend: function () {
                $("#daily").prop("disabled", true).text("Searching...");
            },
            success: function (response) {
                $('#salesstatement').html(response); // HTML response
            },
            error: function () {
                alert("An error occurred. Please try again.");
            },
            complete: function () {
                $("#daily").prop("disabled", false).text("Search");
            }
        });
    });

    

</script>




@endsection
