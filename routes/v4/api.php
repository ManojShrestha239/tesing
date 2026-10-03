<?php

use App\Http\Controllers\API\v4\AttendanceController;
use App\Http\Controllers\API\v4\AlertReminderController;
use App\Http\Controllers\API\v4\ComplaintController;
use App\Http\Controllers\API\v4\SessionController;
use Illuminate\Http\Request;
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

Route::group(['middleware' => ['auth:api', 'token.api', 'user.status']], function () {
    Route::post('/negotiation', [SessionController::class, 'new']);
    Route::post('/offline/negotiation', [SessionController::class, 'store']);
    Route::apiResource('reminder', AlertReminderController::class);
    Route::apiResource('complaint', ComplaintController::class, array("as" => "api"));
    Route::get('issue_type', [ComplaintController::class, 'issue_type'], array("as" => "api"));
    Route::post('/location', [SessionController::class, 'loctaion'], array("as" => "api"));

    //Attendance
    Route::post('attendance', [AttendanceController::class, 'online'], array("as" => "api"));
    Route::post('attendancewithkm', [AttendanceController::class, 'onlinewithkm'], array("as" => "api"));

    Route::post('/offline', [AttendanceController::class, 'offline'], array("as" => "api"));

    Route::get('/attendance', [AttendanceController::class, 'getAttendance']);
});
