<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\NpsController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\SettingMasterController;
use App\Http\Controllers\Admin\Setting\TaxMasterController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    Route::get('setting-master', [SettingMasterController::class, 'settingMaster'])->name('setting-master');
    /***************** SUPPLIER ******************/
    Route::resource('supplier', SettingMasterController::class);
    Route::post('supplier/search', [SettingMasterController::class, 'search'])->name('supplier.search');
    Route::post('/supplier/{uid}', [SettingMasterController::class, 'update'])->name('supplier.update');
    Route::post('/supplier/{id}/destroy', [SettingMasterController::class, 'destroy'])->name('supplier.delete');
    Route::get('state-dropdown', [SettingMasterController::class, 'stateNameList'])->name('state-dropdown');
    
    /***************** TAX MASTER ******************/
    Route::resource('tax-master', TaxMasterController::class);
    Route::post('tax-master/search', [TaxMasterController::class, 'search'])->name('tax-master.search');
    Route::post('/tax-master/{uid}', [TaxMasterController::class, 'update'])->name('tax-master.update');
    Route::post('/tax-master/{id}/destroy', [TaxMasterController::class, 'destroy'])->name('tax-master.delete');
    
    /***************** BARCODE ******************/
    Route::get('barcode-setting', [SettingMasterController::class,'barcode'])->name('barcode.setting');
    Route::post('barcode-update', [SettingMasterController::class,'barcodeUpdate'])->name('barcode-update');
    
    /***************** Product and Inventory Settings ******************/
    Route::get('product-setting', [SettingMasterController::class,'productCode'])->name('product.setting');
    Route::post('productsetting-update', [SettingMasterController::class,'productCodeSettingUpdate'])->name('productsetting-update');
    
    /***************** SALES ******************/
    Route::get('sales-setting', [SettingMasterController::class,'sales'])->name('sales.setting');
    Route::post('salessetting-update', [SettingMasterController::class,'salessettingUpdate'])->name('salessetting-update');
    
    /***************** PACKAGE ******************/
    Route::get('package-setting', [SettingMasterController::class,'package'])->name('package.setting');
    Route::post('package/search', [SettingMasterController::class, 'packageSearch'])->name('package.search');
    Route::post('package-store', [SettingMasterController::class, 'packageStore'])->name('package-store');
    Route::post('/package/{id}/destroy', [SettingMasterController::class, 'packagedestroy'])->name('package.delete');
    
    /***************** SMS ******************/
    Route::get('sms-setting', [SettingMasterController::class,'smsSetting'])->name('sms.setting');
    Route::post('sms-update', [SettingMasterController::class, 'smsUpdate'])->name('sms-update');
    
    Route::get('smstemplate-setting', [SettingMasterController::class,'smstemplate'])->name('smstemplate.setting');
    Route::post('sms-template-update', [SettingMasterController::class, 'smsTemplateUpdate'])->name('sms-template-update');
    
    /***************** WHATS APP ******************/
    Route::get('whatsapp-setting', [SettingMasterController::class,'whatsappSetting'])->name('whatsapp.setting');
    Route::post('whatsapp-update', [SettingMasterController::class, 'whatsappUpdate'])->name('whatsapp-update');
    Route::get('/get-whatsapp-details/{id}', [SettingMasterController::class, 'getWhatsappDetails'])->name('get-whatsapp-details');

    
    Route::get('whatsapptemplate-setting', [SettingMasterController::class,'whatsapptemplateSetting'])->name('whatsapptemplate.setting');
    Route::post('whatsapp-template-update', [SettingMasterController::class, 'whatsappTemplateUpdate'])->name('whatsapp-template-update');
    
    Route::get('mystryaudit-setting', [SettingMasterController::class,'mystryauditSetting'])->name('mystryaudit.setting');
    
    Route::get('mystry-audit-entry', [SettingMasterController::class, 'mystryAuditEntry'])->name('mystry-audit-entry');
    Route::post('mystry-audit-add', [SettingMasterController::class, 'mystryAuditAdd'])->name('mystry-audit-add');
    
    Route::get('mystry-audit-history', [SettingMasterController::class, 'mystryAudithistory'])->name('mystry-audit-history');
    Route::post('mystry-audit-datatable', [SettingMasterController::class, 'mystryAuditDatatable'])->name('mystry-audit-datatable');
    Route::post('/mystryaudit/{id}/destroy', [SettingMasterController::class, 'mystryauditdestroy'])->name('mystryaudit.delete');
    Route::get('mystryaudit/edit/{id}', [SettingMasterController::class, 'editmystryaudit'])->name('mystryaudit.edit');
    Route::post('mystry-audit-update', [SettingMasterController::class, 'mystryAuditupdate'])->name('mystry-audit-update');
    Route::get('mystryaudit/view/{id}', [SettingMasterController::class, 'viewmystryaudit'])->name('mystryaudit.view');
    Route::get('audit/pdf/{id}', [SettingMasterController::class, 'auditPdf'])->name('audit.pdf');
    
    Route::post('mystryaudit-update', [SettingMasterController::class, 'settingAuditupdate'])->name('mystryaudit-update');
    Route::get('/audit-dashboard', [SettingMasterController::class, 'auditDashboard'])->name('audit-dashboard');
    
    Route::get('/reports/nps', [NpsController::class, 'index'])->name('nps-dashboard');
    Route::get('pending-messages', [NpsController::class, 'pendingMessages'])->name('pending-messages');    
    
    Route::post('pending-messages/mark-sent', [NpsController::class, 'markPendingAsSent'])->name('mark-pending-sent');   
    
    Route::get('/reports/nps/export', [NpsController::class, 'exportNpsData'])->name('nps-export');
    
    
    Route::get('membership-setting', [SettingMasterController::class,'membershipSetting'])->name('membership-setting');
    Route::post('membershipcard-add', [SettingMasterController::class,'membershipcardAdd'])->name('membershipcard-add');
    Route::delete('/membership-delete/{id}', [SettingMasterController::class, 'membsershipDelete'])
    ->name('membership-delete');
    
});