<?php

use App\Http\Controllers\API\v3\AttendanceController;
use App\Http\Controllers\API\v3\LocationController;
use App\Http\Controllers\API\v3\MessageController;
use App\Http\Controllers\API\v3\MobileuserController;
use App\Http\Controllers\API\v3\OrderController;
use App\Http\Controllers\API\v3\PaymentController;
use App\Http\Controllers\API\v3\SessionController;
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
    Route::get('/orderHistory', [OrderController::class, 'index']);
    Route::get('/order/filter', [OrderController::class, 'filter']);
    Route::get('/orderDetails/{id}', [OrderController::class, 'show']);
    Route::post('/locations', [LocationController::class, 'store']);
    Route::post('/order', [OrderController::class, 'store']);
    Route::post('/negotiation', [SessionController::class, 'store']);
    Route::get('/debateScheme', 'API\v3\DebateSchemeController@index');
    Route::get('/userlist', [MessageController::class, 'userlist']);
    Route::apiResource('message', MessageController::class)->only(['index', 'store']);
    Route::resource('receipt', 'API\v3\ReceiptController')->only(['index', 'show', 'store']);
    Route::apiResource('payment', PaymentController::class)->only(['store', 'show']);
    Route::post('/offline', 'API\v3\AttendanceController@offline');
    Route::apiResource('attendance', AttendanceController::class)->only(['store']);
});
