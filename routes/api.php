<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('admin/send-link', [App\Http\Controllers\Api\AuthController::class, 'sendInvitation'])->name('send-link');

Route::post('auth/confirm-registration', [App\Http\Controllers\Api\AuthController::class, 'confirmRegistration'])->name('confirm-registration');

Route::group(['middleware' => ['json.response', 'localization']], function () {
    //Auth
    Route::post('auth/login', [App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
    Route::post('auth/register', [App\Http\Controllers\Api\AuthController::class, 'register'])->name('register');

    Route::post('check/login', [App\Http\Controllers\Api\AuthController::class, 'checkLogin'])->name('check.login');
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logoutUser'])->name('logoutUser')->middleware('auth:api');
    Route::get('auth/getProfile', [App\Http\Controllers\Api\AuthController::class, 'getProfile'])->name('getProfile')->middleware('auth:api');

    Route::post('update-profile', [\App\Http\Controllers\Api\AuthController::class, 'userProfile'])->name('update-profile')->middleware('auth:api');

});