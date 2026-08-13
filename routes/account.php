<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\OtherController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AccountController;

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
     Route::get('/account-dashboard', [AccountController::class, 'accountDashboard'])->name('account-dashboard');
     
    Route::get('/store-expenses', [AccountController::class, 'storeExpenses'])->name('store-expenses');
    Route::post('/expenses-datatable', [AccountController::class, 'expensesDatatable'])->name('expenses-datatable');
    Route::post('/voucher/{id}/destroy', [AccountController::class, 'voucherDestroy'])->name('voucher.delete');
    Route::post('/expense-stored', [AccountController::class, 'expenseStored'])->name('expense-stored');
    Route::get('/voucher/recepit/{id}', [AccountController::class, 'voucherRecepit'])->name('voucher.recepite');
    Route::get('/recepit/pdf/{id}', [AccountController::class, 'recepitpdf'])->name('recepit.pdf');
    
    Route::get('/account-receivable', [AccountController::class, 'accountReceivable'])->name('account-receivable');

});

