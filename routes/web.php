<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AlertReminderController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\MobileUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\SubareaController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChallanController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CollectionReportController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\DenominationController;
use App\Http\Controllers\FinalProductController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PurposeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ScheduleVisitController;
use App\Http\Controllers\RepeatescheduleController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\AreaVisitController;
use App\Http\Controllers\AreaVisitExportController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\AttendanceSummaryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChallanReportController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DealerVisitController;
use App\Http\Controllers\DealerVisitExportController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\ExcelInventoryController;
use App\Http\Controllers\ExcelProductManufactureController;
use App\Http\Controllers\ExcelRawProductController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\NegotiationController;
use App\Http\Controllers\NoticeImageController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrivacyPolicyController;
use App\Http\Controllers\ProductDealerController;
use App\Http\Controllers\ProductInventoryController;
use App\Http\Controllers\ProductInventorymanufController;
use App\Http\Controllers\ProductManufactureController;
use App\Http\Controllers\RawInventoryController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\RawProductController;
use App\Http\Controllers\RawPurchaseController;
use App\Http\Controllers\SubdealerInventoryController;
use App\Http\Controllers\UnlistedExportController;
use App\Http\Controllers\VisitExportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\SiteRegistrationControllerV2;
use App\Http\Controllers\FollowUpControllerV2;


use App\Http\Controllers\TadaReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Working for cache.
Route::get('optimize', function () {
    Artisan::call('migrate');
    Artisan::call('storage:link');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    return response()->json('Migrations run, Storage linked, Cache cleared and Config cached successfully.');
});

Route::get('/', [DashboardController::class, 'index']);

Auth::routes();

//Forgetting Password Or Resetting Password
Route::get('aforget-password', [ForgotPasswordController::class, 'showForgetPasswordForm'])->name('forget.password.get');
Route::post('aforget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::get('areset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('reset.password.get');
Route::post('areset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

Route::group(['middleware' => ['auth', 'user.status']], function () {
    Route::get('dashboard', [LocationController::class, 'index'])->middleware('permission:home');
    Route::get('activeuser', [LocationController::class, 'indexReplace']);
    Route::get('userposition/{id}/{date}', [LocationController::class, 'individual'])->name('userposition');
    Route::get('userposition/{id}', [LocationController::class, 'individual'])->name('testm');

    Route::get('artisan', [AccountController::class, 'artisan']);

    Route::get('/admin/{id}/pass', [UserController::class, 'change']);
    Route::PATCH('/adminpass/{id}', [UserController::class, 'changepass']);
    Route::resource('admin', UserController::class);

    Route::get('/user/reset/{id}', [MobileUserController::class, 'reset']);
    Route::get('/userPassword/{id}/edit', [MobileUserController::class, 'passwordView']);
    Route::patch('/user/changePassword/{id}', [MobileUserController::class, 'changePassword']);

    Route::group(['middleware' => ['permission:notice']], function () {
        Route::post('/notice/search', [NoticeController::class, 'search']);
        Route::patch('/noticePush/{id}', [NoticeController::class, 'noticePush']);
        Route::resource('notice', NoticeController::class);
        Route::resource('noticefile', NoticeImageController::class)->only('destroy');
    });

    Route::middleware(['permission:order'])->group(function () {
        Route::resource('order', OrderController::class);
        Route::post('addstoreOrder', [OrderController::class, 'addOrderstore']);

        Route::get('productlist', [CartController::class, 'getProduct']);
        Route::get('sub-productlist', [CartController::class, 'getSubProduct']);
        Route::get('productsize', [CartController::class, 'getproductsize']);

        Route::get('addorder/{id}', [CartController::class, 'addOrder'])->name('addorder');
        Route::get('orderRequest', [CartController::class, 'orderRequest']);
        Route::post('add-cart-to-order', [CartController::class, 'addOrderstore'])->name('store-order');
        Route::post('store-to-cart', [CartController::class, 'storeToCart'])->name('store-to-cart');
        Route::delete('storetocart/{id}', [CartController::class, 'destroy'])->name('storetocart.destory');
        Route::patch('store-tocart/{id}', [CartController::class, 'update'])->name('storetocart.update');

        Route::post('orderCollectionReport', [OrderReportController::class, 'ordercollection']);
        Route::post('orderTrackingReport', [OrderReportController::class, 'orderreport']);
        Route::get('/allorder/{id}', [OrderController::class, 'allOrder']);

        Route::get('/order/edit/{id}', [OrderController::class, 'editOrderView']);
        Route::patch('/order/edit/{id}', [OrderController::class, 'chalan']);
        Route::get('/orders/{status}', [OrderController::class, 'status']);
        Route::get('/order/{id}', [OrderController::class, 'general']);
        Route::get('/orderpop/{id}', [OrderController::class, 'pop']);
        Route::get('orderdetails/{id}', [OrderController::class, 'orderdetails']);

        Route::get('/order/challan', [ChallanController::class, 'listChallan']);
        Route::get('/order/challan/{id}', [ChallanController::class, 'challan']);
        Route::get('/order/singleChallan/{id}', [ChallanController::class, 'singleChallan']);
        Route::get('/challan/edit/{id}', [ChallanController::class, 'challanEditView']);
        Route::PATCH('/challan/edit/{id}', [ChallanController::class, 'challanAdd']);
        Route::PATCH('/order/availability/{id}', [ChallanController::class, 'availability']);
        Route::POST('/order/singleChallan/{id}', [ChallanController::class, 'singleChallanAdd']);
        Route::POST('/order/challan/{id}', [ChallanController::class, 'allChallan']);

        Route::resource('/orderReport', OrderReportController::class);

        Route::get('/challan/report/{id}', [ChallanReportController::class, 'challanreport']);

        Route::post('/orederrequest', [CartController::class, 'addOrderRequest']);

        Route::get('viewcomplaint/{id}', [OrderController::class, 'viewcomplaint']);
    });

    Route::group(['middleware' => ['permission:collection']], function () {
        Route::get('/collection', [PaymentController::class, 'list']);
        Route::post('/collection', [PaymentController::class, 'filter']);
        Route::get('/cheque-collection', [PaymentController::class, 'cheque_list']);
        Route::post('/cheque-collection', [PaymentController::class, 'cheque_filter']);
        Route::get('/direct-collection', [PaymentController::class, 'direct_list']);
        Route::post('/direct-collection', [PaymentController::class, 'direct_filter']);
        Route::patch('/cheque-collection/{id}', [PaymentController::class, 'update']);
        Route::get('/collectionpop/{id}', [PaymentController::class, 'pop']);

        Route::get('/token/details/{id}', [TokenController::class, 'details']);
        Route::resource('token', TokenController::class);

        Route::post('zeroReport', [CollectionReportController::class, 'zeroReport']);
        Route::resource('collection-report', CollectionController::class)->only(['index', 'store']);
        Route::get('/collection-report/filter', [CollectionController::class, 'filter']);

        Route::resource('/collectionReport', CollectionReportController::class);

        Route::post('//collectionSummaryReport', [CollectionReportController::class, 'collectionSummaryReport']);
        Route::patch('/edit-collection/{id}', [PaymentController::class, 'edit']);

        Route::get('/cashPopup/{id}', [PaymentController::class, 'cashPop']);
        Route::get('receiptimage/{id}', [CollectionController::class, 'show'])->name('collection.receiptimage');
    });

    Route::group(['middleware' => ['permission:schedule']], function () {
        // For Schedules
        Route::get('/schedule/dealer/{id}', [SchedulevisitController::class, 'dealerValue']);
        Route::post('/schedule/search', [ScheduleVisitController::class, 'search']);
        Route::get('schedule/area/{id}', [ScheduleVisitController::class, 'getarea']);
        Route::resource('schedule', ScheduleVisitController::class);

        // For Repeated Schedule
        Route::get('repeated/search', [RepeatescheduleController::class, 'search']);
        Route::resource('repeated', RepeatescheduleController::class);
    });

    Route::group(['middleware' => ['permission:chat']], function () {
        // For Chat
        Route::resource('chat', MessageController::class);
        Route::get('/chatuser/search', [MessageController::class, 'search']);
    });

    Route::group(['middleware' => ['permission:complaint']], function () {
        Route::resource('complaint', ComplaintController::class);
    });

    Route::group(['middleware' => ['permission:reports']], function () {
        // Visit Report Route
        Route::get('history', [VisitController::class, 'filter']);
        Route::resource('visithistory', VisitController::class);
        Route::get('/visit/areas', [AreaVisitController::class, 'filter']);
        Route::resource('visitarea', AreaVisitController::class);
        Route::get('/visithistory-dealer', [DealerVisitController::class, 'filter']);
        Route::resource('visitdealer', DealerVisitController::class);
        Route::get('/negotiation-report', [NegotiationController::class, 'filter']);
        Route::get('/negotiation/map/{id}', [NegotiationController::class, 'map']);
        Route::resource('negotiation', NegotiationController::class);


        // TADA Report
        // Note: dashboard-based creation is disabled. Keep other resource routes but exclude create/store.
        Route::resource('tada-report', TadaReportController::class)->except(['create', 'store']);
        Route::get('tada-report/{id}/download-pdf', [TadaReportController::class, 'downloadPdf'])->name('tada-report.pdf');
        // Export filtered TADA list to Excel
        Route::post('/tada-export', [TadaReportController::class, 'export'])->name('tada-report.export');
        // Admin Daily Report export
        Route::post('/tada-admin-export', [TadaReportController::class, 'adminExport'])->name('tada-report.admin-export');
        Route::get('/expense-report', [TadaReportController::class, 'expenseReport'])->name('expense-report.index');
        Route::get('/expense-report/export', [TadaReportController::class, 'exportExpenseReport'])->name('expense-report.export');

        // Attendance
        Route::get('/attendance/filter', [AttendanceController::class, 'filter']);
        Route::get('attendance', [AttendanceController::class, 'index']);
        Route::get('/attendance/{id}', [AttendanceController::class, 'view']);
        Route::get('/attendance/view/{id}', [AttendanceController::class, 'show']);

        //Attendance Image
        Route::get('attendanceimage/{id}', [AttendanceController::class, 'attendanceimageshow'])->name('attendanceimage');

        // UpdateKM for Attendance
        Route::get('attendance/updatekm/{id}', [AttendanceController::class, 'updatekmformshow']);
        Route::patch('attendance/updatekm/{id}', [AttendanceController::class, 'updatekmformpost'])->name('attendanceupdatekm');
        Route::resource('attendanceReport', AttendanceReportController::class);
        Route::get('/report/attendance', [AttendanceReportController::class, 'search']);
        Route::get('/report/attendance/download', [AttendanceReportController::class, 'download']);
        Route::get('/report/attendance/summary', [AttendanceReportController::class, 'summary']);
        Route::resource('monthlySummary', AttendanceSummaryController::class);

        // user-wise visit report export
        Route::post('/visit-export', [VisitExportController::class, 'visit_Report']);
        Route::post('/visit-detailed-export', [VisitExportController::class, 'visit_Detailed_Report']);
        Route::post('/mis-Report', [VisitExportController::class, 'MIS_Report']);
        // area-wise visit report export
        Route::post('/area-visit-export', [AreaVisitExportController::class, 'area_visit_Report']);
        Route::post('/area-visit-detailed-export', [AreaVisitExportController::class, 'Area_visit_Detailed_Report']);
        // Dealer-wise visit report export
        Route::post('/dealer-visit-export', [DealerVisitExportController::class, 'dealer_visit_report']);
        Route::post('/dealer-visit-detailed-export', [DealerVisitExportController::class, 'dealer_visit_detailed_report']);
        // Unlisted Dealer visit report export
        Route::post('/unlisted-visit-export', [UnlistedExportController::class, 'unlisted_visit_Report']);
        Route::post('/unlisted-visit-detailed-export', [UnlistedExportController::class, 'unlisted_visit_Detailed_Report']);

        //Dealer ot Retailer Route
        Route::get('/dealerOrRetailer', [DealerController::class, 'dealerOrRetailer']);
    });

    Route::group(['middleware' => ['permission:user']], function () {
        Route::resource('role-permission', PermissionController::class);
        Route::resource('user', MobileUserController::class);
    });

    Route::group(['middleware' => ['permission:dealer']], function () {
        // For Dealer
        Route::POST('/dealers/excel', [DealerController::class, 'excelUpload']);
        Route::get('/dealers-map', [DealerController::class, 'dealermap']);
        Route::get('/mapdata', [DealerController::class, 'mapdata']);
        Route::resource('dealer', DealerController::class);
        Route::get('dealerExport', [DealerController::class, 'dealerExport']);
    });

    Route::group(['middleware' => ['permission:area']], function () {
        //for Area CRUD
        Route::resource('area', AreaController::class);
    });

    Route::group(['middleware' => ['permission:rawmaterial']], function () {
        //begin for raw materials
        Route::resource('raw', RawMaterialController::class);
        //for raw-products
        Route::resource('raw-product', RawProductController::class);
        //rawpurchase
        Route::resource('rawpurchase', RawPurchaseController::class);
        //productmanufactures
        Route::resource('product-manufactures', ProductManufactureController::class);
        //inventory
        Route::resource('inventory', RawInventoryController::class);
    });

    Route::group(['middleware' => ['permission:productinventory']], function () {
        ///begin for product inventory///
        Route::resource('productmanufac', ProductInventorymanufController::class);
        Route::get('/get-size/{product_id}', [ProductInventorymanufController::class, 'getSize']);

        Route::resource('producinventory', ProductInventoryController::class);

        Route::resource('product-dealer', ProductDealerController::class);
        Route::get('/get-size/{product_id}', [ProductDealerController::class, 'getSize']);

        Route::resource('subdealer-inventory', SubdealerInventoryController::class)->except(['edit', 'update', 'destroy']);
        Route::get('subdealer-inventory-reduce/create', [SubdealerInventoryController::class, 'reduceCreate'])->name('subdealer-inventory-reduce.create');
        Route::post('subdealer-inventory-reduce', [SubdealerInventoryController::class, 'reduceStore'])->name('subdealer-inventory-reduce.store');
    });

    Route::group(['middleware' => ['permission:product']], function () {
        // For Category
        Route::resource('category', CategoryController::class);

        // For Category
        Route::resource('size', SizeController::class);

        // For Products
        Route::get('/product/images/{id}', [ProductController::class, 'viewImage']);
        Route::POST('/product/images', [ProductController::class, 'imageSave']);
        Route::get('/product/images/{id}/all', [ProductController::class, 'imgView']);
        Route::DELETE('/image/delete/{id}', [ProductController::class, 'unlink']);
        Route::resource('product', ProductController::class);

        // For Final Product
        Route::resource('final-product', FinalProductController::class);

        //Final Product Export
        Route::get('/exportproduct', [ExcelExportController::class, 'export']);

        //Raw Materials Inventory
        Route::get('/exportinventory', [ExcelInventoryController::class, 'export']);

        //Raw Product Inventory
        Route::get('/exportrawproduct', [ExcelRawProductController::class, 'export']);
        Route::get('/exportproductmanufacture', [ExcelProductManufactureController::class, 'export']);
        Route::post('/products/excel', [ProductController::class, 'excelProduct']);
    });

    Route::post('/bank/excel', [BankController::class, 'bankexcel'])->middleware('permission:bank');
    Route::resource('bank', BankController::class)->middleware('permission:bank');

    //For Upload ledger file
    Route::resource('uploadfile', AccountController::class)->only(['index', 'store', 'create'])->middleware('permission:upload-due');

    // Distance Setting
    Route::resource('distance', SettingController::class)->only(['index', 'store'])->middleware('permission:distance');

    // Auto Checkout Time Setting
    Route::get('checkout-time', [SettingController::class, 'checkoutIndex'])->name('checkout-time.index')->middleware('permission:auto-checkout');
    Route::post('checkout-time', [SettingController::class, 'checkoutStore'])->name('checkout-time.store')->middleware('permission:auto-checkout');
    // For Denomination
    Route::resource('denomination', DenominationController::class)->middleware('permission:denomination');

    // For Purpose
    Route::resource('purpose', PurposeController::class)->middleware('permission:purpose');

    // For Role
    Route::resource('role', RoleController::class)->middleware('permission:role');

    Route::get('/privacy-policy/status/{privacypolicy_id}', [PrivacyPolicyController::class, 'status'])->middleware('permission:privacy-policy');
    Route::resource('/privacy-policy', PrivacyPolicyController::class)->middleware('permission:privacy-policy');

    Route::get('/getdealer', [DealerController::class, 'getdealer']);
    Route::get('/getsubarea', [SubareaController::class, 'getsubarea']);
    Route::resource('subarea', SubareaController::class)->only(['store', 'show', 'update', 'edit']);

    //User Location View
    Route::post('/user-location/{id}', [LocationController::class, 'userLocation'])->name('user.location');
    Route::post('/origin/{id}', [OrderController::class, 'getOrderLocationDetails'])->name('origin');
    Route::post('visit/origin/{id}', [VisitController::class, 'getVisitLocationDetails'])->name('visit.origin');


    // Site Registration

    Route::get('/export-site-registrations-v2', [SiteRegistrationControllerV2::class, 'export'])->name('export.site.registrations.v2');

    //V2 Site Registration
    Route::resource('site-registration-v2', SiteRegistrationControllerV2::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
    Route::get('/site-registration/{site}/activitylog', [SiteRegistrationControllerV2::class, 'getActivityLogs']);
    Route::get('site-registration-v2-map', [SiteRegistrationControllerV2::class, 'siteMaps'])->name('site.registration.v2.map');
    Route::resource('followUp', FollowUpControllerV2::class)->only(['index', 'create']);
});

Route::get('notification/reminder/3jR4GUzghGFTrpy2', [AlertReminderController::class, 'index']);
Route::get('privacy_policy', [PrivacyPolicyController::class, 'privacypolicy']); //privacy-policy page

Route::get('/artisan-command', function (Request $request) {
    $command = $request->query('command');
    Artisan::call($command);
    return response()->json('success');
});
