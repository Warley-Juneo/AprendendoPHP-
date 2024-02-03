<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/v1')->group(function () {

    // public
    Route::post('/auth', [AuthController::class, 'login']);

    // protected with auth
    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/auth', [AuthController::class, 'logout']);
        Route::get('/users/profile', [UserController::class, 'getUserProfile']);
        Route::resource('/leads', LeadController::class);
    });

    // protected on construct
    Route::resource('/users', UserController::class);
});

