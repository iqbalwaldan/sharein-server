<?php

use App\Http\Controllers\Client\User\AuthController;
use App\Http\Controllers\Client\User\AutoPostController;
use App\Http\Controllers\Client\User\DashboardController;
use App\Http\Controllers\Client\User\FacebookAccountController;
use App\Http\Controllers\Client\User\GroupPostController;
use App\Http\Controllers\Client\User\ManageScheduleController;
use App\Http\Controllers\Client\User\ProfileController;
use App\Http\Controllers\Client\User\ReminderController;
use App\Http\Controllers\Client\User\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'indexLogin'])->name('user.login');
    // Route::get('/admin/login', [AdminAuthController::class, 'indexLogin'])->name('admin.login');

    Route::get('/register', [AuthController::class, 'indexRegister'])->name('user.register');

    Route::post('/login', [AuthController::class, 'login'])->name('user.loginPost');
    // Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.loginPost');

    Route::post('/register', [AuthController::class, 'register'])->name('user.registerPost');
    Route::get('/forgot-password', [AuthController::class, 'indexForgotPassword'])->name('user.forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('user.reset-link-password');

    Route::get('/reset-password/{token}', [AuthController::class, 'indexResetPassword'])->name('user.reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('user.update-password');
});

Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        // Admin routes
        Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/dashboard/getFacebookData', [DashboardController::class, 'getFacebookData'])->name('user.dashboard.getFacebookData');
    Route::get('/dashboard/schedules', [DashboardController::class, 'schedule'])->name('user.dashboard.shcedule');
    Route::get('/dashboard/reminders', [DashboardController::class, 'reminder'])->name('user.dashboard.reminder');
    Route::get('/group-post', [GroupPostController::class, 'index'])->name('user.group-post');
    Route::resource('/auto-post', AutoPostController::class)->names('user.auto-post');
    // Route::post('/post', [AutoPostController::class, 'post'])->name('user.auto-post.post');


    Route::resource('/reminder', ReminderController::class)->names('user.reminder');
    Route::resource('/profile', ProfileController::class)->names('user.profile');
    Route::resource('/manage-schedule', ManageScheduleController::class)->names('user.manage-schedule');
    Route::resource('/facebook-account', FacebookAccountController::class)->names('user.facebook-account');
    Route::get('/update-cookies', [FacebookAccountController::class, 'updateCookies'])->name('user.facebook-account.updateCookies');
    Route::post('/logout', [AuthController::class, 'logout'])->name('user.logout');
});

// Daftar route cronjob
Route::get('/post-schedule', [AutoPostController::class, 'schedulePost'])->name('schedule.post');
Route::get('/send-reminder', [ReminderController::class, 'sendReminder'])->name('reminder.send');

Route::get('/get-facebook-data', [AutoPostController::class, 'getFacebookData'])->name('get.facebook.data');
Route::get('/email-verification/success', [AuthController::class, 'emailVerificationSuccess'])->name('email-verification.success');
Route::get('/email-verification/already-success', [AuthController::class, 'emailVerificationAlreadySuccess'])->name('email-verification.Alreadysuccess');
Route::get('/email-verification/process', [AuthController::class, 'emailVerificationProcess'])->name('email-verification.process');
Route::get('/email-verification/resend', [AuthController::class, 'resendEmailVerification'])->name('email-verification.resend');
Route::get('/token', [DashboardController::class, 'fetchFacebookData']);

Route::get('/auth/facebook', [SocialAuthController::class, 'redirectToProvider'])->name('auth.facebook');
Route::get('/auth/facebook/call-back', [SocialAuthController::class, 'handleProviderCallback'])->name('auth.facebook.callback');
