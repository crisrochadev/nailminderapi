<?php

use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLayoutPageController;
use App\Http\Controllers\UserPageController;
use App\Models\UserLayoutPage;
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

    //Pages
    Route::post('/user/page',[UserPageController::class, 'register']);
    Route::get('/user/page/{user_id}',[UserPageController::class, 'getPageByUserId']);
    Route::put('/user/page/{page_id}',[UserPageController::class, 'updatePage']);

    //Layouts
    Route::get('/user/layout-pages',[UserLayoutPageController::class, 'getLayouts']);
    Route::put('/user/layout-pages/{layout_id}',[UserLayoutPageController::class, 'updateLayout']);
});
Route::prefix('auth')->group(function(){
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register',[AuthController::class, 'register']);
    Route::post('reset-pass', [AuthController::class, 'resetpass']);
    Route::post('check-code', [AuthController::class, 'checkCode']);
    Route::put('update-pass', [AuthController::class, 'updatePass']);
    Route::post('email-verify', [AuthController::class, 'emailVerify']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('login/{provider}', [AuthController::class, 'redirectToProvider']);
    Route::post('login/{provider}/callback', [AuthController::class, 'handleProviderCallback']);
    // Route::post('profile', [AuthController::class, 'profile'])->middleware('verified');
    // Route::post('refresh', [AuthController::class, 'refresh']);

    

})->middleware('api');

Route::prefix('public')->group(function () {
    Route::get('/page/{user_id}', [UserPageController::class, 'getPageById']);
    Route::get('/user-layout/{slug}', [UserController::class, 'getUserBySlug']);
});

