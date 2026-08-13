<!doctype html>
<html><head>
    <title>INVOICE</title>
    <link rel="shortcut icon" href="{{asset('frontend/asset/img/logo/Fevicon-speckart.png')}}" type="image/png">
    <!-- Font Awesome 4.7.0 Css -->
    <link href="{{ asset('assets/plugins/iconfonts/font-awesome/font-awesome.min.css') }}" rel="stylesheet">
    <!-- JQuery Min JS -->
    <script src="{{ asset('assets/js/vendors/jquery-3.4.0.min.js') }}"></script>
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
</head>
<body>
    
    
    <div class="main-wrapper dejavusans">
       <div id="printSection">
           <div class="format-a4">
               <div class="print-header">
                    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="print-header-table">
                        <tbody>
                            <tr>
                                <td width="40%" style="vertical-align:top;">
                                    <img src="{{asset('frontend/asset/img/logo/Specskart-logo.png')}}" height="110px" width="250px">
                                </td>
                                <td width="60%" align="right">
                                    <span class="rtl-fix" style="font-size: 14px;"><span>Reference No</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$refrence_no->refrence_no}}</span><br>
                                    <span class="rtl-fix" style="font-size: 14px;"><span>Date</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$refrence_no->created_at}}</span><br>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="full-line"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <table width="100%" cellspacing="1" style="margin-top: 0px;">
                    <tbody>
                        <tr>
                            <td width="53%" style="vertical-align: top; font-size:10pt;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        
                                        <tr>
                                            <td style="font-size: 16px; text-decoration: underline;" colspan="2"><span>FROM STORE</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><strong> {{$fromstore->store_name}}</strong> </b></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2"><b><span class="ltr-fix">{{$fromstore->store_address}}, {{$fcity->city_name}} - {{$fromstore->pincode}}, {{$fstate->state_name}}, India</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Mobile No </span></span> : <span class="ltr-fix">{{$fromstore->contact_no}}</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Email Id</span></span> : </b><span class="ltr-fix">{{$fromstore->email_id}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>GST NO</span></span></b> : <span class="ltr-fix">{{$fromstore->gst_no}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="47%" style="vertical-align: top; font-size:10pt;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        
                                        <tr>
                                            <td style="font-size: 16px; text-decoration: underline;" colspan="2"><span>TO STORE</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><strong> {{$tostore->store_name}}</strong> </b></td>
                                        </tr>
                                        
                                        <tr>
                                            <td colspan="2"><b><span class="ltr-fix">{{$tostore->store_address}}, {{$tcity->city_name}} - {{$tostore->pincode}}, {{$tstate->state_name}}, India</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Mobile No </span></span> : <span class="ltr-fix">{{$tostore->contact_no}}</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Email Id</span></span> : </b><span class="ltr-fix">{{$tostore->email_id}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>GST NO</span></span></b> : <span class="ltr-fix">{{$tostore->gst_no}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                   </tbody>
                </table>
                <hr/>
                <table cellspacing="0" cellpadding="1" class="print-table" style="font-size:9pt;width:100%;">
                    <tbody>
                        <tr style="font-weight: bold; font-size:9.5pt">
                            <td width="8%">SL NO</td>
                            <td>PRODUCT DETAILS</td>
                            <td width="8%">QUANTITY</td>
                            <td width="12%">AVERAGE PRICE</td>
                            <td width="12%">AMOUNT</td>
                        </tr>
                
                        @php   
                            $transferproduct = DB::table('tbl_transfer_stock')
                                ->where('refrence_no', $refrence_no->refrence_no)
                                ->select(
                                    'refrence_no',
                                    'product_type',
                                    'product_code',
                                    'product_details',
                                    'perbox',
                                    DB::raw('SUM(purchase_price) as total_purchase'),
                                    DB::raw('SUM(retail_price) as total_retail'),
                                    DB::raw('COUNT(*) as total_items')
                                )
                                ->groupBy('refrence_no', 'product_type', 'product_code', 'product_details', 'perbox')
                                ->get();
                
                            // Initialize totals
                            $grandTotalQty = 0;
                            $grandTotalAmount = 0;
                            $sumOfAveragePrice = 0;
                        @endphp        
                
                        @foreach($transferproduct as $i => $product)    
                            @php
                                // Average purchase price per product
                                $averagePrice = $product->total_purchase / $product->total_items;
                
                                // Update grand totals
                                $grandTotalQty += $product->total_items;
                                $grandTotalAmount += $product->total_purchase;
                                $sumOfAveragePrice += $averagePrice;
                            @endphp
                
                            <tr style="font-size:9pt;">
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ $product->product_code }} - {{ $product->product_details }}<br>
                                    @php   
                                        $barcodes = DB::table('tbl_transfer_stock')
                                            ->where('refrence_no', $refrence_no->refrence_no)
                                            ->where('product_details', $product->product_details)
                                            ->where('product_code', $product->product_code)
                                            ->where('product_type', $product->product_type)
                                            ->pluck('barcode_no');
                                    @endphp 
                                    {{ implode(', ', $barcodes->toArray()) }}
                                </td>
                                <td align="center">{{ $product->total_items }}</td>
                                <td align="center">Rs {{ number_format($averagePrice, 2) }}</td>
                                <td align="center">Rs {{ number_format($product->total_purchase, 2) }}</td>
                            </tr> 
                        @endforeach
                
                        {{-- ✅ Grand Total Row --}}
                        <tr style="font-weight:bold; font-size:10pt; border-top:1px solid #000;">
                            <td colspan="2" align="center">Total</td>
                            <td align="center">{{ $grandTotalQty }}</td>
                            <td align="center">Rs {{ number_format($sumOfAveragePrice, 2) }}</td>
                            <td align="center">Rs {{ number_format($grandTotalAmount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                
                 <table width="100%" border="0" style="font-size:13pt; font-weight:bold;margin-top:3px;">
                    <tbody>
                        <tr>
                            <td style=" width:50%;" > <strong>Comment : </strong> {{$refrence_no->transfer_comment}}</td>
                            <td style=" width:50%;" align="right" width="auto" style="font-weight: bold;">&nbsp;<div style="height:40px;"></div><span>AUTHORISED SIGNATURE</span></td>
                        </tr>
                    </tbody>
                </table>
    
               
                
                
            </div>
        </div>
    </div>
    <script>
        function splitPrintSection(maxHeightMM) {
            var mmToPx = maxHeightMM * 3.78; // convert mm to px
            var $section = $("#printSection");

            var $content = $section.children().clone(); // Clone all child nodes

            $section.empty(); // Clear print section
            var $currentBox = $("<div class='format-a4'></div>").appendTo($section);

            var $temp = $("<div>").css({
                position: "absolute",
                visibility: "hidden",
                top: "-9999px",
                width: "200mm"
            }).appendTo("body");

            $content.each(function() {
                if ($(this).hasClass("page-break")) {
                    $currentBox.append($(this));
                    $temp.empty();
                    $currentBox = $("<div class='format-a4'></div>").appendTo($section);
                    return;
                }

                $temp.append($(this).clone());

                if ($temp.outerHeight(true) > mmToPx) {
                    $temp.empty().append($(this).clone());
                    $currentBox = $("<div class='format-a4'></div>").appendTo($section);
                }
                $currentBox.append($(this));
            });
            $temp.remove();
            var $watermark = $("#watermark-order-formAR");

            if ($watermark.length) {
                var $reference = $("#print-order-form");
                if ($reference.length) {
                    $watermark.appendTo($reference);
                }
            }

        }

        $(window).on("load", function () {
            splitPrintSection( 292);
        });

    </script>

</body>
</html>
