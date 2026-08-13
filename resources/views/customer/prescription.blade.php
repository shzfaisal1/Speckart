<!doctype html>
<html><head>
    <title>Prescription </title>
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
    <div class="no-print action-buttons">
        <button class="print-btn" onclick="window.print()"><i class="fa fa-print fa-fw"></i>Print</button>
        <a href="{{ route('admin.prescription.pdf', ['id' => $testid, 'idd' => $printtype]) }}" class="btn btn-primary">
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
                    <td width="40%" style="vertical-align:top;"><img src="{{asset('frontend/asset/img/logo/Specskart-logo.png')}}" height="110px" width="250px"></td>
                    <td width="60%" align="right">
                        <span style="font-size: 22px; font-weight: bold;">{{$store->store_name}}</span><br>
                        <span style="font-size: 14px;">{{$store->store_address}}, {{$city->city_name}} - {{$store->pincode}}, {{$state->state_name}}, India</span><br>
                        <span class="rtl-fix" style="font-size: 14px;"><span>Mobile No</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->contact_no}}</span><br>
                        <span class="rtl-fix" style="font-size: 14px;"><span>Email ID</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->email_id}}</span><br>
                        <span class="rtl-fix" style="font-size: 14px;"><span>GST NUMBER</span> : </span><span style="font-size: 14px;" class="ltr-fix">{{$store->gst_no}}</span></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="full-line"></div>
                    </td>
                </tr>
           </tbody>
       </table>
            </div>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 0px;line-height:17px;">
                <tbody>
                    <tr>
                        <td style="font-size: 16pt; font-weight: bold; text-decoration: underline;" colspan="2" align="center"><span>EYEWEAR PRESCRIPTION</span></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top; font-size: 10pt; width:49%"><br>
                            <b><span class="rtl-fix"><span>Patient Name</span></span></b> : <span class="ltr-fix">{{$eye_test->cust_name}}</span><br>
                            <b><span class="rtl-fix"><span>Doctor / Optometrist Name</span></span></b> : <span class="ltr-fix">{{$eye_test->optometrist}}</span><br>
                            <b><span class="rtl-fix"><span>Customer Name</span></span></b> : <span class="ltr-fix">{{$eye_test->cust_name}}</span><br>
                            <b><span class="rtl-fix"><span>Mobile Number</span></span></b> : <span class="ltr-fix">{{$eye_test->contact_no}}</span><br>
                            <b><span class="rtl-fix"><span>Customer Age</span></span></b> : <span class="ltr-fix">{{$eye_test->age_group}}</span><br>
                            <b><span class="rtl-fix"><span>Customer Gender</span></span></b> : <span class="ltr-fix">{{$eye_test->gender}}</span></td>
                            <td style="vertical-align: top; font-size: 10pt; width:49%"><br>
                            <b><span class="rtl-fix"><span>Date &amp; Time</span></span></b> : <span class="ltr-fix">{{ date("d-m-Y h:i:A", strtotime($eye_test->created_at))}}</span><br>
                            <b><span class="rtl-fix"><span>Sales Person</span></span></b> : <span class="ltr-fix">{{$salePerson->name}}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="watermark"></div>
            <br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="print-header-table">
                <span>Single Vision Power</span>
                <tr>
                    <td width="55%" style="vertical-align:top;">
                        
                            <table cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                                <tbody>
                                    <tr style="background-color: RGB(210, 210, 210);" align="center">
                                        <td width="9%" style="border: 1px solid black;">Rx</td>
                                        <td width="9.1%" style="border: 1px solid black;">SPH</td>
                                        <td width="9.1%" style="border: 1px solid black;">CYL</td>
                                        <td width="9.1%" style="border: 1px solid black;">AXIS</td>
                                        <td width="9.1%" style="border: 1px solid black;">PD</td>
                                    </tr>
                                    <tr align="center">
                                        <td style="border: 1px solid black; background-color: RGB(210, 210, 210);">RE</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_sph_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_cyl_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_axis_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->pd_re_new}}</td>
        
                                    </tr>
                                     <tr align="center">
                                        <td style="border: 1px solid black; background-color: RGB(210, 210, 210);">LE</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_sph_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_cyl_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_axis_new}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->pd_le_new}}</td>
        
                                    </tr>
                                    
                                </tbody>
                            </table>
                    </td>
                    <td width="5%" style="vertical-align:top;"></td>
                    <td width="40%" style="vertical-align:top;">
                        <table width="100%" cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                            <tbody>
                                <tr style="background-color: RGB(210, 210, 210);" align="center">
                                    <td  style="border: 1px solid black;">Distance Vision</td>
                                    <td  style="border: 1px solid black;">Near Vision</td>
                                           </tr>
                                <tr align="center">
                                    <td style="border: 1px solid black;">{{$eye_test->re_distance_new }} Right Eye(RE)</td>
                                    <td style="border: 1px solid black;">{{$eye_test->re_near_new }} Right Eye(RE)</td>
                                    

    
                                </tr>
                                 <tr align="center">
                                    
                                    <td style="border: 1px solid black;">{{$eye_test->le_distance_new }} Left Eye(LE)</td>
                                    <td style="border: 1px solid black;">{{$eye_test->le_near_new }} Left Eye(LE)</td>
                                </tr>
                                
                            </tbody>
                        </table>
                    </td>
                   
                </tr>
                    
                
            </table>
            
            <table width="100%" cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                <tbody>
                    <tr style="background-color: RGB(210, 210, 210);" align="center">
                        <td  style="border: 1px solid black;">Distance Vision</td>
                        <td  style="border: 1px solid black;">Pinhole Vision</td>
                        <td  style="border: 1px solid black;">Near Vision</td>
                    </tr>
                    <tr align="center">
                        <td style="border: 1px solid black;">{{$eye_test->re_distance }} Right Eye(RE)</td>
                        <td style="border: 1px solid black;">{{$eye_test->re_pinhole }} Right Eye(RE)</td>
                        <td style="border: 1px solid black;">{{$eye_test->re_near }} Right Eye(RE)</td>
                    </tr>
                     <tr align="center">
                        
                        <td style="border: 1px solid black;">{{$eye_test->le_distance }} Left Eye(LE)</td>
                        <td style="border: 1px solid black;">{{$eye_test->le_pinhole }} Left Eye(LE)</td>
                        <td style="border: 1px solid black;">{{$eye_test->le_near }} Left Eye(LE)</td>
                    </tr>
                    
                </tbody>
            </table>
            <br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="print-header-table">
                
                <tr>
                    <td width="55%" style="vertical-align:top;">
                            <span>AR Power</span>
                            <table cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                                <tbody>
                                    <tr style="background-color: RGB(210, 210, 210);" align="center">
                                        <td width="9%" style="border: 1px solid black;">Rx</td>
                                        <td width="9.1%" style="border: 1px solid black;">SPH</td>
                                        <td width="9.1%" style="border: 1px solid black;">CYL</td>
                                        <td width="9.1%" style="border: 1px solid black;">AXIS</td>
                                    </tr>
                                    <tr align="center">
                                        <td style="border: 1px solid black; background-color: RGB(210, 210, 210);">RE</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_sph}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_cyl}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->re_axis}}</td>

                                    </tr>
                                     <tr align="center">
                                        <td style="border: 1px solid black; background-color: RGB(210, 210, 210);">LE</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_sph}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_cyl}}</td>
                                        <td style="border: 1px solid black;">{{$eye_test->le_axis}}</td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                    </td>
                    <td width="5%" style="vertical-align:top;"></td>
                    <td width="40%" style="vertical-align:top;">
                        <span>PD Deatils</span>
                        <table width="100%" cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                            <tbody>
                                <tr style="background-color: RGB(210, 210, 210);" align="center">
                                    <td  style="border: 1px solid black;">RE</td>
                                    <td  style="border: 1px solid black;">LE</td>
                                    <td  style="border: 1px solid black;">BE</td>
                               </tr>
                                <tr align="center">
                                    <td style="border: 1px solid black;">{{$eye_test->right_eye }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->left_eys }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->both_eyes }}</td>
                                </tr>
                                 
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            
            <br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="print-header-table">
                
                <tr>
                    <td width="100%" style="vertical-align:top;">
                        <table width="100%" cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                            <tbody>
                                <tr style="background-color: RGB(210, 210, 210);" align="center">
                                    <td  style="border: 1px solid black;">Torch Light</td>
                                    <td  style="border: 1px solid black;">Cover-Uncover</td>
                                    <td  style="border: 1px solid black;">Convergence</td>
                               </tr>
                                <tr align="center">
                                    <td style="border: 1px solid black;">{{$eye_test->torch_light }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->cover_uncover }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->convergence }}</td>
                                </tr>
                                 
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            
            <br>
            <table width="100%" cellspacing="0" cellpadding="0" border="0" class="print-header-table">
                
                <tr>
                    <td width="100%" style="vertical-align:top;">
                        <table width="100%" cellspacing="0" cellpadding="5" border="0" class="print-table" style="font-size: 10pt;">
                            <tbody>
                                <tr style="background-color: RGB(210, 210, 210);" align="center">
                                    <td  style="border: 1px solid black;"></td>
                                    <td  style="border: 1px solid black;">Duochome</td>
                                    <td  style="border: 1px solid black;">JCC</td>
                                    <td  style="border: 1px solid black;">Binocular Balance Test</td>
                               </tr>
                                <tr align="center">
                                    <td style="border: 1px solid black;">Right Eye (RE)</td>
                                    <td style="border: 1px solid black;">{{$eye_test->re_green_red }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->re_refined }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->re_balanced }}</td>
                                </tr>
                                <tr align="center">
                                    <td style="border: 1px solid black;">Left Eye (LE)</td>
                                    <td style="border: 1px solid black;">{{$eye_test->le_green_red }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->le_refined }}</td>
                                    <td style="border: 1px solid black;">{{$eye_test->le_balanced }}</td>
                                </tr>
                                 
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
            
            <div style="line-height: 5px;">&nbsp;</div>
            <div style="margin-top: 10px;"></div>
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