<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\InventoryController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    Route::get('/add-inventory', [InventoryController::class, 'addInventory'])->name('add-inventory');
    Route::post('/add-inventory-record', [InventoryController::class, 'addInventoryRecord'])->name('add-inventory-record');
    Route::post('/add-inventory-product-wise', [InventoryController::class, 'addinventoryProductWise'])->name('add-inventory-product-wise');
    Route::post('/purchase-product-add', [InventoryController::class, 'purchaseProductAdd'])->name('purchase-product-add');
    Route::post('/challan-product-add', [InventoryController::class, 'challanProductAdd'])->name('challan-product-add');
    Route::get('/inventory-level', [InventoryController::class, 'inventoryLevel'])->name('inventory-level');
    Route::post('/inventory-datatable', [InventoryController::class, 'inventoryDatatable'])->name('inventory-datatable');
    Route::get('inventory/delete/{id}', [InventoryController::class, 'inventoryDelete'])->name('inventory.delete');
    Route::post('/inventory-barcode-datatable', [InventoryController::class, 'inventorybarcodeDatatable'])->name('inventory-barcode-datatable');
    Route::post('/inventory-destroy', [InventoryController::class, 'inventorydestroy'])->name('inventory-destroy');
    Route::get('/getglasssels', [InventoryController::class, 'getglasssels'])->name('getglasssels');
    Route::get('/getsalesproductwisedetails', [InventoryController::class, 'getsalesproductwisedetails'])->name('getsalesproductwisedetails');
    
    Route::get('/stock-transfer', [InventoryController::class, 'stockTransfer'])->name('stock-transfer');
    Route::post('/transferstock-datatable', [InventoryController::class, 'transferstockDatatable'])->name('transferstock-datatable');
    Route::get('/create-transferstock', [InventoryController::class, 'createTransferstock'])->name('create-transferstock');
    Route::get('/stock-product-list', [InventoryController::class, 'stockProductList'])->name('stock-product-list');
    Route::post('/stock-transfer-update', [InventoryController::class, 'stockTransferUpdate'])->name('stock-transfer-update');
    Route::get('stocktransfer-print/{id}', [InventoryController::class, 'stocktransferPrint'])->name('stocktransfer-print');
     Route::get('stocktransfer-pdf/{id}', [InventoryController::class, 'stocktransferPdf'])->name('stocktransfer-pdf');
    
    Route::get('/barcode-transfer', [InventoryController::class, 'barcodeTransfer'])->name('barcode-transfer');
    Route::post('/check-barcode', [InventoryController::class, 'checkBarcode'])->name('check-barcode');
    Route::post('/get-barcode-details', [InventoryController::class, 'getBarcodeDetails'])->name('get-barcode-details');
    Route::post('/confirm-transfer', [InventoryController::class, 'confirmTransfer'])->name('confirm-transfer');



    Route::get('/stock-received-store', [InventoryController::class, 'stockReceivedStore'])->name('stock-received-store');
    Route::post('/receviedstockstore-datatable', [InventoryController::class, 'receviedstockstoreDatatable'])->name('receviedstockstore-datatable');
    
    Route::get('/inventory-audit', [InventoryController::class, 'inventoryAudit'])->name('inventory-audit');
    Route::post('/audit-datatable', [InventoryController::class, 'auditDatatable'])->name('audit-datatable');
    Route::post('/add-audit-record', [InventoryController::class, 'addAuditRecord'])->name('add-audit-record');
    Route::get('audit-excel/{audit_id}/{status}', [InventoryController::class, 'auditExcel'])->name('audit-excel');
    
    Route::get('/stock-movement', [InventoryController::class, 'stockMovement'])->name('stock-movement');
    Route::post('/stockmovement-datatable', [InventoryController::class, 'stockmovementDatatable'])->name('stockmovement-datatable');
    Route::get('/get-stockdetails', [InventoryController::class, 'getStockdetails'])->name('get-stockdetails');
    
    Route::get('/track-barcode', [InventoryController::class, 'trackBarcode'])->name('track-barcode');
    Route::get('/barcode-activity-list', [InventoryController::class, 'barcodeActivityList'])->name('barcode-activity-list');
    
    Route::get('/inventory-adjustment-history', [InventoryController::class, 'inventoryAdjustmentHistory'])->name('inventory-adjustment-history');
    Route::post('/adjustment-datatable', [InventoryController::class, 'adjustmentDatatable'])->name('adjustment-datatable');

    
});