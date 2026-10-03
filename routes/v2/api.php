<?php

use App\Http\Controllers\API\v2\AttendanceController;
use App\Http\Controllers\API\v2\MobileuserController;
use App\Http\Controllers\API\v2\PaymentController;
use App\Http\Controllers\API\v2\ProductController;
use App\Http\Controllers\API\v2\NoticeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\v2\SiteRegistrationController;

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

Route::post('/login', 'API\v2\MobileuserController@authenticate');
// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware' => ['auth:api', 'token.api', 'user.status']], function () {
    // Route::group(['middleware'=>"token"],function(Route::){

    // nearby dealers
    Route::get('/dealerWithboundry', 'API\v2\DealerController@dealerWithboundry');

    // count Amount and Visit of the day
    Route::get('/countAmountandVisit', 'API\v2\PaymentController@countAmountandVisit');

    //     order history
    Route::get('/orderHistory', 'API\v2\OrderController@orderHistory');

    // today's visit
    Route::get('/visit', 'API\v2\NegotiationController@showNegotiation');

    //collection History
    Route::get('/collectionHistory', [PaymentController::class, 'collectionHistory']);

    // visit schedule
    Route::get('/visitSchedule', [MobileuserController::class, 'visitSchedule']);
    Route::get('/today', 'API\v2\MobileuserController@visitScheduleToday');
    Route::get('/visit', 'API\v2\MobileuserController@schedule');
    Route::get('/visit/{date}', 'API\v2\MobileuserController@visitHistory');

    // message
    Route::get('/message', 'API\v2\MessageController@receive');
    Route::post('/send', 'API\v2\MessageController@send');

    Route::get('/banks', 'API\v2\BankController@index');
    Route::get('/purpose', 'API\v2\PurposeController@index');

    Route::get('/profile/{id}', 'API\v2\MobileuserController@show');
    Route::get('/profile/{id}/edit', 'API\v2\MobileuserController@update');
    Route::get('/mobileusers', 'API\v2\MobileuserController@index');
    Route::put('/profile/{id}/pass', 'API\v2\MobileuserController@change');

    Route::get('/sites', 'API\v2\SiteController@index');
    Route::post('/site', 'API\v2\SiteController@store');
    Route::get('/site/{id}', 'API\v2\SiteController@show');
    Route::put('/site/{id}', 'API\v2\SiteController@update');

    Route::get('/dealers/reposition', 'API\v2\DealerController@reposition');
    Route::get('/dealers/position', 'API\v2\DealerController@position');
    Route::get('/dealer/{code}', 'API\v2\DealerController@show');
    Route::get('/dealer/{code}/update', 'API\v2\DealerController@update');

    Route::post('/dealerlog', 'API\v2\DealerlogController@store');

    Route::get('/areas', 'API\v2\AreaController@index');
    // Route::get('/area/{areacode}', 'AreaController@show');
    Route::get('/area/{areacodes}/reposition', 'API\v2\AreaController@reposition');
    Route::get('/area/{areacodes}/position', 'API\v2\AreaController@position');
    Route::get('/area/{areacodes}/pending', 'API\v2\AreaController@pending');
    Route::post('/area/{areacodes}/dist', 'API\v2\AreaController@visit');

    Route::get('/{id}/history/{date}', 'API\v2\SessionController@index');
    Route::post('/session', 'API\v2\SessionController@store');
    Route::post('/negotiation', 'API\v2\SessionController@new');

    Route::post('/locations', 'API\v2\LocationController@insert');
    Route::get('/locationinfo', 'API\v2\LocationController@index');

    Route::get('/products', [ProductController::class, 'index']);

    Route::post('/order', 'API\v2\OrderController@place');
    Route::get('/order/filter', 'API\v2\OrderController@filter');

    Route::post('/payment', [PaymentController::class, 'index']);
    Route::get('/payment/filter', [PaymentController::class, 'filter']);

    Route::get('/notice', [NoticeController::class, 'index']);
    Route::get('/notice/{id}', [NoticeController::class, 'individual']);
    Route::get('/orderDetails/{id}', [NoticeController::class, 'orderDetails']);
    Route::get('/collectionDetails/{id}', [NoticeController::class, 'collectionDetails']);

    Route::patch('/updatestatus/{id}', [PaymentController::class, 'updatestatus']);

    // test
    Route::get('/test', 'API\v2\SessionController@test');
    Route::get('/m', 'API\v2\MessageController@m');
    Route::get('/all', [NoticeController::class, 'all']);
    Route::get('/repeateschedule', 'API\v2\RepeatescheduleController@index');
    Route::post('/attendance/offline', 'API\v2\AttandenceController@offline');
    Route::post('/attendance', 'API\v2\AttandenceController@store');
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::post('/attendance/offline', [AttendanceController::class, 'offline']);
    Route::get('/attendance', [AttendanceController::class, 'index']);


    Route::PATCH('/cancel/{id}', 'API\v2\OrderController@editOrder');

    Route::PATCH('/singleCancel/{id}', 'API\v2\OrderController@cancelOrderItem');

    Route::get('/denomination', 'API\v2\DenominationController@index');

    Route::get('/profile', 'API\v2\MobileuserController@profile');

    //Site Registration V2 APIs
    Route::get('/site-registration', [SiteRegistrationController::class, 'index']);
    Route::post('/site-registration', [SiteRegistrationController::class, 'store']);
    Route::get('/site-registration/{site}', [SiteRegistrationController::class, 'show']);
    Route::patch('/site-registration/{site}/updateSite', [SiteRegistrationController::class, 'updateSite']);
    Route::patch('/site-registration/{followUp}', [SiteRegistrationController::class, 'update']);
    Route::post('/site-registration/{site}/add-contact', [SiteRegistrationController::class, 'addContact']);
    Route::delete('/site-registration/{contact}/delete-contact', [SiteRegistrationController::class, 'deleteContact']);
    //Site Products
    Route::get('site-products', [SiteRegistrationController::class, 'getSiteProducts']);
    Route::patch('site-products/{site}/{siteProduct}', [SiteRegistrationController::class, 'toggleProduct']);
});
