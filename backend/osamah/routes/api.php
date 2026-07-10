<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\FileUploadController;
use App\Http\Middleware\ApiTokenAuth;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiController::class, 'login']);
Route::post('/register', [ApiController::class, 'register']);

Route::middleware(ApiTokenAuth::class)->group(function () {
    Route::get('/me', [ApiController::class, 'me']);
    Route::get('/orders', [ApiController::class, 'orders']);
    Route::get('/cart/{order}', [ApiController::class, 'cart']);
    Route::post('/cart/{order}/pay', [ApiController::class, 'pay']);

    Route::post('/upload-file', [FileUploadController::class, 'upload']);
    Route::patch('/order-files/{file}', [FileUploadController::class, 'updateFile']);
    Route::delete('/order-files/{file}', [FileUploadController::class, 'destroyFile']);

    Route::patch('/account/profile', [ApiController::class, 'updateProfile']);
    Route::patch('/account/address', [ApiController::class, 'updateAddress']);
    Route::patch('/account/password', [ApiController::class, 'updatePassword']);
});
