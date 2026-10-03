<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\DealerController;
use App\Http\Controllers\API\DeviceLogController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\MobileuserController;
use App\Http\Controllers\API\MyTeamController;
use App\Http\Controllers\API\NoticeController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\SessionController;
use App\Http\Controllers\API\v2\SiteRegistrationController;
use App\Http\Controllers\API\v3\OrderController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [MobileuserController::class, 'authenticate']);

Route::group(['middleware' => ['auth:api', 'token.api', 'user.status']], function () {
    //Home Data
    Route::get('/home', [MobileuserController::class, 'home']);

    // nearby dealers
    Route::get('/dealerWithboundry', [DealerController::class, 'dealerWithboundry']);

    // count Amount and Visit of the day
    Route::get('/countAmountandVisit', [PaymentController::class, 'index']);

    //     order history
    Route::get('/orderHistory', 'API\OrderController@orderHistory');

    //collection History
    Route::get('/collectionHistory', [PaymentController::class, 'collectionHistory']);

    // visit schedule
    Route::get('/visitSchedule', 'API\MobileuserController@visitSchedule');
    Route::get('/today', 'API\MobileuserController@visitScheduleToday');
    Route::get('/visit', 'API\MobileuserController@schedule');
    Route::get('/visit/{date}', [MobileuserController::class, 'visitHistory']);
    Route::get('/rangehistory', [MobileuserController::class, 'rangehistory']);

    // message
    Route::get('/message', [MessageController::class, 'index']);
    Route::post('/send', [MessageController::class, 'store']);

    Route::get('/banks', 'API\BankController@index');
    Route::get('/purpose', 'API\PurposeController@index');

    Route::get('/profile/{id}', 'API\MobileuserController@show');
    Route::get('/profile/{id}/edit', 'API\MobileuserController@update');
    Route::get('/mobileusers', 'API\MobileuserController@index');
    Route::put('/profile/{id}/pass', 'API\MobileuserController@change');

    Route::get('/sites', 'API\SiteController@index');
    Route::post('/site', 'API\SiteController@store');
    Route::get('/site/{id}', 'API\SiteController@show');
    Route::put('/site/{id}', 'API\SiteController@update');

    //Dealer related routes
    Route::get('/dealers', [DealerController::class, 'index']);
    Route::get('/dealers/reposition', [DealerController::class, 'reposition']);
    Route::get('/dealers/position', [DealerController::class, 'position']);
    Route::get('/dealer/{code}', [DealerController::class, 'show']);
    Route::get('/dealer/{code}/update', [DealerController::class, 'update']);

    Route::post('/dealerlog', 'API\DealerlogController@store');

    Route::get('/areas', 'API\AreaController@index');
    Route::get('/area/{areacodes}/reposition', 'API\AreaController@reposition');
    Route::get('/area/{areacodes}/position', 'API\AreaController@position');
    Route::get('/area/{areacodes}/pending', 'API\AreaController@pending');
    Route::post('/area/{areacodes}/dist', 'API\AreaController@visit');

    Route::get('/{id}/history/{date}', 'API\SessionController@index');
    Route::post('/session', [SessionController::class, 'store']); //
    Route::post('/negotiation', [SessionController::class, 'new']); //negotiation
    Route::post('v5/negotiation', [SessionController::class, 'v5negotiation']); //negotiation

    Route::post('/locations', 'API\LocationController@insert');
    Route::get('/locationinfo', 'API\LocationController@index');

    Route::get('/products', 'API\ProductController@index');

    Route::post('/order', [OrderController::class, 'store']);

    Route::post('/payment', [PaymentController::class, 'store']);

    Route::get('/notice', [NoticeController::class, 'index']);
    Route::get('/notice/{id}', [NoticeController::class, 'show']);
    Route::get('/orderDetails/{id}', [NoticeController::class, 'orderDetails']);
    Route::get('/collectionDetails/{id}', [NoticeController::class, 'collectionDetails']);

    Route::patch('/updatestatus/{id}', 'API\PaymentController@updatestatus');

    // test
    Route::get('/test', 'API\SessionController@test');
    Route::get('/m', 'API\MessageController@m');
    Route::post('/attendance/offline', 'API\AttandenceController@offline');
    Route::get('/all', 'API\NoticeController@all');
    Route::get('/repeateschedule', 'API\RepeatescheduleController@index');
    Route::post('/attendance', [AttendanceController::class, 'store']);

    Route::apiResource('ledger', 'API\LedgerController')->only(['store']);
    Route::post('/logout', [MobileuserController::class, 'logout']);

    // Device log
    Route::post('/device-log', [DeviceLogController::class, 'store']);

    //Contractor and Site Registration Routes
    Route::get('contractors', [SiteRegistrationController::class, 'indexContractor']);
    Route::post('contractors', [SiteRegistrationController::class, 'storeContractor']);
    Route::resource('site-registrations', SiteRegistrationController::class)->only(['index', 'store']);

    //My Team Routes
    Route::get('my-teams', [MyTeamController::class, 'index']);
    Route::get('my-team/{user}/{date}', [MyTeamController::class, 'show']);

    //unlisted dealer
    Route::get('unlisted-dealers', [DealerController::class, 'unlistedDealers']);

    // TADA Report API
    Route::post('/tada/upload-image', [\App\Http\Controllers\API\TadaReportController::class, 'uploadImage']);
    Route::delete('/tada-expenses/images/{image_id}', [\App\Http\Controllers\API\TadaReportController::class, 'deleteExpenseImage']);
    Route::post('/tada-expenses/images/delete', [\App\Http\Controllers\API\TadaReportController::class, 'deleteExpenseImage']);
    Route::get('/tada/authorized-users', [\App\Http\Controllers\API\TadaReportController::class, 'authorizedUsers']);
    Route::get('/tada/expense-authorized-users', [\App\Http\Controllers\API\TadaReportController::class, 'expenseAuthorizedUsers']);
    Route::get('/tada', [\App\Http\Controllers\API\TadaReportController::class, 'index']);
    Route::post('/tada', [\App\Http\Controllers\API\TadaReportController::class, 'store']);
    Route::get('/tada/{id}', [\App\Http\Controllers\API\TadaReportController::class, 'show']);
    Route::put('/tada/{id}', [\App\Http\Controllers\API\TadaReportController::class, 'update']);
    Route::delete('/tada/{id}', [\App\Http\Controllers\API\TadaReportController::class, 'destroy']);
});

Route::get('due/{area_code}/{dealer_code}', 'API\AccountController@getdue');
Route::get('ageing/{area_code}/{dealer_code}', 'API\AccountController@getdata');
