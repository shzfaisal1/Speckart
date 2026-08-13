<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\CustomerController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    Route::get('/customer-add', [CustomerController::class, 'customerAdd'])->name('customer-add');
    Route::post('/customer-stored', [CustomerController::class, 'customerStored'])->name('customer-stored');
    
    Route::get('/customer-list', [CustomerController::class, 'customerList'])->name('customer-list');
    Route::post('/customer-datatable', [CustomerController::class, 'customerDatatable'])->name('customer-datatable');
    Route::post('/customer/{id}/destroy', [CustomerController::class, 'customerdestroy'])->name('customer.delete');
    Route::get('customer/edit/{id}', [CustomerController::class, 'editCustomer'])->name('customer.edit');
    Route::post('/customer-update', [CustomerController::class, 'customerUpdate'])->name('customer-update');
    
    Route::get('/customer-birthday-list', [CustomerController::class, 'customerBirthdayList'])->name('customer-birthday-list');
    Route::post('/customer-birthday-datatable', [CustomerController::class, 'customerBirthdayDatatable'])->name('customer-birthday-datatable');
    
    Route::get('/customer-anniversary-list', [CustomerController::class, 'customerAnniversaryList'])->name('customer-anniversary-list');
    Route::post('/customer-anniversary-datatable', [CustomerController::class, 'customerAnniversaryDatatable'])->name('customer-anniversary-datatable');
    
    Route::get('/loyalty-program', [CustomerController::class, 'loyaltyProgram'])->name('loyalty-program');
    Route::post('/loyaltyprogram-datatable', [CustomerController::class, 'loyaltyprogramDatatable'])->name('loyaltyprogram-datatable');
    Route::get('loyaltyrogram/view/{id}', [CustomerController::class, 'viewloyaltyrogram'])->name('loyaltyrogram.view');
    Route::post('/statementloyalty-datatable', [CustomerController::class, 'statementloyaltyDatatable'])->name('statementloyalty-datatable');
    Route::post('loyaltyaddremove', [CustomerController::class, 'loyaltyAddremove'])->name('loyaltyaddremove');
    
    Route::get('/generate-token', [CustomerController::class, 'generateToken'])->name('generate-token');
    Route::get('/get-customer', [CustomerController::class, 'getCustomer'])->name('get-customer');
    Route::post('/token-datatable', [CustomerController::class, 'tokenDatatable'])->name('token-datatable');
    Route::post('/eyetest/{id}/destroy', [CustomerController::class, 'eyetestdestroy'])->name('eyetest.delete');
    Route::post('/eyetesttoken-stored', [CustomerController::class, 'eyetesttokenStored'])->name('eyetesttoken-stored');
    Route::get('token/print/{id}', [CustomerController::class, 'printToken'])->name('token.print');
    Route::post('/artest-stored', [CustomerController::class, 'artestStored'])->name('artest-stored');
    
    Route::get('/pretest-queue', [CustomerController::class, 'pretestQueue'])->name('pretest-queue');
    Route::post('/prequeue-datatable', [CustomerController::class, 'prequeueDatatable'])->name('prequeue-datatable');
    Route::post('/eyetesthold/{id}/destroy', [CustomerController::class, 'eyetestholddestroy'])->name('eyetesthold.delete');
    Route::post('/eyetestskip/{id}/destroy', [CustomerController::class, 'eyetestskipdestroy'])->name('eyetestskip.delete');
    Route::get('eyetest/start/{id}', [CustomerController::class, 'eyetestStart'])->name('eyetest.start');
    Route::post('/eyetest/step1/update', [CustomerController::class, 'updatetestStep1'])->name('eyetest.step1.update');
    Route::post('/eyetest/step2/update', [CustomerController::class, 'updatetestStep2'])->name('eyetest.step2.update');
    Route::post('/eyetest/step3/update', [CustomerController::class, 'updatetestStep3'])->name('eyetest.step3.update');
    Route::post('/eyetest/step4/update', [CustomerController::class, 'updatetestStep4'])->name('eyetest.step4.update');
    Route::post('/eyetest/step5/update', [CustomerController::class, 'updatetestStep5'])->name('eyetest.step5.update');
    Route::post('/eyetest/step6/update', [CustomerController::class, 'updatetestStep6'])->name('eyetest.step6.update');
    Route::post('/eyetest/step7/update', [CustomerController::class, 'updatetestStep7'])->name('eyetest.step7.update');
    Route::post('/eyetest/step8/update', [CustomerController::class, 'updatetestStep8'])->name('eyetest.step8.update');
    Route::post('/test-send-otp', [CustomerController::class, 'testSendOtp'])->name('test-send-otp');
    Route::post('/testotp-verify', [CustomerController::class, 'testotpVerify'])->name('testotp-verify');
    
    Route::get('/eye-test-record', [CustomerController::class, 'eyeTestRecord'])->name('eye-test-record');
    Route::post('/eyetest-record-datatable', [CustomerController::class, 'eyetestRecordDatatable'])->name('eyetest-record-datatable');
    Route::get('eyetest/prescription/{id}/{idd}', [CustomerController::class, 'eyetestPrescription'])->name('eyetest/prescription');
    Route::get('prescription/pdf/{id}/{idd}', [CustomerController::class, 'prescriptionPdf'])->name('prescription.pdf');
    Route::post('/prescription/{id}/destroy', [CustomerController::class, 'prescriptiondestroy'])->name('prescription.delete');
    
    
    Route::post('/setLoyaltypointOtp', [CustomerController::class, 'setLoyaltypointOtp'])->name('setLoyaltypointOtp');
    Route::post('/checksetloyaltypointvalueOtp', [CustomerController::class, 'checksetloyaltypointvalueOtp'])->name('checksetloyaltypointvalueOtp');
    
    Route::post('/setautoLoyaltypointOtp', [CustomerController::class, 'setautoLoyaltypointOtp'])->name('setautoLoyaltypointOtp');
    Route::post('/setautoloyaltyprogram', [CustomerController::class, 'setautoloyaltyprogram'])->name('setautoloyaltyprogram');

    Route::get('/discount-coupons', [CustomerController::class, 'discountCoupons'])->name('discount-coupons');
    Route::post('/discount-coupon-datatable', [CustomerController::class, 'discountCouponDatatable'])->name('discount-coupon-datatable');
    Route::post('/manually-coupon-stored', [CustomerController::class, 'manuallyCouponStored'])->name('manually-coupon-stored');
    Route::post('/bulk-delete-coupon', [CustomerController::class, 'bulkDeleteCoupon'])->name('bulk-delete-coupon');
    Route::get('/delete-coupon-row', [CustomerController::class, 'deleteCouponRow'])->name('delete-coupon-row');
    Route::post('/auto-coupon-stored', [CustomerController::class, 'autoCouponStored'])->name('auto-coupon-stored');
});