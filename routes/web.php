<?php

use App\Http\Controllers\Admin\AboutController as AdminAboutController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\TourController as AdminTourController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/admin'));

Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('admin.login');
    });

    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin');

        Route::resource('tours', AdminTourController::class)->names('admin.tours');
        Route::resource('destinations', AdminDestinationController::class)->names('admin.destinations');
        Route::resource('posts', AdminPostController::class)->names('admin.posts');
        Route::resource('testimonials', AdminTestimonialController::class)->names('admin.testimonials');

        Route::get('bookings/export', [AdminBookingController::class, 'export'])->name('admin.bookings.export');
        Route::post('bookings/{booking}/sync-payment', [AdminBookingController::class, 'syncPayment'])->name('admin.bookings.sync-payment');
        Route::resource('bookings', AdminBookingController::class)->names('admin.bookings')->except(['create', 'store', 'edit']);
        Route::resource('contacts', AdminContactController::class)->names('admin.contacts')->except(['create', 'store', 'edit']);

        Route::get('about', [AdminAboutController::class, 'edit'])->name('admin.about');
        Route::put('about', [AdminAboutController::class, 'update']);
        Route::get('settings', [AdminSettingController::class, 'edit'])->name('admin.settings');
        Route::put('settings', [AdminSettingController::class, 'update']);
    });
});
