<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\UserController;
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

Route::post('signup', [UserController::class, 'signUp']);
Route::post('login', [UserController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get("email/verify", [UserController::class, "verify"])->name("verificationapi.verify");
    Route::get("email/resend", [UserController::class, "resend"])->name("verificationapi.resend");


    Route::get('logout', [UserController::class, 'logOut']);
    Route::get('ResetPasswordRequest', [UserController::class, 'ResetPasswordRequest']);
    Route::post('ResetPassword', [UserController::class, 'ResetPassword']);

    Route::get('services', [ServiceController::class, 'index']);

    Route::prefix('Reservation')->group(function () {
        Route::get('', [UserController::class, 'MyReservation']);
        Route::post('', [ReservationController::class, 'store']);
        Route::post('/update/{reservation}', [ReservationController::class, 'update']);
        Route::delete('/{reservation}', [ReservationController::class, 'destroy']);
    });

    Route::get('Staff', [StaffController::class, 'index']);

});


Route::post('Admin/signup', [AdminController::class, 'signUp']);
Route::post('Admin/login', [AdminController::class, 'login']);

Route::middleware(['auth:admin-api'])->group(function () {
    Route::prefix('Admin')->group(function () {
        Route::get('services', [ServiceController::class, 'index']);
        Route::post('AddService', [ServiceController::class, 'store']);
        Route::delete('DeleteService', [ServiceController::class, 'deleteService']);

        Route::get('BlockServices/{service}', [ServiceController::class, 'BlockService']);
        Route::get('Reservation', [ReservationController::class, 'index']);
        Route::get('AcceptReservation/{reservation}', [ReservationController::class, 'AcceptReservation']);
        Route::delete('DeleteReservation/{reservation}', [ReservationController::class, 'DestroyByAdmin']);
        Route::post('Staff', [StaffController::class, 'store']);
        Route::delete('Staff/{staff}', [StaffController::class, 'destroy']);

        Route::get('/logout', [AdminController::class, 'logOut']);
        Route::get('/ResetPasswordRequest', [AdminController::class, 'ResetPasswordRequest']);
        Route::post('/ResetPassword', [AdminController::class, 'ResetPassword']);

    });

});
