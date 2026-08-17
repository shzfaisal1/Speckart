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
    @php $tbl_customer =  DB::table("tbl_customer")->where('contact_no',$sale->contact_no)->first();   @endphp
    
    @php
    // Unique products by product details to avoid duplicates
        $uniqueProducts = $saleproduct->unique(function ($item) {
            return $item['product_type'].'|'.
                   $item['product_code'].'|'.
                   $item['barcode_use'].'|'.
                   $item->base_price . '|' .
                   $item->discount_amt . '|' .
                   $item->qty . '|' .
                   $item['product_deatils'];
        })->values();
    

    @endphp
    
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
                 @if($printtype == 'order')
                <table width="100%" cellspacing="0" cellpadding="1" border="0" style="margin-top: 0px;">
                    <tbody>
                        <tr>
                            <td style="font-size: 13px; text-decoration: underline;" width="50%"></td>
                            <td style="font-size: 20px; font-weight: bold; text-decoration: underline;" width="50%">PAYABLE RECEIPT</td>
                        </tr>
                        <tr>
                            <td style="vertical-align: top; width:50%">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 10pt;">
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Customer Name</span></span> : </b><span class="ltr-fix">{{$sale->cust_name}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="vertical-align: top;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 10pt;">
                                    <tbody>
                                        <tr>
                                            <td colspan="2"><b><span>Order Number</span> : {{$sale->order_no}}</b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Order</span></span> : <span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Deliver</span></span>:<span class="ltr-fix"> {{ date("d-m-Y", strtotime($sale->delivery_date))}}</span></b></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="0" cellpadding="1" border="1" style="width:100%; margin-top:5px;">
                    <tbody>
                        <tr style="font-weight: bold; font-size:10pt">
                            <td width="10%" class="print-td-table"><span>SL NO</span></td>
                            <td width="92.5%" class="print-td-table"><span>PRODUCT DETAILS</span></td>
                        </tr>
                        @foreach($saleproduct as $i => $product)
                        <tr style="font-size:10pt;">
                            <td class="print-td-table">{{ $i+1 }}</td>
                            @if($product['product_type'] == 'Glass' || $product['product_type'] == 'Lens')
                                <td class="print-td-table">
                                    {{ implode(' - ', array_filter([
                                        $product['product_type'],
                                        $product['product_code'],
                                        $product['barcode_use'],
                                        $product['product_deatils'],
                                    ])) }}<br>
                                    <table cellspacing="0" cellpadding="1" border="1" width="98%" style="font-size: 10pt; margin:5px 5px;" class="print-table">
                                        <tbody>
                                            <tr>
                                                <td colspan="6" align="center" width="53%" style="font-size: 12px;">✔&nbsp;<span>RIGHT EYE (OD)</span></td>
                                                <td colspan="6" align="center" width="46%" style="font-size: 12px;">✔&nbsp;<span>LEFT EYE (OS)</span></td>
                                            </tr>
                                            <tr>
                                                <td width="7%">&nbsp;</td>
                                                <td align="center" width="9.2%">SPH</td>
                                                <td align="center" width="9.2%">CYL</td>
                                                <td align="center" width="9.2%">AXIS</td>
                                                <td align="center" width="9.2%">PD</td>
                                                <td align="center" width="9.2%">VA</td>
                                                <td align="center" width="9.2%">SPH</td>
                                                <td align="center" width="9.2%">CYL</td>
                                                <td align="center" width="9.2%">AXIS</td>
                                                <td align="center" width="9.2%">PD</td>
                                                <td align="center" width="9.2%">VA</td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size: 12px;">DV</td>
                                                <td align="center">{{ $product['GL_EYE_RS_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RC_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RA_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RP_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RV_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LS_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LC_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LA_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LP_D'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LV_D'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size: 12px;">NV</td>
                                                <td align="center">{{ $product['GL_EYE_RS_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RC_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RA_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RP_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_RV_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LS_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LC_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LA_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LP_N'] ?? '-' }}</td>
                                                <td align="center">{{ $product['GL_EYE_LV_N'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size: 12px;">ADD</td>
                                                <td align="left" colspan="5">{{ $product['GL_EYE_RADD'] ?? '-' }}</td>
                                                <td align="left" colspan="5">{{ $product['GL_EYE_LADD'] ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td align="center" style="font-size: 12px;">IPD</td>
                                                <td align="left" colspan="10">{{ $product['GL_EYE_totalPD'] ?? '-' }}</td>
                                            </tr>
                                    </tbody>
                                </table>
                                </td>
                            @else
                                 <td>
                                    {{ implode(' - ', array_filter([
                                        $product['product_type'],
                                        $product['product_code'],
                                        $product['barcode_use'],
                                        $product['product_deatils'],
                                    ])) }}
                                </td>
                            @endif
                        </tr>
                        
                       
                        @endforeach
                    </tr>
                </tbody>
            </table>
                @endif
             
                @if($printtype == 'invoice')
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 0px;">
                    <tbody>
                        <tr>
                            <td width="53%" style="vertical-align: top; font-size:10pt;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 16px; text-decoration: underline;" colspan="2"><span>CUSTOMER DETAILS</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Customer Name</span></span> : <span class="ltr-fix">{{$sale->cust_name}}</span></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Mobile Number</span></span> : </b><span class="ltr-fix">{{$sale->contact_no}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Membership ID</span></span> : </b><span class="ltr-fix"></span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Loyalty Points Balance</span></span></b> : <span class="ltr-fix">{{$tbl_customer->Loyalty_Points_Bal}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="47%" style="vertical-align: top; font-size:10pt;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 22px; font-weight: bold; text-decoration: underline;">INVOICE</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Invoice Number</span></span> : </b><span class="ltr-fix">{{$sale->order_no}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Invoice Date</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Order</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Deliver</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->delivery_date))}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Sales Person</span></span> : </b><span class="ltr-fix">{{$salePerson->name}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table> 
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-bottom:3px;margin-top:3px;">
                    <tbody>
                        <tr style="font-size: 10pt; font-weight: bold;">
                            <td align="left"><span class="rtl-fix"><span>Customer Has Taken External Warranty</span></span> : <span class="ltr-fix">@if($sale->extrnal_warranty == '0') None @else Yes @endif</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table cellspacing="0" cellpadding="1" class="print-table" style="font-size:9pt;width:100%;">
                    <tbody>
                        <tr style="font-weight: bold; font-size:9.5pt">
                            <td width="8%"><span>SL NO</span></td>
                            <td width="52%"><span>PRODUCT DETAILS</span></td>
                            @if($sale->tax_rule != 'Not Applicable')
                            <td width="12%"><span>HSN CODE</span></td>
                            <td width="8%">GST %</td>
                             @endif
                            <td width="8%"><span>QTY</span> </td>
                            <td width="14%" align="right"><span>PRICE</span></td>
                        </tr>
                        @foreach($saleproduct as $i => $product)
                        <tr style="font-size:9pt;">
                            <td>{{ $i+1 }}</td>
                            <td>
                                {{ implode(' - ', array_filter([
                                    $product['product_type'],
                                    $product['product_code'],
                                    $product['barcode_use'],
                                    $product['product_deatils'],
                                ])) }}
                            </td>
                            @if($sale->tax_rule != 'Not Applicable')
                            <td>{{$product['hsn_code']}}</td>
                            <td>{{$product['gst']}}</td>
                             @endif
                            <td>{{$product['qty']}}</td>
                            <td align="right">Rs {{$product['base_price']*$product['qty']}}<br>-{{$product['discount_amt']*$product['qty']}}</td>
                        </tr>    
                        @endforeach
             
                    </tbody>
                </table>
                <table width="100%" cellspacing="0" cellpadding="2" border="0" style="margin-top:1%;">
                    <tbody>
                        <tr>
                            <td style="border: 1px solid black; background-color: #ededed; width:35%; vertical-align: top;" class="print-td-table">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr style="font-size: 15px; font-weight: bold;">
                                            <td width="55%"><span>Advance Paid</span></td>
                                            <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 12px;">
                                                <td colspan="2">{{$sale->pay_method}} = Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 15px; font-weight: bold;">
                                            <td width="55%"><span>Total Paid</span></td>
                                            <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="border: 1px solid black; background-color: #ededed; width:33%; vertical-align: top;" class="print-td-table">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        @if($sale->loyalty_point_apply > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Redeemed {{$sale->loyalty_point}} Loyalty Points</span></span> : <span class="ltr-fix">Rs {{$sale->loyalty_point_amount}}</span></td>
                                        </tr>
                                        @endif
                                        @if($sale->cart_discount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Cart Discount</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount}}</span></td>
                                        </tr>
                                        @endif
                                        @if($sale->coupon_amount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Coupon Discount</span></span> : <span class="ltr-fix">Rs {{$sale->coupon_amount}}</span></td>
                                        </tr>
                                        @endif
                                         @if($sale->total_discount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Product Discount</span></span> : <span class="ltr-fix">Rs {{$sale->total_discount}}</span></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="2" align="center" style="font-size: 25x; font-weight: bold;"><br><span class="rtl-fix"><span>YOU SAVE</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="vertical-align: top;">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Total</span></span> : Rs {{$sale->total_basic_amount}}</td>
                                        </tr>
                                        @if($sale->tax_rule != 'Not Applicable')
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>GST Amount</span></span> : Rs {{$sale->total_gst_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Gross Total</span></span> : Rs {{$sale->total_item_price}}</td>
                                        </tr>
                                        @endif
                                         @if($sale->total_discount > 0)
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Total Discount</span></span> : Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</td>
                                        </tr>
                                         @endif
                                        <tr style="font-size: 20px; font-weight: bold;">
                                            <td colspan="2" align="right"><br><span>Net Total Amount</span></td>
                                        </tr>
                                        <tr style="font-size: 20px; font-weight: bold;">
                                            <td colspan="2" align="right">Rs {{$sale->total_payable}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellspacing="0" cellpadding="3" border="0" style="margin-top:5px; margin-bottom:5px;">
                    <tbody>
                        <tr align="center" style="font-weight: bold; font-size: 12px;">
                              @if($sale->earnedPoints > 0)
                            <td width="49%" style="border: 1px solid gray; background-color: #82fefa;" class="print-td-table">
                                YOU HAVE EARNED<br>{{$sale->earnedPoints}} NEW LOYALTY POINTS FOR THIS PURCHASE<br><font color="blue">Your total loyalty point balance is {{$tbl_customer->Loyalty_Points_Bal}}</font>
                            </td>
                             @endif
                              
                            <td width="2%"></td>
                           @if(!empty($sale->earncoupon))
                            @php
                                $tbl_coupon = DB::table('tbl_coupon')
                                    ->where('coupon_id', $sale->earncoupon)
                                    ->first();
                            @endphp
                        
                            @if($tbl_coupon)
                                <td width="50%" style="border: 1px solid gray; background-color: #fefe85;" class="print-td-table">
                                    USE COUPON CODE <strong>{{ $tbl_coupon->coupon_code }}</strong> TO GET
                        
                                    @if($sale->coupon_type == '0')
                                        {{ $tbl_coupon->coupon_value }} %
                                    @elseif($sale->coupon_type == '1')
                                        Rs {{ $tbl_coupon->coupon_value }}
                                    @endif
                        
                                    DISCOUNT ON NEXT PURCHASE <br>
                                    (*MINIMUM NEXT PURCHASE VALUE {{ $tbl_coupon->min_sale_value }}) <br>
                        
                                    <font color="red">
                                        *Coupon valid till {{ date('d M Y', strtotime($tbl_coupon->valid_to)) }}
                                    </font>
                                </td>
                            @endif
                        @endif
                        </tr>
                    </tbody>
                </table>
                <table width="100%" border="0" style="font-size:13pt; font-weight:bold;margin-top:3px;"><tbody><tr><td align="right" width="auto" style="font-weight: bold;">&nbsp;<div style="height:40px;"></div><span>AUTHORISED SIGNATURE</span></td></tr></tbody></table>
                <div style="margin-top: 5px;">
                        <img src="https://sec.opticalcrm.com/data/customerlogo/924793-6-footer-image.jpg" alt="Footer Image" style="width: 100%;">
                    </div>
                @endif 
            
            
                @if($printtype == 'receipt')
                
                <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 0px;">
                    <tbody>
                        <tr style="font-size:10pt;">
                            <td width="55%" style="vertical-align:top;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 16px; text-decoration: underline; " colspan="2"><span>CUSTOMER DETAILS</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Customer Name</span></span> : </b><span class="ltr-fix">{{$sale->cust_name}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Mobile Number</span></span> : </b><span class="ltr-fix">{{$sale->contact_no}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Membership ID</span></span> : </b><span class="ltr-fix">{{$sale->membership_id}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Loyalty Points Balance</span></span> : </b><span class="ltr-fix">{{$tbl_customer->Loyalty_Points_Bal}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td width="45%" style="vertical-align:top;">
                                <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                    <tbody>
                                        <tr>
                                            <td style="font-size: 18px; font-weight: bold; text-decoration: underline; ">ADVANCE RECEIPT</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Order Number</span></span> : </b><span class="ltr-fix">{{$sale->order_no}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Order</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><b><span class="rtl-fix"><span>Date of Deliver</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->delivery_date))}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table width="100%" cellspacing="0" cellpadding="1" class="print-table">
                    <tbody>
                        <tr style="font-weight: bold; font-size:9pt;">
                            <td width="8%"><span>SL NO</span></td>
                            <td width="78%"><span>PRODUCT DETAILS</span></td>
                            <td width="14%" align="right"><span>PRICE</span></td>
                        </tr>
                        @foreach($saleproduct as $i => $product)
                        <tr style="font-size:9pt;">
                             <td>{{ $i+1 }}</td>
                            <td>
                                {{ implode(' - ', array_filter([
                                    $product['product_type'],
                                    $product['product_code'],
                                    $product['barcode_use'],
                                    $product['product_deatils'],
                                ])) }}
                            </td>
                            <td align="right">Rs {{$product['base_price']*$product['qty']}}<br>-{{$product['discount_amt']*$product['qty']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <table width="100%" cellspacing="0" cellpadding="2" border="0" style="margin-top:5px;">
                    <tbody>
                        <tr>
                            <td style="background-color: #ededed; width:33%; vertical-align: top;" class="print-td-table">
                                <table width="100%" cellspacing="0" cellpadding="0">
                                    <tbody>
                                        <tr style="font-size: 15px; font-weight: bold;">
                                            <td width="55%"><span>Advance Paid</span></td>
                                            <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 12px;">
                                                <td colspan="2">{{$sale->pay_method}} = Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 15px; font-weight: bold;">
                                            <td width="55%"><span>Total Paid</span></td>
                                            <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="background-color: #ededed; width:33%; vertical-align: top; " class="print-td-table">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        @if($sale->loyalty_point_apply > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Redeemed {{$sale->loyalty_point}} Loyalty Points</span></span> : <span class="ltr-fix">Rs {{$sale->loyalty_point_amount}}</span></td>
                                        </tr>
                                        @endif
                                        @if($sale->cart_discount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Cart Discount</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount}}</span></td>
                                        </tr>
                                        @endif
                                        @if($sale->coupon_amount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Coupon Discount</span></span> : <span class="ltr-fix">Rs {{$sale->coupon_amount}}</span></td>
                                        </tr>
                                        @endif
                                         @if($sale->total_discount > 0)
                                        <tr style="font-size: 12px;">
                                            <td align="right"><span class="rtl-fix"><span>Product Discount</span></span> : <span class="ltr-fix">Rs {{$sale->total_discount}}</span></td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="2" align="center" style="font-size: 25x; font-weight: bold;"><br><span class="rtl-fix"><span>YOU SAVE</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                            <td style="vertical-align: top;">
                                <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                    <tbody>
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Total</span></span> : Rs {{$sale->total_basic_amount}}</td>
                                        </tr>
                                        @if($sale->tax_rule != 'Not Applicable')
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>GST Amount</span></span> : Rs {{$sale->total_gst_amount}}</td>
                                        </tr>
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Gross Total</span></span> : Rs {{$sale->total_item_price}}</td>
                                        </tr>
                                        @endif
                                         @if($sale->total_discount > 0)
                                        <tr style="font-size: 17px; font-weight: bold;">
                                            <td align="right"><span><span>Total Discount</span></span> : Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</td>
                                        </tr>
                                         @endif
                                        <tr style="font-size: 20px; font-weight: bold;">
                                            <td colspan="2" align="right"><br><span>Net Total Amount</span></td>
                                        </tr>
                                        <tr style="font-size: 20px; font-weight: bold;">
                                            <td colspan="2" align="right">Rs {{$sale->total_payable}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @foreach($saleproduct as $i => $product)
                 @if($product['product_type'] == 'Glass' || $product['product_type'] == 'Lens')
                <div style="font-weight: bold; text-decoration: underline; font-size: 14pt; margin-top: 2px; text-align:center;">
                    <span>PRESCRIPTION</span>
                </div>
                <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 11pt; margin-top:5px;">
                    <tbody>
                        <tr>
                            <td><b><span class="rtl-fix"><span>Patient Name</span></span> : </b><span class="ltr-fix">{{ $product['patient_name'] ?? '-' }}</span></td>
                            <td><b><span class="rtl-fix"><span>Doctor / Optometrist Name</span></span> : </b><span class="ltr-fix">{{ $product['doc_name'] ?? '-' }}</span></td>
                        </tr>
                    </tbody>
                </table>
                <div style="display: flex; font-size: 12pt; font-weight: bold; margin-bottom: 4px;">
                    <div style="width: 50%; text-align: center;"><span class="rtl-fix"><span>RIGHT EYE (OD)</span></span></div>
                    <div style="width: 50%; text-align: center;"><span class="rtl-fix"><span>LEFT EYE (OS)</span></span></div>
                </div>
                <table cellspacing="0" cellpadding="2" border="0" class="print-table" style="font-size: 10pt; width:100%">
                    <tbody>
                        <tr style="font-size: 10pt; margin-top: 2px;" class="print-table"></tr>
                        <tr style="background-color: RGB(210, 210, 210);" align="center">
                            <td width="7%">&nbsp;</td>
                            <td align="center" width="9.2%">SPH</td>
                            <td align="center" width="9.2%">CYL</td>
                            <td align="center" width="9.2%">AXIS</td>
                            <td align="center" width="9.2%">PD</td>
                            <td align="center" width="9.2%">VA</td>
                            <td align="center" width="9.2%">SPH</td>
                            <td align="center" width="9.2%">CYL</td>
                            <td align="center" width="9.2%">AXIS</td>
                            <td align="center" width="9.2%">PD</td>
                            <td align="center" width="9.2%">VA</td>
                        </tr>
                        <tr>
                        <td align="center" style="font-size: 12px;">DV</td>
                        <td align="center">{{ $product['GL_EYE_RS_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RC_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RA_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RP_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RV_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LS_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LC_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LA_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LP_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LV_D'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">NV</td>
                        <td align="center">{{ $product['GL_EYE_RS_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RC_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RA_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RP_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RV_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LS_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LC_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LA_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LP_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LV_N'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">ADD</td>
                        <td align="left" colspan="5">{{ $product['GL_EYE_RADD'] ?? '-' }}</td>
                        <td align="left" colspan="5">{{ $product['GL_EYE_LADD'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">IPD</td>
                        <td align="left" colspan="10">{{ $product['GL_EYE_totalPD'] ?? '-' }}</td>
                    </tr>
                    </tbody>
                </table>
                @php
                    $wearingTypes = array_map('trim', explode(',', $product['wearing_type']));
                @endphp
                @foreach ($wearingTypes as $type)
                    <div style="margin-top:7px;">
                        <span style="border:1px solid #000; display:inline-block; width:12pt; height:12pt; text-align:center; font-size:12pt; line-height:12pt;">
                            @if(in_array($type, $wearingTypes))
                                ✓
                            @endif
                        </span>
                        <span style="font-size:12pt;">&nbsp;{{ $type }}&nbsp;</span>
                    </div>
                @endforeach
                 @endif
                 @endforeach
                <div style="margin-top: 5px;">
                    <img src="https://sec.opticalcrm.com/data/customerlogo/924793-6-footer-image.jpg" alt="Footer Image" style="width: 100%;">
                </div>
                <div class="page-break"></div>
                <div id="printSection">
                    <div class="format-a4">
                        <div id="print-order-form">
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
                            
                            <div class="watermark-order-formAR">SPECKARTS.COM</div>
                            <table width="100%" cellspacing="0" cellpadding="1" border="0" style="margin-top: 0px;">
                                <tbody>
                                    <tr>
                                        <td style="font-size: 13px; text-decoration: underline;" width="50%"></td>
                                        <td style="font-size: 20px; font-weight: bold; text-decoration: underline;" width="50%">PAYABLE RECEIPT</td>
                                    </tr>
                                    <tr>
                                        <td style="vertical-align: top; width:50%">
                                            <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 10pt;">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"><b><span class="rtl-fix"><span>Customer Name</span></span> : </b><span class="ltr-fix">{{$sale->cust_name}}</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <td style="vertical-align: top;">
                                            <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 10pt;">
                                                <tbody>
                                                    <tr>
                                                        <td colspan="2"><b><span>Order Number</span> : {{$sale->order_no}}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><b><span class="rtl-fix"><span>Date of Order</span></span> : <span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></b></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><b><span class="rtl-fix"><span>Date of Deliver</span></span>:<span class="ltr-fix"> {{ date("d-m-Y", strtotime($sale->delivery_date))}}</span></b></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <table cellspacing="0" cellpadding="1" border="1" style="width:100%; margin-top:5px;">
                                <tbody>
                                    <tr style="font-weight: bold; font-size:10pt">
                                        <td width="10%" class="print-td-table"><span>SL NO</span></td>
                                        <td width="92.5%" class="print-td-table"><span>PRODUCT DETAILS</span></td>
                                    </tr>
                                    @foreach($saleproduct as $i => $product)
                                    <tr style="font-size:10pt;">
                                        <td class="print-td-table">{{ $i+1 }}</td>
                                        @if($product['product_type'] == 'Glass' || $product['product_type'] == 'Lens')
                                            <td class="print-td-table">
                                                {{ implode(' - ', array_filter([
                                                    $product['product_type'],
                                                    $product['product_code'],
                                                    $product['barcode_use'],
                                                    $product['product_deatils'],
                                                ])) }}<br>
                                                <table cellspacing="0" cellpadding="1" border="1" width="98%" style="font-size: 10pt; margin:5px 5px;" class="print-table">
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="6" align="center" width="53%" style="font-size: 12px;">✔&nbsp;<span>RIGHT EYE (OD)</span></td>
                                                            <td colspan="6" align="center" width="46%" style="font-size: 12px;">✔&nbsp;<span>LEFT EYE (OS)</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td width="7%">&nbsp;</td>
                                                            <td align="center" width="9.2%">SPH</td>
                                                            <td align="center" width="9.2%">CYL</td>
                                                            <td align="center" width="9.2%">AXIS</td>
                                                            <td align="center" width="9.2%">PD</td>
                                                            <td align="center" width="9.2%">VA</td>
                                                            <td align="center" width="9.2%">SPH</td>
                                                            <td align="center" width="9.2%">CYL</td>
                                                            <td align="center" width="9.2%">AXIS</td>
                                                            <td align="center" width="9.2%">PD</td>
                                                            <td align="center" width="9.2%">VA</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="font-size: 12px;">DV</td>
                                                            <td align="center">{{ $product['GL_EYE_RS_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RC_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RA_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RP_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RV_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LS_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LC_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LA_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LP_D'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LV_D'] ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="font-size: 12px;">NV</td>
                                                            <td align="center">{{ $product['GL_EYE_RS_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RC_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RA_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RP_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_RV_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LS_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LC_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LA_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LP_N'] ?? '-' }}</td>
                                                            <td align="center">{{ $product['GL_EYE_LV_N'] ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="font-size: 12px;">ADD</td>
                                                            <td align="left" colspan="5">{{ $product['GL_EYE_RADD'] ?? '-' }}</td>
                                                            <td align="left" colspan="5">{{ $product['GL_EYE_LADD'] ?? '-' }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center" style="font-size: 12px;">IPD</td>
                                                            <td align="left" colspan="10">{{ $product['GL_EYE_totalPD'] ?? '-' }}</td>
                                                        </tr>
                                                </tbody>
                                            </table>
                                            </td>
                                        @else
                                             <td>
                                                {{ implode(' - ', array_filter([
                                                    $product['product_type'],
                                                    $product['product_code'],
                                                    $product['barcode_use'],
                                                    $product['product_deatils'],
                                                ])) }}
                                            </td>
                                        @endif
                                    </tr>
                                    
                                   
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="page-break"></div>
                </div>
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
                        <table width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 0px;">
                            <tbody>
                                <tr style="font-size:10pt;">
                                    <td width="53%" style="vertical-align: top; font-size:10pt;">
                                    <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 16px; text-decoration: underline;" colspan="2"><span>CUSTOMER DETAILS</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Customer Name</span></span> : <span class="ltr-fix">{{$sale->cust_name}}</span></b></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Mobile Number</span></span> : </b><span class="ltr-fix">{{$sale->contact_no}}</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Membership ID</span></span> : </b><span class="ltr-fix"></span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Loyalty Points Balance</span></span></b> : <span class="ltr-fix">{{$tbl_customer->Loyalty_Points_Bal}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td width="47%" style="vertical-align: top; font-size:10pt;">
                                    <table width="100%" cellspacing="0" cellpadding="1" border="0">
                                        <tbody>
                                            <tr>
                                                <td style="font-size: 22px; font-weight: bold; text-decoration: underline;">ADVANCE RECEIPT</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Invoice Number</span></span> : </b><span class="ltr-fix">{{$sale->order_no}}</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Invoice Date</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Date of Order</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->sale_date))}}</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Date of Deliver</span></span> : </b><span class="ltr-fix">{{ date("d-m-Y", strtotime($sale->delivery_date))}}</span></td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><b><span class="rtl-fix"><span>Sales Person</span></span> : </b><span class="ltr-fix">{{$salePerson->name}}</span></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="watermark"><span>CUSTOMER COPY</span></div>
                        <table cellspacing="0" cellpadding="1" class="print-table" style="font-size:9pt;width:100%;">
                            <tbody>
                                <tr style="font-weight: bold; font-size:9.5pt">
                                    <td width="8%"><span>SL NO</span></td>
                                    <td width="52%"><span>PRODUCT DETAILS</span></td>
                                    @if($sale->tax_rule != 'Not Applicable')
                                    <td width="12%"><span>HSN CODE</span></td>
                                    <td width="8%">GST %</td>
                                     @endif
                                    <td width="8%"><span>QTY</span> </td>
                                    <td width="14%" align="right"><span>PRICE</span></td>
                                </tr>
                                @foreach($saleproduct as $i => $product)
                                <tr style="font-size:9pt;">
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        {{ implode(' - ', array_filter([
                                            $product['product_type'],
                                            $product['product_code'],
                                            $product['barcode_use'],
                                            $product['product_deatils'],
                                        ])) }}
                                    </td>
                                    @if($sale->tax_rule != 'Not Applicable')
                                    <td>{{$product['hsn_code']}}</td>
                                    <td>{{$product['gst']}}</td>
                                     @endif
                                    <td>{{$product['qty']}}</td>
                                    <td align="right">Rs {{$product['base_price']*$product['qty']}}<br>-{{$product['discount_amt']*$product['qty']}}</td>
                                </tr>    
                                @endforeach
                     
                            </tbody>
                        </table>
                        <table width="100%" cellspacing="0" cellpadding="2" border="0" style="margin-top:1%;">
                            <tbody>
                                <tr>
                                    <td style="border: 1px solid black; background-color: #ededed; width:35%; vertical-align: top;" class="print-td-table">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                <tr style="font-size: 15px; font-weight: bold;">
                                                    <td width="55%"><span>Advance Paid</span></td>
                                                    <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                                </tr>
                                                <tr style="font-size: 12px;">
                                                        <td colspan="2">{{$sale->pay_method}} = Rs {{$sale->pay_amount}}</td>
                                                </tr>
                                                <tr style="font-size: 15px; font-weight: bold;">
                                                    <td width="55%"><span>Total Paid</span></td>
                                                    <td width="45%">: Rs {{$sale->pay_amount}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="border: 1px solid black; background-color: #ededed; width:33%; vertical-align: top;" class="print-td-table">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                @if($sale->loyalty_point_apply > 0)
                                                <tr style="font-size: 12px;">
                                                    <td align="right"><span class="rtl-fix"><span>Redeemed {{$sale->loyalty_point}} Loyalty Points</span></span> : <span class="ltr-fix">Rs {{$sale->loyalty_point_amount}}</span></td>
                                                </tr>
                                                @endif
                                                @if($sale->cart_discount > 0)
                                                <tr style="font-size: 12px;">
                                                    <td align="right"><span class="rtl-fix"><span>Cart Discount</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount}}</span></td>
                                                </tr>
                                                @endif
                                                @if($sale->coupon_amount > 0)
                                                <tr style="font-size: 12px;">
                                                    <td align="right"><span class="rtl-fix"><span>Coupon Discount</span></span> : <span class="ltr-fix">Rs {{$sale->coupon_amount}}</span></td>
                                                </tr>
                                                @endif
                                                 @if($sale->total_discount > 0)
                                                <tr style="font-size: 12px;">
                                                    <td align="right"><span class="rtl-fix"><span>Product Discount</span></span> : <span class="ltr-fix">Rs {{$sale->total_discount}}</span></td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td colspan="2" align="center" style="font-size: 25x; font-weight: bold;"><br><span class="rtl-fix"><span>YOU SAVE</span></span> : <span class="ltr-fix">Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                    <td style="vertical-align: top;">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                            <tbody>
                                                <tr style="font-size: 17px; font-weight: bold;">
                                                    <td align="right"><span><span>Total</span></span> : Rs {{$sale->total_basic_amount}}</td>
                                                </tr>
                                                @if($sale->tax_rule != 'Not Applicable')
                                                <tr style="font-size: 17px; font-weight: bold;">
                                                    <td align="right"><span><span>GST Amount</span></span> : Rs {{$sale->total_gst_amount}}</td>
                                                </tr>
                                                <tr style="font-size: 17px; font-weight: bold;">
                                                    <td align="right"><span><span>Gross Total</span></span> : Rs {{$sale->total_item_price}}</td>
                                                </tr>
                                                @endif
                                                 @if($sale->total_discount > 0)
                                                <tr style="font-size: 17px; font-weight: bold;">
                                                    <td align="right"><span><span>Total Discount</span></span> : Rs {{$sale->cart_discount + $sale->loyalty_point_amount + $sale->cart_discount + $sale->total_discount}}</td>
                                                </tr>
                                                 @endif
                                                <tr style="font-size: 20px; font-weight: bold;">
                                                    <td colspan="2" align="right"><br><span>Net Total Amount</span></td>
                                                </tr>
                                                <tr style="font-size: 20px; font-weight: bold;">
                                                    <td colspan="2" align="right">Rs {{$sale->total_payable}}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        @foreach($saleproduct as $i => $product)
                 @if($product['product_type'] == 'Glass' || $product['product_type'] == 'Lens')
                <div style="font-weight: bold; text-decoration: underline; font-size: 14pt; margin-top: 2px; text-align:center;">
                    <span>PRESCRIPTION</span>
                </div>
                <table width="100%" cellspacing="0" cellpadding="1" border="0" style="font-size: 11pt; margin-top:5px;">
                    <tbody>
                        <tr>
                            <td><b><span class="rtl-fix"><span>Patient Name</span></span> : </b><span class="ltr-fix">{{ $product['patient_name'] ?? '-' }}</span></td>
                            <td><b><span class="rtl-fix"><span>Doctor / Optometrist Name</span></span> : </b><span class="ltr-fix">{{ $product['doc_name'] ?? '-' }}</span></td>
                        </tr>
                    </tbody>
                </table>
                <div style="display: flex; font-size: 12pt; font-weight: bold; margin-bottom: 4px;">
                    <div style="width: 50%; text-align: center;"><span class="rtl-fix"><span>RIGHT EYE (OD)</span></span></div>
                    <div style="width: 50%; text-align: center;"><span class="rtl-fix"><span>LEFT EYE (OS)</span></span></div>
                </div>
                <table cellspacing="0" cellpadding="2" border="0" class="print-table" style="font-size: 10pt; width:100%">
                    <tbody>
                        <tr style="font-size: 10pt; margin-top: 2px;" class="print-table"></tr>
                        <tr style="background-color: RGB(210, 210, 210);" align="center">
                            <td width="7%">&nbsp;</td>
                            <td align="center" width="9.2%">SPH</td>
                            <td align="center" width="9.2%">CYL</td>
                            <td align="center" width="9.2%">AXIS</td>
                            <td align="center" width="9.2%">PD</td>
                            <td align="center" width="9.2%">VA</td>
                            <td align="center" width="9.2%">SPH</td>
                            <td align="center" width="9.2%">CYL</td>
                            <td align="center" width="9.2%">AXIS</td>
                            <td align="center" width="9.2%">PD</td>
                            <td align="center" width="9.2%">VA</td>
                        </tr>
                        <tr>
                        <td align="center" style="font-size: 12px;">DV</td>
                        <td align="center">{{ $product['GL_EYE_RS_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RC_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RA_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RP_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RV_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LS_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LC_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LA_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LP_D'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LV_D'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">NV</td>
                        <td align="center">{{ $product['GL_EYE_RS_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RC_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RA_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RP_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_RV_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LS_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LC_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LA_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LP_N'] ?? '-' }}</td>
                        <td align="center">{{ $product['GL_EYE_LV_N'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">ADD</td>
                        <td align="left" colspan="5">{{ $product['GL_EYE_RADD'] ?? '-' }}</td>
                        <td align="left" colspan="5">{{ $product['GL_EYE_LADD'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 12px;">IPD</td>
                        <td align="left" colspan="10">{{ $product['GL_EYE_totalPD'] ?? '-' }}</td>
                    </tr>
                    </tbody>
                </table>
                @php
                    $wearingTypes = array_map('trim', explode(',', $product['wearing_type']));
                @endphp
                @foreach ($wearingTypes as $type)
                    <div style="margin-top:7px;">
                        <span style="border:1px solid #000; display:inline-block; width:12pt; height:12pt; text-align:center; font-size:12pt; line-height:12pt;">
                            @if(in_array($type, $wearingTypes))
                                ✓
                            @endif
                        </span>
                        <span style="font-size:12pt;">&nbsp;{{ $type }}&nbsp;</span>
                    </div>
                @endforeach
                 @endif
                 @endforeach
                        <div style="margin-top: 5px;"></div>
                        <div style="margin-top: 5px;">
                            <img src="https://sec.opticalcrm.com/data/customerlogo/924793-6-footer-image.jpg" alt="Footer Image" style="width: 100%;">
                        </div>
                    </div>
                </div>
                    
                @endif
            
            
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
