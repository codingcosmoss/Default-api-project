<?php

use App\Http\Controllers\Api\AController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('/auth-user', [AuthController::class, 'authUser']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::get('test', [AController::class, 'index']);

});
