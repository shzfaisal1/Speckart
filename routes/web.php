<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\Product\Frame\FramemasterController;
use App\Http\Controllers\Admin\Product\Goggles\GogglesmasterController;
use App\Http\Controllers\Admin\Product\Glass\GlassmasterController;
use App\Http\Controllers\Admin\Product\Lens\LensmasterController;
use App\Http\Controllers\Admin\Product\Solution\SolutionmasterController;
use App\Http\Controllers\Admin\Product\Other\OthermasterController;
use App\Http\Controllers\Admin\Product\Import\ProductimportController;
use App\Http\Controllers\OtherController;
use App\Http\Controllers\Admin\SettingMasterController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\Product\ProductController;
use App\Http\Controllers\Website\WebsitesController;


//website routes//




// Frontend Client Website Routes
Route::group(['middleware' => ['web']], function ()
{
    Route::get('/speckart-website',[\App\Http\Controllers\Website\WebsiteController::class,'index'])->name('home');
    Route::get('/membership', [\App\Http\Controllers\Website\WebsiteController::class, 'membership'])->name('website.membership');
    // Auth & OTP
    Route::get('/login-web',               [\App\Http\Controllers\Website\WebLoginController::class, 'login_web'])->name('login.web');
     Route::match(['get', 'post'], '/login-web/send-otp', [\App\Http\Controllers\Website\WebLoginController::class, 'send_otp'])->name('login.web.post');
    Route::get('/otp-web',                 [\App\Http\Controllers\Website\WebLoginController::class, 'otp_web'])->name('otp.web');
    Route::post('/otp-web/verify',         [\App\Http\Controllers\Website\WebLoginController::class, 'verify_otp'])->name('otp.web.post');
    Route::get('/register-web',            [\App\Http\Controllers\Website\WebLoginController::class, 'register_web'])->name('register.web');
    Route::post('/register-web',           [\App\Http\Controllers\Website\WebLoginController::class, 'store_register_web'])->name('register.web.post');
    Route::match(['get', 'post'], '/logout-web', [\App\Http\Controllers\Website\WebLoginController::class, 'logout'])->name('logout.web');
    Route::post('/login-web/send-otp-ajax',   [\App\Http\Controllers\Website\WebLoginController::class, 'send_otp_ajax'])->name('login.web.otp.ajax');
    Route::post('/login-web/verify-otp-ajax', [\App\Http\Controllers\Website\WebLoginController::class, 'verify_otp_ajax'])->name('login.web.verify.ajax');
    Route::post('/register-web-ajax',         [\App\Http\Controllers\Website\WebLoginController::class, 'register_ajax'])->name('register.web.ajax');
    // Wishlist
    Route::get('/wishlist',              [\App\Http\Controllers\Website\WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/toggle',      [\App\Http\Controllers\Website\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/wishlist/{id}',        [\App\Http\Controllers\Website\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/wishlist/count',        [\App\Http\Controllers\Website\WishlistController::class, 'count'])->name('wishlist.count');


    // Products Catalog
    Route::get('/products',                [\App\Http\Controllers\Website\ProductController::class, 'products'])->name('products');
    Route::get('/ajax-search',             [\App\Http\Controllers\Website\ProductController::class, 'ajaxSearch'])->name('ajax.search');
    Route::get('/product/{slug}',          [\App\Http\Controllers\Website\ProductController::class, 'details'])->name('product.detail');

    // Home Eye-Test Appointment Booking
    Route::get('/home-eye-test',                           [\App\Http\Controllers\Website\HomeEyeTestController::class, 'index'])->name('home-eye-test');
    Route::post('/home-eye-test/book',                     [\App\Http\Controllers\Website\HomeEyeTestController::class, 'book'])->name('home-eye-test.book');
    Route::get('/home-eye-test/confirmation/{booking_id}', [\App\Http\Controllers\Website\HomeEyeTestController::class, 'confirmation'])->name('home-eye-test.confirmation');
    Route::get('/my-eye-test-appointments',                [\App\Http\Controllers\Website\HomeEyeTestController::class, 'myAppointments'])->name('my-eye-test-appointments');

    // Shopping Cart
    Route::get('/cart',                    [\App\Http\Controllers\Website\CartController::class, 'shopping_cart'])->name('cart');
    Route::post('/cart/add',               [\App\Http\Controllers\Website\CartController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update',            [\App\Http\Controllers\Website\CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/remove',            [\App\Http\Controllers\Website\CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/cart/update-prescription', [\App\Http\Controllers\Website\CartController::class, 'updatePrescription'])->name('cart.update-prescription');
    Route::post('/cart/apply-coupon',      [\App\Http\Controllers\Website\CartController::class, 'applyCoupon'])->name('cart.coupon');
     Route::post('/cart/remove-coupon',     [\App\Http\Controllers\Website\CartController::class, 'removeCoupon'])->name('cart.remove_coupon');
    Route::post('/cart/add-membership',    [\App\Http\Controllers\Website\CartController::class, 'addMembershipToCart'])->name('cart.add_membership');
    Route::post('/cart/remove-membership', [\App\Http\Controllers\Website\CartController::class, 'removeMembershipFromCart'])->name('cart.remove_membership');
     Route::post('/cart/apply-loyalty',     [\App\Http\Controllers\Website\CartController::class, 'applyLoyalty'])->name('cart.apply_loyalty');
    Route::post('/cart/remove-loyalty',    [\App\Http\Controllers\Website\CartController::class, 'removeLoyalty'])->name('cart.remove_loyalty');
    Route::post('/cart/toggle-loyalty',    [\App\Http\Controllers\Website\CartController::class, 'toggleLoyalty'])->name('cart.toggle_loyalty');
      Route::post('/cart/apply-voucher',     [\App\Http\Controllers\Website\CartController::class, 'applyVoucher'])->name('cart.apply_voucher');
    Route::post('/cart/remove-voucher',    [\App\Http\Controllers\Website\CartController::class, 'removeVoucher'])->name('cart.remove_voucher');
    
       // Wishlist Routes
    Route::get('/wishlist',                [\App\Http\Controllers\Website\WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle',        [\App\Http\Controllers\Website\WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::delete('/wishlist/{id}',        [\App\Http\Controllers\Website\WishlistController::class, 'destroy'])->name('wishlist.destroy');
    Route::get('/wishlist/count',          [\App\Http\Controllers\Website\WishlistController::class, 'count'])->name('wishlist.count');

    // Shipping & Checkout Orders
    Route::get('/shipping-details',               [\App\Http\Controllers\Website\OrderController::class, 'add_shipping_details'])->name('shipping-details');
    Route::post('/shipping-details/save',         [\App\Http\Controllers\Website\OrderController::class, 'save_shipping_details'])->name('shipping.save');
    Route::post('/shipping-details/select',       [\App\Http\Controllers\Website\OrderController::class, 'select_saved_address'])->name('shipping.select');
    Route::delete('/shipping-details/address/{id}', [\App\Http\Controllers\Website\OrderController::class, 'delete_address'])->name('shipping.address.delete');
    Route::post('/shipping/check-pincode',        [\App\Http\Controllers\Website\OrderController::class, 'check_pincode'])->name('shipping.check-pincode');
    Route::get('/payment',                 [\App\Http\Controllers\Website\OrderController::class, 'payment_page'])->name('payment');
    Route::post('/checkout/complete',      [\App\Http\Controllers\Website\CheckoutController::class, 'completeCheckout'])->name('checkout.complete');
    Route::get('/my-orders',               [\App\Http\Controllers\Website\OrderController::class, 'my_order'])->name('my-orders');
    Route::post('/my-orders/cancel/{id}',  [\App\Http\Controllers\Website\OrderController::class, 'cancel_order'])->name('my-orders.cancel');
    Route::post('/my-orders/reorder/{id}', [\App\Http\Controllers\Website\OrderController::class, 'reorder'])->name('my-orders.reorder');

    // Eye Prescription
    Route::get('/add-power',               [\App\Http\Controllers\Website\PrescriptionController::class, 'add_power'])->name('add-power');
    Route::get('/saved-prescription',      [\App\Http\Controllers\Website\PrescriptionController::class, 'saved_prescription'])->name('saved-prescription');
    Route::get('/prescription-manually',   [\App\Http\Controllers\Website\PrescriptionController::class, 'prescription_manually'])->name('prescription-manually');
    Route::get('/my-prescriptions',        [\App\Http\Controllers\Website\PrescriptionController::class, 'my_prescription'])->name('my-prescriptions');
    Route::post('/my-prescriptions/upload', [\App\Http\Controllers\Website\PrescriptionController::class, 'upload_prescription'])->name('my-prescriptions.upload');
    Route::post('/my-prescriptions/manual', [\App\Http\Controllers\Website\PrescriptionController::class, 'save_manual_prescription'])->name('my-prescriptions.manual');
    Route::delete('/my-prescriptions/{id}', [\App\Http\Controllers\Website\PrescriptionController::class, 'delete_prescription'])->name('my-prescriptions.delete');

    // Customer Profiles & Addresses
    Route::get('/profile',                 [\App\Http\Controllers\Website\ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/image',          [\App\Http\Controllers\Website\ProfileController::class, 'update_profile_image'])->name('profile.image.update');
    Route::get('/account-info',            [\App\Http\Controllers\Website\ProfileController::class, 'account_information'])->name('account-info');
    Route::post('/account-info',           [\App\Http\Controllers\Website\ProfileController::class, 'update_account_information'])->name('account-info.update');
    Route::post('/account-info-save',      [\App\Http\Controllers\Website\ProfileController::class, 'update_account_information'])->name('account_information.post');
    Route::get('/notifications',           [\App\Http\Controllers\Website\ProfileController::class, 'manage_notification'])->name('notifications');
    Route::get('/my-addresses',            [\App\Http\Controllers\Website\ProfileController::class, 'my_address'])->name('my-addresses');
    Route::get('/my-address',              [\App\Http\Controllers\Website\ProfileController::class, 'my_address'])->name('my_address');
    Route::get('/address/create',          [\App\Http\Controllers\Website\ProfileController::class, 'new_address'])->name('address.create');
    Route::post('/address',                [\App\Http\Controllers\Website\ProfileController::class, 'store_address'])->name('address.store');
    Route::post('/address-store',          [\App\Http\Controllers\Website\ProfileController::class, 'store_address'])->name('store_address');
    Route::get('/address/{id}/edit',        [\App\Http\Controllers\Website\ProfileController::class, 'edit_address'])->name('address.edit');
    Route::post('/address/{id}',           [\App\Http\Controllers\Website\ProfileController::class, 'update_address'])->name('address.update');
    Route::post('/address-update/{id}',    [\App\Http\Controllers\Website\ProfileController::class, 'update_address'])->name('update_address');
    Route::match(['post', 'delete'], '/address/{id}/delete', [\App\Http\Controllers\Website\ProfileController::class, 'delete_address'])->name('address.delete');
    Route::match(['post', 'delete'], '/address-delete/{id}', [\App\Http\Controllers\Website\ProfileController::class, 'delete_address'])->name('delete_address');
    Route::post('/address/{id}/default',   [\App\Http\Controllers\Website\ProfileController::class, 'set_default_address'])->name('address.default');
});

// End website routes//

Route::group(['middleware' => ['auth'], 'prefix' => config('app.admin_path'), 'as' => 'admin.'], function () 
{
    
    Route::post('/signup-otp', [UserController::class, 'signupOtp'])->name('signupOtp');
    Route::post('/checksignup-otp', [UserController::class, 'checksignupOtp'])->name('checksignupOtp');
    
    
    /************ SETTING MASTER ****************/
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::post('/user/update-toggle', [UserController::class, 'updateUserToggle'])->name('user.update.toggle');
    
    /************ STORE ****************/
    Route::get('/store-list', [StoreController::class, 'storeList'])->name('store-list');
    Route::get('/store-add-page', [StoreController::class, 'StoreAddPage'])->name('store-add-page');
    Route::post('/store-add', [StoreController::class, 'StoreAdd'])->name('store-add');
    Route::post('/store-data', [StoreController::class, 'storeData'])->name('store-data');
    Route::post('/store/update-toggle', [StoreController::class, 'updateStoreToggle'])->name('store.update.toggle');
    Route::get('/store-edit-page/{store_id}', [StoreController::class, 'storeEditPage'])->name('store-edit-page');
    Route::post('/store-update', [StoreController::class, 'StoreUpdate'])->name('store-update');
    Route::get('/change-password', [StoreController::class, 'changePassword'])->name('change-password');

    /***************** MULTI-VARIANT PRODUCT BUILDER ******************/
    Route::get('/products/create',            [ProductController::class, 'create'])->name('products.create');
    Route::post('/products',                  [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product_id}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product_id}',      [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product_id}',   [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('/products/{product_id}/destroy', [ProductController::class, 'destroy'])->name('products.destroy.post');
    Route::get('/products/{product_id}/details', [ProductController::class, 'getProductDetails'])->name('products.details');

    /***************** PRODUCT FRAME ******************/
    Route::resource('frameproduct', FramemasterController::class);
    Route::post('frameproduct/search', [FramemasterController::class, 'search'])->name('frameproduct.search');
    Route::post('/frameproduct/{uid}', [FramemasterController::class, 'update'])->name('frameproduct.update');
    Route::post('/frameproduct/{id}/destroy', [FramemasterController::class, 'destroy'])->name('frameproduct.delete');

    /***************** PRODUCT GOGGLES ******************/
    Route::resource('gogglesproduct', GogglesmasterController::class);
    Route::post('gogglesproduct/search', [GogglesmasterController::class, 'search'])->name('gogglesproduct.search');
    Route::post('/gogglesproduct/{uid}', [GogglesmasterController::class, 'update'])->name('gogglesproduct.update');
    Route::post('/gogglesproduct/{id}/destroy', [GogglesmasterController::class, 'destroy'])->name('gogglesproduct.delete');

    
    /***************** PRODUCT GLASS ******************/
    Route::resource('glassproduct', GlassmasterController::class);
    Route::post('glassproduct/search', [GlassmasterController::class, 'search'])->name('glassproduct.search');
    Route::post('/glassproduct/{uid}', [GlassmasterController::class, 'update'])->name('glassproduct.update');
    Route::post('/glassproduct/{id}/destroy', [GlassmasterController::class, 'destroy'])->name('glassproduct.delete');

    /***************** PRODUCT LENS ******************/
    Route::resource('lensproduct', LensmasterController::class);
    Route::post('lensproduct/search', [LensmasterController::class, 'search'])->name('lensproduct.search');
    Route::post('/lensproduct/{uid}', [LensmasterController::class, 'update'])->name('lensproduct.update');
    Route::post('/lensproduct/{id}/destroy', [LensmasterController::class, 'destroy'])->name('lensproduct.delete');

    /***************** PRODUCT SOLUTION ******************/
    Route::resource('solutionproduct', SolutionmasterController::class);
    Route::post('solutionproduct/search', [SolutionmasterController::class, 'search'])->name('solutionproduct.search');
    Route::post('/solutionproduct/{uid}', [SolutionmasterController::class, 'update'])->name('solutionproduct.update');
    Route::post('/solutionproduct/{id}/destroy', [SolutionmasterController::class, 'destroy'])->name('solutionproduct.delete');

      /***************** PRODUCT CATALOG ******************/
    Route::get('/catalog',                    [ProductController::class, 'index'])->name('catalog.index');
    Route::post('/catalog/search',            [ProductController::class, 'searchCatalog'])->name('catalog.search');
    Route::post('/catalog/toggle-status',     [ProductController::class, 'toggleStatus'])->name('catalog.toggle-status');
    /***************** PRODUCT OTHER ******************/
    Route::resource('otherproduct', OthermasterController::class);
    Route::post('otherproduct/search', [OthermasterController::class, 'search'])->name('otherproduct.search');
    Route::post('/otherproduct/{uid}', [OthermasterController::class, 'update'])->name('otherproduct.update');
    Route::post('/otherproduct/{id}/destroy', [OthermasterController::class, 'destroy'])->name('otherproduct.delete');
    
    /***************** PRODUCT IMPORT ******************/
    Route::resource('importproduct', ProductimportController::class);
    Route::post('importproduct/search', [ProductimportController::class, 'search'])->name('importproduct.search');
    Route::post('bulk-product-add', [ProductimportController::class, 'bulkProductAdd'])->name('bulk-product-add');
    
    Route::get(
    '/download-invalid-file/{file}',
    [ProductimportController::class, 'downloadInvalidFile']
)->name('download-invalid-file');
    
    /***************** BRAND MASTER ******************/
    Route::get('/brand-master', [MasterController::class, 'brandMaster'])->name('brand-master');
    Route::post('/brand-list', [MasterController::class, 'brandDatatable'])->name('brand-list');
    Route::post('/brand-stored', [MasterController::class, 'brandstored'])->name('brand-stored');
    Route::post('/brand/{id}/destroy', [MasterController::class, 'branddestroy'])->name('brand.delete');
    
    
    /***************** SIZE MASTER ******************/
    Route::get('/size-master', [MasterController::class, 'sizeMaster'])->name('size-master');
    Route::post('/size-list', [MasterController::class, 'sizeDatatable'])->name('size-list');
    Route::post('/size-stored', [MasterController::class, 'sizestored'])->name('size-stored');
    Route::post('/size/{id}/destroy', [MasterController::class, 'sizedestroy'])->name('size.delete');

    /***************** SHAPE MASTER ******************/
    Route::get('/shape-master', [MasterController::class, 'shapeMaster'])->name('shape-master');
    Route::post('/shape-list', [MasterController::class, 'shapeDatatable'])->name('shape-list');
    Route::post('/shape-stored', [MasterController::class, 'shapestored'])->name('shape-stored');
    Route::post('/shape/{id}/destroy', [MasterController::class, 'shapedestroy'])->name('shape.delete');
    
    /***************** COLOR MASTER ******************/
    Route::get('/color-master', [MasterController::class, 'colorMaster'])->name('color-master');
    Route::post('/color-list', [MasterController::class, 'colorDatatable'])->name('color-list');
    Route::post('/color-stored', [MasterController::class, 'colorstored'])->name('color-stored');
    Route::post('/color/{id}/destroy', [MasterController::class, 'colordestroy'])->name('color.delete');
    
    /***************** MATERIAL MASTER ******************/
    Route::get('/material-master', [MasterController::class, 'materialMaster'])->name('material-master');
    Route::post('/material-list', [MasterController::class, 'materialDatatable'])->name('material-list');
    Route::post('/material-stored', [MasterController::class, 'materialstored'])->name('material-stored');
    Route::post('/material/{id}/destroy', [MasterController::class, 'materialdestroy'])->name('material.delete');
    
    /***************** TYPE MASTER ******************/
    Route::get('/type-master', [MasterController::class, 'typeMaster'])->name('type-master');
    Route::post('/type-list', [MasterController::class, 'typeDatatable'])->name('type-list');
    Route::post('/type-stored', [MasterController::class, 'typestored'])->name('type-stored');
    Route::post('/type/{id}/destroy', [MasterController::class, 'typedestroy'])->name('type.delete');
    
    /***************** VARIANTS MASTER ******************/
    Route::get('/variants-master', [MasterController::class, 'variantMaster'])->name('variant-master');
    Route::post('/variants-list', [MasterController::class, 'variantDatatable'])->name('variant-list');
    Route::post('/variants-stored', [MasterController::class, 'variantstored'])->name('variant-stored');
    Route::post('/variants/{id}/destroy', [MasterController::class, 'variantdestroy'])->name('variant.delete');
    
    
    /***************** COATING MASTER ******************/
    Route::get('/coating-master', [MasterController::class, 'coatingMaster'])->name('coating-master');
    Route::post('/coating-list', [MasterController::class, 'coatingDatatable'])->name('coating-list');
    Route::post('/coating-stored', [MasterController::class, 'coatingstored'])->name('coating-stored');
    Route::post('/coating/{id}/destroy', [MasterController::class, 'coatingdestroy'])->name('coating.delete');
    
    
    /***************** DESIGN MASTER ******************/
    Route::get('/design-master', [MasterController::class, 'designMaster'])->name('design-master');
    Route::post('/design-list', [MasterController::class, 'designDatatable'])->name('design-list');
    Route::post('/design-stored', [MasterController::class, 'designstored'])->name('design-stored');
    Route::post('/design/{id}/destroy', [MasterController::class, 'designdestroy'])->name('design.delete');
    
    /***************** INDEX MASTER ******************/
    Route::get('/index-master', [MasterController::class, 'indexMaster'])->name('index-master');
    Route::post('/index-list', [MasterController::class, 'indexDatatable'])->name('index-list');
    Route::post('/index-stored', [MasterController::class, 'indexstored'])->name('index-stored');
    Route::post('/index/{id}/destroy', [MasterController::class, 'indexdestroy'])->name('index.delete');
    
    /***************** CT MASTER ******************/
    Route::get('/ct-master', [MasterController::class, 'ctMaster'])->name('ct-master');
    Route::post('/ct-list', [MasterController::class, 'ctDatatable'])->name('ct-list');
    Route::post('/ct-stored', [MasterController::class, 'ctstored'])->name('ct-stored');
    Route::post('/ct/{id}/destroy', [MasterController::class, 'ctdestroy'])->name('ct.delete');
    
    
    /***************** VALIDITY MASTER ******************/
    Route::get('/validity-master', [MasterController::class, 'validityMaster'])->name('validity-master');
    Route::post('/validity-list', [MasterController::class, 'validityDatatable'])->name('validity-list');
    Route::post('/validity-stored', [MasterController::class, 'validitystored'])->name('validity-stored');
    Route::post('/validity/{id}/destroy', [MasterController::class, 'validitydestroy'])->name('validity.delete');
    
    
    Route::get('/companyname-dropdown', [MasterController::class, 'companyListdropdown'])->name('companyname-dropdown');
    Route::get('/colorname-dropdown', [MasterController::class, 'colorListdropdown'])->name('colorname-dropdown');
    Route::get('/sizename-dropdown', [MasterController::class, 'sizeListdropdown'])->name('sizename-dropdown');
    Route::get('/typename-dropdown', [MasterController::class, 'typeListdropdown'])->name('typename-dropdown');
    Route::get('/shapename-dropdown', [MasterController::class, 'shapeListdropdown'])->name('shapename-dropdown');
    Route::get('/materialname-dropdown', [MasterController::class, 'materialListdropdown'])->name('materialname-dropdown');
    Route::get('/variantname-dropdown', [MasterController::class, 'variantListdropdown'])->name('variantname-dropdown');
    Route::get('/coatingname-dropdown', [MasterController::class, 'coatingListdropdown'])->name('coatingname-dropdown');
    Route::get('/designname-dropdown', [MasterController::class, 'designListdropdown'])->name('designname-dropdown');
    Route::get('/indexname-dropdown', [MasterController::class, 'indexListdropdown'])->name('indexname-dropdown');
    Route::get('/ctname-dropdown', [MasterController::class, 'ctListdropdown'])->name('ctname-dropdown');
    Route::get('/validityname-dropdown', [MasterController::class, 'validityListdropdown'])->name('validityname-dropdown');
    
    /*********************** REPORTS **************************/
    
    Route::get('/generate-reports', [ReportController::class, 'generateReports'])->name('generate-reports');
    Route::get('/inventory-report', [ReportController::class, 'inventoryReport'])->name('inventory-report');
    Route::post('/get-inventorydata-report', [ReportController::class, 'inventorydataReport'])->name('get-inventorydata-report');
    Route::post('/inventory-excel-download', [ReportController::class, 'inventoryExcelDownload'])->name('inventory-excel-download');
    
    Route::get('/stock-transfer-report', [ReportController::class, 'stockTransferReport'])->name('stock-transfer-report');
    Route::post('/get-transferdata-report', [ReportController::class, 'transferdataReport'])->name('get-transferdata-report');
    Route::post('/transferstock-report-datatable', [ReportController::class, 'transferstockReportDatatable'])->name('transferstock-report-datatable');
    Route::post('/transfer-excel-download', [ReportController::class, 'transferExcelDownload'])->name('transfer-excel-download');
    
    Route::get('/purchase-report', [ReportController::class, 'purchaseReport'])->name('purchase-report');
    Route::post('/get-purchasedata-report', [ReportController::class, 'purchasedataReport'])->name('get-purchasedata-report');
    Route::post('/purchase-report-datatable', [ReportController::class, 'purchaseReportDatatable'])->name('purchase-report-datatable');
    Route::post('/purchase-excel-download', [ReportController::class, 'purchaseExcelDownload'])->name('purchase-excel-download');
    
    Route::get('/purchase-return-report', [ReportController::class, 'purchasereturnReport'])->name('purchase-return-report');
    Route::post('/get-purchasereturndata-report', [ReportController::class, 'purchasereturndataReport'])->name('get-purchasereturndata-report');
    Route::post('/purchase-return-report-datatable', [ReportController::class, 'purchasereturnReportDatatable'])->name('purchase-return-report-datatable');
    Route::post('/purchase-return-excel-download', [ReportController::class, 'purchasereturnExcelDownload'])->name('purchase-return-excel-download');
    
    Route::get('/loss-report', [ReportController::class, 'lossReport'])->name('loss-report');
    Route::post('/get-lossdata-report', [ReportController::class, 'lossdataReport'])->name('get-lossdata-report');
    Route::post('/loss-datatable', [ReportController::class, 'lossDatatable'])->name('loss-datatable');
    Route::post('/loss-excel-download', [ReportController::class, 'lossExcelDownload'])->name('loss-excel-download');
    
    Route::get('/sales-report', [ReportController::class, 'salesReport'])->name('sales-report');
    Route::post('/get-salesdata-report', [ReportController::class, 'saledataReport'])->name('get-salesdata-report');
    Route::post('/sale-excel-download', [ReportController::class, 'SaleExcelDownload'])->name('sale-excel-download');
    
    Route::get('/pending-order-report', [ReportController::class, 'pendingOrderReport'])->name('pending-order-report');
    Route::post('/get-pendingorderdata-report', [ReportController::class, 'pendingorderdataReport'])->name('get-pendingorderdata-report');
    Route::post('/pending-order-excel-download', [ReportController::class, 'pendingorderExcelDownload'])->name('pending-order-excel-download');
    
    Route::get('/sales-return-report', [ReportController::class, 'salesReturnReport'])->name('sales-return-report');
    Route::post('/get-salesreturndata-report', [ReportController::class, 'salereturndataReport'])->name('get-salesreturndata-report');
    Route::post('/sale-return-excel-download', [ReportController::class, 'SaleReturnExcelDownload'])->name('sale-return-excel-download');
    
    Route::get('/GST-input-report', [ReportController::class, 'GSTinputReport'])->name('GST-input-report');
    Route::post('/gstinput-datatable', [ReportController::class, 'gstinputDatatable'])->name('gstinput-datatable');
    Route::post('/gstinput-excel-download', [ReportController::class, 'gstinputExcelDownload'])->name('gstinput-excel-download');
    
    Route::get('/GST-out-report', [ReportController::class, 'GSToutReport'])->name('GST-out-report');
    Route::post('/gstoutput-datatable', [ReportController::class, 'gstoutputDatatable'])->name('gstoutput-datatable');
    Route::post('/gstoutput-excel-download', [ReportController::class, 'gstoutputExcelDownload'])->name('gstoutput-excel-download');
    
    Route::get('/payment-report', [ReportController::class, 'paymentReport'])->name('payment-report');
    
    Route::get('/feedback-dashboard-A', [OtherController::class, 'feedbackDashboardA'])->name('feedback-dashboard-A');
    Route::post('/feedback-dashboard-A-datatable', [OtherController::class, 'dahsboardADatatable'])->name('feedback-dashboard-A-datatable');
    Route::get('/getfeedbackdahbaordAdata', [OtherController::class, 'getfeedbackdahbaordAdata'])->name('getfeedbackdahbaordAdata');
    Route::post('/sale-feedback-updated', [OtherController::class, 'saleFeedbackUpdated'])->name('sale-feedback-updated');
    
    Route::get('/feedback-dashboard-B', [OtherController::class, 'feedbackDashboardB'])->name('feedback-dashboard-B');
    Route::post('/feedback-dashboard-B-datatable', [OtherController::class, 'dahsboardBDatatable'])->name('feedback-dashboard-B-datatable');
    
    
    Route::get('/walkin-dashboard', [OtherController::class, 'walkinDashboard'])->name('walkin-dashboard');
    Route::post('/add-walkin-record', [OtherController::class, 'addWalkinRecord'])->name('add-walkin-record');
    Route::post('/store-performance', [OtherController::class, 'getStorePerformance'])->name('store.performance');
    Route::post('/staff-performance', [OtherController::class, 'getStaffPerformance'])->name('staff.performance');
    Route::post('/head-office-performance', [OtherController::class, 'getHeadOfficePerformance'])->name('head.performance');
    Route::post('/store-metrics', [OtherController::class, 'storePerformanceMetrics'])->name('store.metrics');
    Route::post('/walkout-reasons', [OtherController::class, 'walkoutReasonData'])->name('walkout.reasons');
    Route::post('/staff-performanceoverall', [OtherController::class, 'getStaffPerformanceoverall'])->name('staff.performanceoverall');
    Route::post('/store-comparison', [OtherController::class, 'storeComparison'])->name('store.comparison');
    Route::post('/walkout-all-reasons', [OtherController::class, 'walkoutAllReasons'])->name('walkout.allreasons');
    Route::post('/followup-dashboard', [OtherController::class, 'followupDashboard'])->name('followup.dashboard');
    Route::post('/walkin-datatable', [OtherController::class, 'walkinDatatable'])->name('walkin-datatable');
    Route::post('/walkin/{id}/destroy', [OtherController::class, 'walkinDestroy'])->name('walkin.delete');
    Route::post('/followup/list', [OtherController::class, 'followupList'])->name('followup.list');
    
    Route::post('/update-walkin-record', [OtherController::class, 'updateWalkinRecord'])->name('update-walkin-record');
    Route::post('followup/update',[OtherController::class,'updateFollowup'])->name('followup.update');
    Route::get('/counting-dashboard', [OtherController::class, 'countingDashboard'])->name('counting-dashboard');
    Route::post('/counting-datatable', [OtherController::class, 'countingDatatable'])->name('counting-datatable');
    Route::post('counting-store',[OtherController::class,'countingStore'])->name('counting-store');
    Route::post('/countingrecord/{id}/destroy', [OtherController::class, 'countingrecorddestroy'])->name('countingrecord.delete');

    Route::controller(\App\Http\Controllers\Admin\ProductTypeController::class)
     ->prefix('product-types')
     ->name('product-types.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    Route::controller(\App\Http\Controllers\Admin\CollectionController::class)
     ->prefix('collections')
     ->name('collections.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    // ── LENS SYSTEM ──
    Route::controller(\App\Http\Controllers\Admin\PowerTypeController::class)
     ->prefix('power-types')
     ->name('power-types.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    Route::controller(\App\Http\Controllers\Admin\LensPackageController::class)
     ->prefix('lens-packages')
     ->name('lens-packages.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
         Route::post('/{id}/media',          'uploadMedia')  ->name('media.upload');
         Route::delete('/media/{mediaId}',   'deleteMedia')  ->name('media.delete');
     });

    Route::controller(\App\Http\Controllers\Admin\LensBenefitController::class)
     ->prefix('lens-benefits')
     ->name('lens-benefits.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    Route::controller(\App\Http\Controllers\Admin\LensTagController::class)
     ->prefix('lens-tags')
     ->name('lens-tags.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    Route::controller(\App\Http\Controllers\Admin\CouponController::class)
     ->prefix('coupons')
     ->name('coupons.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });

    // ── CATEGORIES ──
    Route::controller(CategoryController::class)
     ->prefix('categories')
     ->name('categories.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
         Route::get('/dropdown',      'dropdown')     ->name('dropdown');
     });

    // ── SUBCATEGORIES ──
    Route::controller(SubcategoryController::class)
     ->prefix('subcategories')
     ->name('subcategories.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/data',          'data')         ->name('data');
         Route::post('/',             'store')        ->name('store');
         Route::get('/{id}/edit',     'edit')         ->name('edit');
         Route::put('/{id}',          'update')       ->name('update');
         Route::patch('/{id}/toggle', 'toggleStatus') ->name('toggle');
         Route::delete('/{id}',       'destroy')      ->name('destroy');
     });
     
     
        // ── OFFERS & PROMOTIONS ──
    Route::controller(\App\Http\Controllers\Admin\OfferController::class)
     ->prefix('offers')
     ->name('offers.')
     ->group(function () {
         Route::get('/',                'index')          ->name('index');
         Route::get('/data',            'data')           ->name('data');
         Route::get('/create',          'create')         ->name('create');
         Route::post('/',               'store')          ->name('store');
         Route::get('/search-products', 'searchProducts') ->name('search-products');
         Route::get('/brands',          'getBrands')      ->name('brands');
         Route::get('/categories',      'getCategories')  ->name('categories');
         Route::delete('/{id}',         'destroy')        ->name('destroy');
         Route::get('/{id}/edit',       'edit')           ->name('edit');
         Route::put('/{id}',            'update')         ->name('update');
        Route::get('/{id}',            'show')           ->name('show');
          Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
     });
     
      Route::get('/products/subcategories',     [ProductController::class, 'getSubcategories'])->name('products.subcategories');
        Route::post('/products/check-sku',        [ProductController::class, 'checkSku'])->name('products.check-sku');
        // ── BANNERS MODULE ──
    Route::controller(\App\Http\Controllers\Admin\BannerController::class)
     ->prefix('banners')
     ->name('banners.')
     ->group(function () {
         Route::get('/',                'index')          ->name('index');
         Route::get('/data',            'data')           ->name('data');
         Route::post('/',               'store')          ->name('store');
         Route::get('/{id}/edit',       'edit')           ->name('edit');
         Route::post('/{id}',           'update')         ->name('update'); // Use POST with multipart for uploads
         Route::patch('/{id}/toggle',   'toggleStatus')   ->name('toggle');
         Route::delete('/{id}',         'destroy')        ->name('destroy');
         Route::get('/search-products', 'searchProducts') ->name('search-products');
     });

    // ── B2C ONLINE ORDERS MODULE ──
    Route::controller(\App\Http\Controllers\Admin\B2cOrderController::class)
     ->prefix('b2c-orders')
     ->name('b2c-orders.')
     ->group(function () {
         Route::get('/',                          'index')              ->name('index');
         Route::get('/{id}',                      'show')               ->name('show');
         Route::post('/{id}/status',              'updateStatus')       ->name('update-status');
         Route::post('/{id}/verify-prescription', 'verifyPrescription') ->name('verify-prescription');
         Route::post('/{id}/lab-status',          'updateLabStatus')    ->name('update-lab-status');
         Route::post('/{id}/tracking',            'updateTracking')     ->name('update-tracking');
         Route::post('/{id}/note',                'addNote')            ->name('add-note');
         Route::post('/{id}/cancel',              'cancelOrder')        ->name('cancel');
         Route::post('/{id}/return',              'processReturn')      ->name('process-return');
         Route::post('/bulk-action',              'bulkAction')         ->name('bulk-action');
         Route::get('/{id}/invoice',              'invoice')            ->name('invoice');
         Route::get('/{id}/lab-work-order',       'labWorkOrder')       ->name('lab-work-order');
     });

    // ── B2C REGISTERED CUSTOMERS MODULE ──
    Route::controller(\App\Http\Controllers\Admin\B2cCustomerController::class)
     ->prefix('b2c-customers')
     ->name('b2c-customers.')
     ->group(function () {
         Route::get('/',                     'index')        ->name('index');
         Route::get('/{id}',                 'show')         ->name('show');
         Route::post('/{id}/toggle-status',  'toggleStatus') ->name('toggle-status');
     });

    // ── HOME EYE TEST APPOINTMENTS ADMIN MODULE ──
    Route::controller(\App\Http\Controllers\Admin\HomeEyeTestAdminController::class)
     ->prefix('home-eye-test')
     ->name('home-eye-test.')
     ->group(function () {
         Route::get('/',              'index')        ->name('index');
         Route::get('/{id}',          'show')         ->name('show');
         Route::post('/{id}/status',  'updateStatus') ->name('update-status');
         Route::post('/{id}/delete',  'destroy')      ->name('delete');
     });

    // ── SHIPPING CHARGES ADMIN MODULE ──
    Route::controller(\App\Http\Controllers\Admin\ShippingChargeController::class)
     ->prefix('shipping-charges')
     ->name('shipping-charges.')
     ->group(function () {
         Route::get('/',                'index')        ->name('index');
         Route::get('/data',            'data')         ->name('data');
         Route::post('/',               'store')        ->name('store');
         Route::get('/{id}',            'show')         ->name('show');
         Route::post('/{id}',           'update')       ->name('update');
         Route::patch('/{id}/toggle',   'toggleStatus') ->name('toggle');
         Route::patch('/{id}/toggle-cod', 'toggleCod')  ->name('toggle-cod');
         Route::delete('/{id}',         'destroy')      ->name('destroy');
     });

});


 



Route::get('/get-state', [OtherController::class, 'getState'])->name('get-state');
Route::get('/get-city-by-state', [OtherController::class, 'getCityByState'])->name('get-city-by-state');


