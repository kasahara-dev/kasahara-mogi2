<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController as AdminStaffController;
use App\Http\Controllers\RestController;
use App\Http\Controllers\RequestedAttendanceController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Admin\RequestedAttendanceController as AdminRequestedAttendanceController;
use App\Http\Controllers\RequestController;



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

Route::get('/register', [UserController::class, 'create']);
Route::post('/register', [UserController::class, 'store']);
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'show'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/attendance');
    })->middleware(['auth', 'signed'])->name('verification.verify');
    Route::post('/email/verification-notification', function (Request $request) {
        Auth::user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');
});
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'create']);
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::patch('/attendance/{id}', [AttendanceController::class, 'update']);
    Route::post('/attendance/rest', [RestController::class, 'store']);
    Route::patch('/attendance/rest/{id}', [RestController::class, 'update']);
    Route::get('/attendance/list', [AttendanceController::class, 'show']);
    Route::get('/attendance/detail/{id}', [RequestedAttendanceController::class, 'create']);
    Route::post('/attendance/detail/{id}', [RequestedAttendanceController::class, 'store']);
    Route::get('/requested_attendance/detail/{id}', [RequestedAttendanceController::class, 'show']);
    Route::get('/stamp_correction_request/list', [RequestController::class, 'show']);
});
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/login', [AdminLoginController::class, 'store']);
    Route::middleware('auth:admin')->name('admin.')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index']);
        Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'show']);
        Route::get('/attendance/{id}', [AdminAttendanceController::class, 'edit']);
        Route::put('/attendance/{id}', [AdminAttendanceController::class, 'update']);
        Route::get('/requested_attendance/{id}', action: [AdminRequestedAttendanceController::class, 'show']);
        Route::get('/staff/list', [AdminStaffController::class, 'index']);
        Route::post('/logout', [AdminLoginController::class, 'destroy']);
    });
});
Route::middleware('auth:admin')->group(function () {
    // Route::get('/stamp_correction_request/list', [AdminRequestController::class, 'show']);
});

