<?php

use App\Http\Controllers\Api\V1\Admin\AirLineController;
use App\Http\Controllers\Api\V1\Admin\BankController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\FaqController;
use App\Http\Controllers\Api\V1\Admin\HotelController;
use App\Http\Controllers\Api\V1\Admin\ManasikController;
use App\Http\Controllers\Api\V1\Admin\PaymentConfirmationController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\RouteController;
use App\Http\Controllers\Api\V1\Admin\SettingController;
use App\Http\Controllers\Api\V1\Admin\SliderController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PackageController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Public Auth ────────────────────────────────────────────
    Route::post('/register', [AuthController::class, 'register'])->name('api.register');
    Route::post('/login', [AuthController::class, 'login'])->name('api.login');

    // ── Public Package Listing ─────────────────────────────────
    Route::get('/packages', [PackageController::class, 'index'])->name('api.packages.index');
    Route::get('/packages/{package}', [PackageController::class, 'show'])->name('api.packages.show');

    // ── Public Master Data ─────────────────────────────────────
    Route::get('/airlines', [AirLineController::class, 'index'])->name('api.airlines.index');
    Route::get('/hotels', [HotelController::class, 'index'])->name('api.hotels.index');
    Route::get('/routes', [RouteController::class, 'index'])->name('api.routes.index');
    Route::get('/banks', [BankController::class, 'index'])->name('api.banks.index');
    Route::get('/faqs', [FaqController::class, 'publicIndex'])->name('api.faqs.index');
    Route::get('/manasiks', [ManasikController::class, 'publicIndex'])->name('api.manasiks.index');
    Route::get('/sliders', [SliderController::class, 'index'])->name('api.sliders.index');
    Route::get('/settings', [SettingController::class, 'publicIndex'])->name('api.settings.index');

    // ── Authenticated ──────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.logout');
        Route::get('/me', [AuthController::class, 'me'])->name('api.me');
        Route::put('/me', [UserController::class, 'updateProfile'])->name('api.me.update');

        // Customer: Orders
        Route::post('/orders', [OrderController::class, 'store'])->name('api.orders.store');
        Route::get('/orders', [OrderController::class, 'myOrders'])->name('api.orders.my');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('api.orders.show');
        Route::post('/orders/{order}/payment', [PaymentConfirmationController::class, 'store'])->name('api.orders.payment.store');

        // Package Images (merchant & admin)
        Route::middleware('role:merchants|admin')->group(function () {
            Route::post('/packages/{package}/images', [PackageController::class, 'uploadImage'])->name('api.packages.images.store');
            Route::delete('/packages/images/{image}', [PackageController::class, 'deleteImage'])->name('api.packages.images.destroy');
            Route::put('/packages/images/{image}/primary', [PackageController::class, 'setPrimaryImage'])->name('api.packages.images.primary');
        });

        // ── Merchant Routes ───────────────────────────────────────
        Route::prefix('merchant')->middleware('role:merchants|admin')->group(function () {
            Route::get('/packages', [PackageController::class, 'merchantIndex'])->name('api.merchant.packages.index');
            Route::get('/packages/{package}', [PackageController::class, 'show'])->name('api.merchant.packages.show');
            Route::post('/packages', [PackageController::class, 'store'])->name('api.merchant.packages.store');
            Route::put('/packages/{package}', [PackageController::class, 'update'])->name('api.merchant.packages.update');
            Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('api.merchant.packages.destroy');
        });

        // ── Admin Routes ──────────────────────────────────────────
        Route::prefix('admin')->middleware('role:admin')->group(function () {
            Route::get('/dashboard', [DashboardController::class, 'index'])->name('api.admin.dashboard');

            // Users
            Route::get('/users', [UserController::class, 'index'])->name('api.admin.users.index');
            Route::post('/users', [UserController::class, 'store'])->name('api.admin.users.store');
            Route::get('/users/{user}', [UserController::class, 'show'])->name('api.admin.users.show');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('api.admin.users.update');
            Route::put('/users/{user}/status', [UserController::class, 'updateStatus'])->name('api.admin.users.status');
            Route::put('/users/{user}/role', [UserController::class, 'updateRole'])->name('api.admin.users.role');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('api.admin.users.destroy');

            // Master Data
            Route::apiResource('banks', BankController::class);
            Route::apiResource('airlines', AirLineController::class);
            Route::apiResource('hotels', HotelController::class);
            Route::apiResource('routes', RouteController::class);

            // Packages (admin can see all, create, edit, approve, delete)
            Route::get('/packages', [PackageController::class, 'adminIndex'])->name('api.admin.packages.index');
            Route::get('/packages/{package}', [PackageController::class, 'show'])->name('api.admin.packages.show');
            Route::post('/packages', [PackageController::class, 'store'])->name('api.admin.packages.store');
            Route::put('/packages/{package}', [PackageController::class, 'update'])->name('api.admin.packages.update');
            Route::put('/packages/{package}/status', [PackageController::class, 'updateStatus'])->name('api.admin.packages.status');
            Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('api.admin.packages.destroy');

            // Orders
            Route::get('/orders', [OrderController::class, 'adminIndex'])->name('api.admin.orders.index');
            Route::get('/orders/{order}', [OrderController::class, 'adminShow'])->name('api.admin.orders.show');

            // Participants / Jamaah
            Route::get('/participants', [OrderController::class, 'participants'])->name('api.admin.participants.index');
            Route::get('/participants/export', [ReportController::class, 'exportParticipants'])->name('api.admin.participants.export');

            // Payment Confirmations
            Route::get('/payments', [PaymentConfirmationController::class, 'index'])->name('api.admin.payments.index');
            Route::put('/payments/{payment}/approve', [PaymentConfirmationController::class, 'approve'])->name('api.admin.payments.approve');

            // Reports
            Route::get('/reports/sales', [ReportController::class, 'sales'])->name('api.admin.reports.sales');
            Route::get('/reports/agents', [ReportController::class, 'agents'])->name('api.admin.reports.agents');

            // FAQ & Manasik
            Route::apiResource('faq-groups', FaqController::class);
            Route::post('/faq-groups/{faqGroup}/faqs', [FaqController::class, 'storeFaq'])->name('api.admin.faqs.store');
            Route::put('/faqs/{faq}', [FaqController::class, 'updateFaq'])->name('api.admin.faqs.update');
            Route::delete('/faqs/{faq}', [FaqController::class, 'destroyFaq'])->name('api.admin.faqs.destroy');

            Route::apiResource('manasik-groups', ManasikController::class);
            Route::post('/manasik-groups/{manasikGroup}/manasiks', [ManasikController::class, 'storeManasik'])->name('api.admin.manasiks.store');
            Route::put('/manasiks/{manasik}', [ManasikController::class, 'updateManasik'])->name('api.admin.manasiks.update');
            Route::delete('/manasiks/{manasik}', [ManasikController::class, 'destroyManasik'])->name('api.admin.manasiks.destroy');

            // Sliders
            Route::apiResource('sliders', SliderController::class);
            Route::put('/sliders/reorder', [SliderController::class, 'reorder'])->name('api.admin.sliders.reorder');

            // Settings
            Route::get('/settings', [SettingController::class, 'index'])->name('api.admin.settings.index');
            Route::put('/settings', [SettingController::class, 'update'])->name('api.admin.settings.update');
        });
    });
});
