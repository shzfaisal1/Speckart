<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\BBInvoiceController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    Route::get('/sale-dashboard', [SalesController::class, 'saleDashboard'])->name('sale-dashboard');
    /************************** DISCOUNT**************************/
    
    Route::get('/barcode-wise-discount', [SalesController::class, 'barcodeWiseDiscount'])->name('barcode-wise-discount');
    Route::post('/apply-discount-barcode', [SalesController::class, 'applyDiscountBarcode'])->name('apply-discount-barcode');
    Route::post('/discountbarcode-datatable', [SalesController::class, 'discountbarcodeDatatable'])->name('discountbarcode-datatable');
    Route::post('/bulk-barcode-discount', [SalesController::class, 'bulkBarcodeDiscount'])->name('bulk-barcode-discount');
    
    Route::get('/productid-wise-discount', [SalesController::class, 'productidWiseDiscount'])->name('productid-wise-discount');
    Route::post('/discountproduct-datatable', [SalesController::class, 'discountproductDatatable'])->name('discountproduct-datatable');
    Route::post('/bulk-product-discount', [SalesController::class, 'bulkProductDiscount'])->name('bulk-product-discount');
    Route::post('update-discount', [SalesController::class, 'updateDiscount'])
    ->name('update-discount');
    
    Route::get('/brand-wise-discount', [SalesController::class, 'brandWiseDiscount'])->name('brand-wise-discount');
    Route::post('/discountbrand-datatable', [SalesController::class, 'discountbrandDatatable'])->name('discountbrand-datatable');
    Route::post('/bulk-brand-discount', [SalesController::class, 'bulkBrandDiscount'])->name('bulk-brand-discount');
    
    /************************** ADD NEW ORDER ***************************/
    Route::get('/create-new-order', [SalesController::class, 'createNewOrder'])->name('create-new-order');
    Route::get('/get-store-details', [SalesController::class, 'getStoreDetails'])->name('get-store-details');
    Route::get('/getcustomer', [SalesController::class, 'getcustomerDetails'])->name('getcustomer');
    Route::get('/get-barcode-table', [SalesController::class, 'getBarcodeTable'])->name('get.barcode.table');
    Route::post('/add-sale-record', [SalesController::class, 'storedSaleOrder'])->name('add-sale-record');
    Route::get('/sale-history', [SalesController::class, 'saleHistory'])->name('sale-history');
    Route::post('/check-loyalty-point', [SalesController::class, 'checkLoyaltyPoint'])->name('check-loyalty-point');
    Route::post('/redeemOtp', [SalesController::class, 'redeemOtp'])->name('redeemOtp');
    Route::post('/checkredeemOtp', [SalesController::class, 'checkredeemOtp'])->name('checkredeemOtp');
    Route::post('/checkcoupon', [SalesController::class, 'checkcoupon'])->name('checkcoupon');
    Route::get('/get-store-product-by-product-code', [SalesController::class, 'getProductByProductCode'])->name('get-store-product-by-product-code');
    Route::post('/get-lens-packages', [SalesController::class, 'getPackages'])->name('get.lens.packages');
    Route::post('/get-lens-packages-coating', [SalesController::class, 'getPackagesCoating'])->name('get.lens.packages.coating');
    Route::get('/getprescription', [SalesController::class, 'getprescription'])->name('getprescription');
    Route::get('/glassnumber-dropdown', [SalesController::class, 'glassnumberDropdown'])->name('glassnumber-dropdown');
    Route::post('/checksetloyaltypointvalue', [SalesController::class, 'checksetloyaltypointvalue'])->name('checksetloyaltypointvalue');
    Route::get('/getlensbarcode', [SalesController::class, 'getlensbarcode'])->name('getlensbarcode');
    
    Route::post('/cartOtp', [SalesController::class, 'cartOtp'])->name('cartOtp');
    Route::post('/checkcartOtp', [SalesController::class, 'checkcartOtp'])->name('checkcartOtp');
    
    Route::post('/saleOtp', [SalesController::class, 'saleOtp'])->name('saleOtp');
    Route::post('/checksaleOtp', [SalesController::class, 'checksaleOtp'])->name('checksaleOtp');
    
    Route::get('/inter-store-sale', [SalesController::class, 'interStoreSale'])->name('inter-store-sale');
    Route::get('/get-store-product-by-barcode', [SalesController::class, 'getStoreProductByBarcode'])->name('get-store-product-by-barcode');
    Route::post('/add-inter-sale-record', [SalesController::class, 'storedinterSaleOrder'])->name('add-inter-sale-record');
    Route::post('/sales-datatable', [SalesController::class, 'salesDatatable'])->name('sales-datatable');
    Route::get('sale/invoice/{id}/{idd}', [SalesController::class, 'saleInvoice'])->name('sale.invoice');
    Route::get('sale/pdf/{id}/{idd}', [SalesController::class, 'salePdf'])->name('sale.pdf');
    Route::get('/checkremindersms', [SalesController::class, 'checkremindersms'])->name('checkremindersms');
    Route::get('/getallwhatsapptamplete', [SalesController::class, 'getallwhatsapptamplete'])->name('getallwhatsapptamplete');
    Route::get('/sendmessageonwhtasapp', [SalesController::class, 'sendmessageonwhtasapp'])->name('sendmessageonwhtasapp');
    Route::get('sale/confirm/{id}', [SalesController::class, 'saleConfirmpage'])->name('sale.confirm');
    Route::post('/orderconfirm', [SalesController::class, 'orderconfirm'])->name('orderconfirm');
    Route::get('sale/edit/{id}', [SalesController::class, 'saleInvoiceEdit'])->name('sale.edit');
    Route::get('/loadorderdetails', [SalesController::class, 'loadorderdetails'])->name('loadorderdetails');
    Route::post('/saleseditpaymentdelete/{id}/destroy', [SalesController::class, 'saleseditpaymentdestroy'])->name('saleseditpaymentdelete.delete');
    Route::post('/saleseditpaymentreturndelete/{id}/destroy', [SalesController::class, 'saleseditpaymentreturndelete'])->name('saleseditpaymentreturndelete.delete');
    Route::post('sales-customer-update',[SalesController::class, 'salesCustomerUpdate'])->name('sales-customer-update');
    Route::post('sales-order-update',[SalesController::class, 'salesorderUpdate'])->name('sales-order-update');
    Route::post('sales-remove-product',[SalesController::class, 'salesRemoveProduct'])->name('sales-remove-product');
    Route::post('roundoff-value-update',[SalesController::class, 'roundoffValueUpdate'])->name('roundoff-value-update');
    Route::post('add-new-payment-order',[SalesController::class, 'addNewPaymentOrder'])->name('add-new-payment-order');
    Route::post('add-new-item-order',[SalesController::class, 'addNewItemOrder'])->name('add-new-item-order');
    Route::get('/getsalespayment', [SalesController::class, 'getsalespayment'])->name('getsalespayment');
    Route::get('/getreturnpayment', [SalesController::class, 'getreturnpayment'])->name('getreturnpayment');
    Route::get('/getsalesproduct', [SalesController::class, 'getsalesproduct'])->name('getsalesproduct');
    Route::post('updateSalesPurchasePrice',[SalesController::class, 'updatePurchasePrice'])->name('updateSalesPurchasePrice');
    Route::get('applyredeempoint',[SalesController::class, 'applyredeempoint'])->name('applyredeempoint');
    Route::post('/updateredeempoint', [SalesController::class, 'updateredeempoint'])->name('updateredeempoint');
    Route::get('checkcouponapplyornot',[SalesController::class, 'checkcouponapplyornot'])->name('checkcouponapplyornot');
    Route::post('/couponupdateonorder', [SalesController::class, 'couponupdateonorder'])->name('couponupdateonorder');
    Route::post('checkcartapplyornot',[SalesController::class, 'checkcartapplyornot'])->name('checkcartapplyornot');
    Route::post('updatecartdiscount',[SalesController::class, 'updatecartdiscount'])->name('updatecartdiscount');
    Route::post('/deleteOtp', [SalesController::class, 'deleteOtp'])->name('deleteOtp');
    Route::post('/orderdelete', [SalesController::class, 'orderdelete'])->name('orderdelete');
    Route::get('getorderprescription', [SalesController::class, 'getorderprescription'])->name('getorderprescription');
    Route::post('prescriptionupdate', [SalesController::class, 'prescriptionupdate'])->name('prescriptionupdate');
    
    Route::get('/handover-history', [SalesController::class, 'handoverHistory'])->name('handover-history');
    Route::post('/sale-handover-datatable', [SalesController::class, 'saleHandoverDatatable'])->name('sale-handover-datatable');
    Route::get('/sale-handover-product-list', [SalesController::class, 'saleHandoverProductList'])->name('sale-handover-product-list');
    Route::post('/sale-handover-stored', [SalesController::class, 'saleHandoverStored'])->name('sale-handover-stored');

    Route::get('/sale-return', [SalesController::class, 'saleReturn'])->name('sale-return');
    Route::get('/sale-product-list', [SalesController::class, 'saleProductList'])->name('sale-product-list');
    
    Route::post('/get-returnrequestdata', [SalesController::class, 'getReturnrequestdata'])->name('get-returnrequestdata');
    Route::post('/sale-returen-approval-request', [SalesController::class, 'saleReturenRequest'])->name('sale-returen-approval-request');
    Route::get('/sale-return-request-history', [SalesController::class, 'saleReturnRequesthistory'])->name('sale-return-request-history');
    Route::post('/sale-return-request-datatable', [SalesController::class, 'saleReturnRequestDatatable'])->name('sale-return-request-datatable');
    
    Route::post('/sale-returen-stored', [SalesController::class, 'saleReturenStored'])->name('sale-returen-stored');
    Route::post('/sale-returen-payment-stored', [SalesController::class, 'saleReturenPaymentStored'])->name('sale-returen-payment-stored');
    
    Route::get('/sale-return-history', [SalesController::class, 'saleReturnhistory'])->name('sale-return-history');
    Route::post('/sale-return-datatable', [SalesController::class, 'saleReturnDatatable'])->name('sale-return-datatable');
    Route::post('/gatepass/{id}', [SalesController::class, 'creategatepass'])
    ->name('gatepass.create');
    
    Route::get('gatepass-history', [SalesController::class, 'gatepassHistory'])->name('gatepass-history');
    Route::post('/gatepass-datatable', [SalesController::class, 'gatepassDatatable'])->name('gatepass-datatable');
    Route::post('/get-gatepassdata', [SalesController::class, 'getgatepassdata'])->name('get-gatepassdata');
    Route::post('/bulk-confirm-gatepass', [SalesController::class, 'bulkConfirmGatepass'])->name('bulk-confirm-gatepass');
    

    Route::get('sale-pending-history', [SalesController::class, 'salePendingHistory'])->name('sale-pending-history');
    Route::post('/sales-pending-datatable', [SalesController::class, 'salesPendingDatatable'])->name('sales-pending-datatable');
    
    Route::get('/order-item-tracking', [SalesController::class, 'orderItemTracking'])->name('order-item-tracking');
    Route::post('/item-tracking-list', [SalesController::class, 'itemTrackingList'])->name('item-tracking-list');
    Route::get('/gettrackinghistory', [SalesController::class, 'gettrackinghistory'])->name('gettrackinghistory');
    Route::post('/tracking-status-update', [SalesController::class, 'trackingStatusUpdate'])->name('tracking-status-update');
    
    Route::get('/bulk-invoice', [SalesController::class, 'createbulkinvoice'])->name('bulk-invoice');
    Route::post('/bulk-sale-record', [SalesController::class, 'bulkSaleRecord'])->name('bulk-sale-record');
    Route::post('/check-inventory', [SalesController::class, 'checkinventory'])->name('check.inventory');
    
    Route::get('/daily-statement', [SalesController::class, 'dailyStatement'])->name('daily-statement');
    Route::get('/sale-statement-record', [SalesController::class, 'saleStatementRecord'])->name('sale-statement-record');
    
    Route::get('/pending-courier', [SalesController::class, 'pendingCourier'])->name('pending-courier');
    Route::post('/pending-courier-datatable', [SalesController::class, 'pendingCourierDatatable'])->name('pending-courier-datatable');
    Route::get('/getpendingcourierproduct', [SalesController::class, 'getpendingcourierproduct'])->name('getpendingcourierproduct');
    Route::post('/update-courier', [SalesController::class, 'updateCourier'])->name('update.courier');
    
    Route::get('/courier-history', [SalesController::class, 'courierHistory'])->name('courier-history');
    Route::post('/history-courier-datatable', [SalesController::class, 'historyCourierDatatable'])->name('history-courier-datatable');
    
    Route::post('/update-index', [SalesController::class, 'updateIndex'])->name('update-index');
    
    
     /************************** B2B Invoice ***************************/
    Route::get('/bb-sales-history', [BBInvoiceController::class, 'bbSalesHistory'])->name('bb-sales-history'); 
    Route::get('/create-bb-invoice', [BBInvoiceController::class, 'createBBInvoice'])->name('create-bb-invoice');
    Route::get('/getbbcustomer', [BBInvoiceController::class, 'getbbcustomer'])->name('getbbcustomer');
});

Route::get('/get-store-gst/{id}', function ($id) {
        $store = DB::table('tbl_store')->where('id', $id)->first();
        return response()->json(['gst_no' => $store->gst_no ?? '']);
    });