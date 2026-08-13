<!doctype html>
<html><head>
    <title>RECEPIT</title>
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
        td {
            vertical-align: top;
        }
        .print-header-table {
            margin-bottom : 5px;
        }
    </style>
</head>
<body>
    <div class="no-print action-buttons">
        <button class="print-btn" onclick="window.print()"><i class="fa fa-print fa-fw"></i>Print</button>
        <a href="{{ route('admin.recepit.pdf', ['id' => $voucher_id]) }}" class="btn btn-primary">
            <button class="download-btn" style="background-color:#3192d6;">
                <i class="fa fa-download fa-fw"></i>Download
            </button>
        </a>
    </div>
    
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
                            <span style="font-size: 22px; font-weight: bold;">{{$store->store_name}}</span><br>
                            <span style="font-size: 14px;">{{$store->store_address}}, {{$city->city_name}} - {{$store->pincode}}, {{$state->state_name}}, India</span><br>
                            <span class="rtl-fix" style="font-size: 14px;"><span>Mobile No</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->contact_no}}</span><br>
                            <span class="rtl-fix" style="font-size: 14px;"><span>Email ID</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->email_id}}</span><br>
                            <span class="rtl-fix" style="font-size: 14px;"><span>GST Number</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->gst_no}}</span>
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
        <div style="font-size: 15pt; font-weight: bold; text-decoration: underline;" align="center"></div>
        <div class="watermark"></div>
        <table width="100%" cellspacing="0" cellpadding="3" border="0" style="font-size:9pt; margin-top:5px;">
             <tbody>
                <tr>
                    <td width="20%" class="print-td-table"><span><span>Date</span></span> : </td>
                    <td width="40%" class="print-td-table">{{ date("d-m-Y", strtotime($voucher->voucher_date))}}</td>
                    <td width="10%" class="print-td-table" rowspan="5" align="center" style="font-size: 10px;"><span><span>Amount</span></span> :</td>
                    <td width="30%" rowspan="5" align="center" valign="bottom" class="print-td-table" style="font-weight: bold; font-size: 15px;">Rs {{$voucher->total_amount}}</td>
                </tr>
                <tr>
                    <td class="print-td-table"><span><span>Voucher Number</span></span> : </td>
                    <td class="print-td-table">{{$voucher->total_amount}}</td>
                </tr>
                
                <tr>
                    <td class="print-td-table"><span><span>Purpose</span></span> : </td>
                    <td class="print-td-table">{{$voucher->purpose}}</td>
                </tr>
                <tr>
                    <td colspan="2" class="print-td-table"><span><span>Amount in Words</span></span> : {{ numberToWords($voucher->total_amount) }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="print-td-table"><span><span>Payment Remark</span></span> : {{$voucher->pay_remark}}</td>
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
        }

        $(window).on("load", function () {
            splitPrintSection( 292);
        });

    </script>

</body>
</html>