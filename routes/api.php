<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerAuthController;
use App\Http\Controllers\Api\CashupPaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Public (frontend React) + Admin (auth:sanctum, prefixed /admin)
*/

// ---- Public ----
Route::get('tours', [TourController::class, 'index']);
Route::get('tours/{slug}', [TourController::class, 'show']);

Route::get('destinations', [DestinationController::class, 'index']);
Route::get('destinations/{slug}', [DestinationController::class, 'show']);

Route::get('posts', [PostController::class, 'index']);
Route::get('posts/{slug}', [PostController::class, 'show']);

Route::get('testimonials', [TestimonialController::class, 'index']);
Route::get('about', [AboutController::class, 'show']);
Route::get('settings', [SettingController::class, 'show']);

Route::post('bookings', [BookingController::class, 'store'])->middleware('throttle:5,1');
Route::post('contacts', [ContactController::class, 'store'])->middleware('throttle:5,1');
Route::post('payments/cashup/create', [CashupPaymentController::class, 'create'])->middleware('throttle:5,1');
Route::get('payments/cashup/callback', [CashupPaymentController::class, 'callback'])->middleware('throttle:30,1');
Route::get('payments/cashup/status/{booking}', [CashupPaymentController::class, 'status'])->middleware('throttle:30,1');
Route::get('payments/cashup/status-by-order/{orderId}', [CashupPaymentController::class, 'statusByOrder'])->middleware('throttle:30,1');

// ---- Admin ----
Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::get('customer/login', fn () => redirect()->away(rtrim(config('app.frontend_url'), '/').'/login'));
Route::post('customer/login', [CustomerAuthController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->prefix('customer')->group(function () {
    Route::get('auth/me', [CustomerAuthController::class, 'me']);
    Route::post('auth/logout', [CustomerAuthController::class, 'logout']);
    Route::get('bookings', [CustomerAuthController::class, 'bookings']);
});

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('auth/me', [AuthController::class, 'me']);
    Route::post('auth/logout', [AuthController::class, 'logout']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::post('upload', [UploadController::class, 'store']);

    Route::get('tours', [TourController::class, 'adminIndex']);
    Route::post('tours', [TourController::class, 'store']);
    Route::get('tours/{tour}', [TourController::class, 'adminShow']);
    Route::put('tours/{tour}', [TourController::class, 'update']);
    Route::delete('tours/{tour}', [TourController::class, 'destroy']);

    Route::get('destinations', [DestinationController::class, 'adminIndex']);
    Route::post('destinations', [DestinationController::class, 'store']);
    Route::get('destinations/{destination}', [DestinationController::class, 'adminShow']);
    Route::put('destinations/{destination}', [DestinationController::class, 'update']);
    Route::delete('destinations/{destination}', [DestinationController::class, 'destroy']);

    Route::get('posts', [PostController::class, 'adminIndex']);
    Route::post('posts', [PostController::class, 'store']);
    Route::get('posts/{post}', [PostController::class, 'adminShow']);
    Route::put('posts/{post}', [PostController::class, 'update']);
    Route::delete('posts/{post}', [PostController::class, 'destroy']);

    Route::get('testimonials', [TestimonialController::class, 'index']);
    Route::post('testimonials', [TestimonialController::class, 'store']);
    Route::put('testimonials/{testimonial}', [TestimonialController::class, 'update']);
    Route::delete('testimonials/{testimonial}', [TestimonialController::class, 'destroy']);

    Route::get('bookings', [BookingController::class, 'index']);
    Route::get('bookings/export', [BookingController::class, 'export']);
    Route::get('bookings/{booking}', [BookingController::class, 'show']);
    Route::put('bookings/{booking}', [BookingController::class, 'update']);
    Route::delete('bookings/{booking}', [BookingController::class, 'destroy']);

    Route::get('contacts', [ContactController::class, 'index']);
    Route::put('contacts/{contact}', [ContactController::class, 'update']);
    Route::delete('contacts/{contact}', [ContactController::class, 'destroy']);

    Route::get('about', [AboutController::class, 'adminShow']);
    Route::put('about', [AboutController::class, 'update']);

    Route::get('settings', [SettingController::class, 'adminShow']);
    Route::put('settings', [SettingController::class, 'update']);
});
