<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('update/{id}', [AuthController::class, 'update']);

});

Route::group([

    'middleware' => 'api',
    'prefix' => 'appointment'

], function ($router) {

    Route::post('add', [AppointmentController::class, 'store'])->middleware(['api', 'auth:api']);
    Route::post('update/{id}', [AppointmentController::class, 'update'])->middleware(['api', 'auth:api']);
    Route::post('update/assignment/{id}', [AppointmentController::class, 'assignment'])->middleware(['api', 'auth:api']);
    Route::get('update/assignment/done{id}', [AppointmentController::class, 'done'])->middleware(['api', 'auth:api']);
    Route::get('show/{id}', [AppointmentController::class, 'show'])->middleware(['api', 'auth:api']);
    Route::get('all', [AppointmentController::class, 'all'])->middleware(['api', 'auth:api']);
    Route::delete('delete/{id}', [AppointmentController::class, 'destroy'])->middleware(['api', 'auth:api']);
    Route::post('pagination', [AppointmentController::class, 'pagination'])->middleware(['api', 'auth:api']);
});







