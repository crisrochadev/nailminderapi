<?php

use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
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



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'index']);
    Route::put('/user', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePass']);

    Route::post('/image',[UploadController::class, 'imageStore']);
});
Route::prefix('auth')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register',[AuthController::class, 'register']);
    Route::post('reset-pass', [AuthController::class, 'resetpass']);
    Route::post('check-code', [AuthController::class, 'checkCode']);
    Route::post('update-pass', [AuthController::class, 'updatePass']);
    Route::post('email-verify', [AuthController::class, 'emailVerify']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::post('login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    // Route::post('profile', [AuthController::class, 'profile'])->middleware('verified');
    // Route::post('refresh', [AuthController::class, 'refresh']);
})->middleware('api');

