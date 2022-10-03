<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {
    Route::get('login', [\App\Http\Controllers\Client\AuthController::class, 'viewLogin'])->name('login');
    Route::post('login-check', [\App\Http\Controllers\Client\AuthController::class, 'loginCheck'])->name('login.check');
    Route::get('login/{token}', [\App\Http\Controllers\Client\AuthController::class, 'viewPassword'])->name('login.password');
    Route::post('login', [\App\Http\Controllers\Client\AuthController::class, 'login'])->name('post.login');

    Route::get('register', [\App\Http\Controllers\Client\AuthController::class, 'viewRegister']);
    Route::post('register', [\App\Http\Controllers\Client\AuthController::class, 'register']);
    Route::post('verify-register-{id}', [\App\Http\Controllers\Client\AuthController::class, 'verifyRegister'])->name('verify.register');

    Route::post('forgot-password', [\App\Http\Controllers\Client\AuthController::class, 'viewForgotPassword'])->name('forgot-password');
    Route::post('verify-forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'verifyForgotPassword']);

    Route::get('verify-otp', [\App\Http\Controllers\OtpController::class, 'index'])->name('verify.otp');
    Route::get('resend-otp', [\App\Http\Controllers\OtpController::class, 'index'])->name('resend.otp');
});

Route::group([], function () {
    Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/change-password', [\App\Http\Controllers\HomeController::class, 'index'])->name('account.change.password');
    Route::post('/logout', [\App\Http\Controllers\Client\AuthController::class, 'logout'])->name('logout');
});
