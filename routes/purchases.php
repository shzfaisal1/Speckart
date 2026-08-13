<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\PurchaseController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    /************************** ADD PURCHASE ***************************/
    Route::get('/add-purchase', [PurchaseController::class, 'addPurchase'])->name('add-purchase');
    Route::get('/suppliername-dropdown', [PurchaseController::class, 'supplierListdropdown'])->name('suppliername-dropdown');
    Route::get('/get-product-wise-code', [PurchaseController::class, 'getProductWiseCode'])->name('get-product-wise-code');
    Route::get('/get-product-details', [PurchaseController::class, 'getProductDetails'])->name('get-product-details');
    Route::get('/get-gst-details', [PurchaseController::class, 'getGSTDetails'])->name('get-gst-details');
    Route::post('/add-purchase-record', [PurchaseController::class, 'StoreOrder'])->name('add-purchase-record');
    Route::get('/get-old-value', [PurchaseController::class, 'getOldValue'])->name('get-old-value');
    
    /**************************  PURCHASE HISTORY ***************************/
    
    Route::get('/purchase-history', [PurchaseController::class, 'purchaseHistory'])->name('purchase-history');
    Route::post('/purchase-datatable', [PurchaseController::class, 'purchaseDatatable'])->name('purchase-datatable');
    Route::get('purchase/view/{id}', [PurchaseController::class, 'viewPurchase'])->name('purchase.view');
    Route::post('/purchase/{id}/destroy', [PurchaseController::class, 'destroy'])->name('purchase.delete');
    
    Route::get('/purchase-edit-history', [PurchaseController::class, 'purchaseeditHistory'])->name('purchase-edit-history');
    Route::post('/purchase-edit-datatable', [PurchaseController::class, 'purchaseeditDatatable'])->name('purchase-edit-datatable');
    
    Route::get('/getedithistroy', [PurchaseController::class, 'getedithistroy'])->name('getedithistroy');
    /************************** UPDATE PURCHASE ***************************/
    
    Route::get('purchase/edit/{id}', [PurchaseController::class, 'editPurchase'])->name('purchase.edit');
    Route::post('/purchase/update', 
        [PurchaseController::class, 'update']
    )->name('update-purchase-record');
    Route::post('purchase/delete-product', [PurchaseController::class, 'deleteProduct'])->name('purchase.delete-product');
    
    /************************** BARCODE ***************************/
    
    Route::get('/purchase-barcode', [PurchaseController::class, 'purchaseBarcode'])->name('purchase-barcode');
    Route::post('/barcode-pending', [PurchaseController::class, 'barcodePending'])->name('barcode-pending');
    Route::post('/bulk-confirm-barcode', [PurchaseController::class, 'bulkConfirmBarcode'])->name('bulk-confirm-barcode');
    Route::post('/bulk-generate-barcode', [PurchaseController::class, 'bulkBarcodeGenerate'])->name('bulk-generate-barcode');
    Route::post('/barcode/{id}/confirm', [PurchaseController::class, 'singleConfirmBarcode'])->name('barcode.confirm');
    Route::post('/single-barcode-update', [PurchaseController::class, 'singleBarcodeUpdate'])->name('single-barcode-update');
    Route::get('/confirm-barcode', [PurchaseController::class, 'confirmBarcode'])->name('confirm-barcode');
    Route::post('/barcode-confirm-datatable', [PurchaseController::class, 'barcodeConfirmDatatable'])->name('barcode-confirm-datatable');
    Route::get('purchase/barcodegenerate/{id}', [PurchaseController::class, 'PurchaseFullbarcode'])->name('purchase.barcodegenerate');
    Route::post('/barcode-new-update', [PurchaseController::class, 'barcodeNewUpdate'])->name('barcode-new-update');
    
    
    Route::get('/generate-barcode', [PurchaseController::class, 'generateBarcode'])->name('generate-barcode');
    Route::post('/new-barcode-datatable', [PurchaseController::class, 'newBarcodeDatatable'])->name('new-barcode-datatable');
    /************************** DISCOUNT ***************************/
    
    Route::get('/additional-discount', [PurchaseController::class, 'additionalDiscount'])->name('additional-discount');
    Route::post('/additional-discount-datatable', [PurchaseController::class, 'additionalDiscountDatatable'])->name('additional-discount-datatable');
    Route::get('/purchase/filter', [PurchaseController::class, 'purchaseFilter'])->name('purchase.filter');
    Route::post('/additional-discount-add', [PurchaseController::class, 'additionalDiscountAdd'])->name('additional-discount-add');
    Route::post('/additionaldis-delete/{id}', [PurchaseController::class, 'additionaldisdestroy'])->name('additionaldis-delete');
    
    /**************************** CHALLAN ********************************/
    
    Route::get('/add-challan', [PurchaseController::class, 'addChallan'])->name('add-challan');
    Route::post('/add-challan-record', [PurchaseController::class, 'Storechallan'])->name('add-challan-record');
    
    Route::get('/pending-challan', [PurchaseController::class, 'pendingChallan'])->name('pending-challan');
    Route::post('/pending-challan-datatable', [PurchaseController::class, 'pendingchallanDatatable'])->name('pending-challan-datatable');
    Route::post('/challan-destroy', [PurchaseController::class, 'challanDestroy'])->name('challan-destroy');
    
    Route::post('/check-same-store', [PurchaseController::class, 'checkSameStore'])->name('check-same-store');
    Route::post('update-purchase-of-challan', [PurchaseController::class, 'updatePurchaseOfChallan'])->name('update-purchase-of-challan');
    Route::post('add-challan-to-purchase-record', [PurchaseController::class, 'addChallanToPurchaseRecord'])->name('add-challan-to-purchase-record');
    
    
    Route::get('/pending-purchase', [PurchaseController::class, 'pendingPurchase'])->name('pending-purchase');
    Route::post('/pending-purchase-datatable', [PurchaseController::class, 'pendingPurchaseDatatable'])->name('pending-purchase-datatable');
    Route::post('/check-same-store-order', [PurchaseController::class, 'checkSameStoreOrder'])->name('check-same-store-order');
    Route::post('update-purchase-of-order', [PurchaseController::class, 'updatePurchaseOfOrder'])->name('update-purchase-of-order');
    Route::post('add-purchase-to-pending-sale', [PurchaseController::class, 'addPurchaseToPendingSale'])->name('add-purchase-to-pending-sale');
    

    /**************************** PURCHASE RETURN ********************************/
     
    Route::get('/purchase-return', [PurchaseController::class, 'purchaseReturn'])->name('purchase-return');
    Route::post('/purchase-return-datatable', [PurchaseController::class, 'purchaseReturnDatatable'])->name('purchase-return-datatable');
    Route::get('/add-purchase-return', [PurchaseController::class, 'addReturn'])->name('add-purchase-return');
    Route::get('/purchase-product-list', [PurchaseController::class, 'purchaseProductList'])->name('purchase-product-list');
    Route::post('/purchase-returen-stored', [PurchaseController::class, 'purchaseReturenStored'])->name('purchase-returen-stored');
    
    Route::get('/purchase-grid', [PurchaseController::class, 'purchaseGrid'])->name('purchase-grid');
    Route::post('/purchase-grid-add', [PurchaseController::class, 'purchaseGridAdd'])->name('purchase-grid-add');
    
    Route::get('/missing-purchase-price', [PurchaseController::class, 'missingPurchasePrice'])->name('missing-purchase-price');
    Route::post('/missing-price-barcode-datatable', [PurchaseController::class, 'missingPriceBarcodeDatatable'])->name('missing-price-barcode-datatable');
    Route::post('/barcode-price-update', [PurchaseController::class, 'barcodePriceUpdate'])->name('barcode-price-update');

});


