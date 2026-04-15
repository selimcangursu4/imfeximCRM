<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\MetaWebhookController;
use App\Http\Controllers\OmnichannelController;
use App\Http\Controllers\SettingController;

Route::match(['get', 'post'], '/webhooks/meta', [MetaWebhookController::class, 'handle'])->name('webhooks.meta');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/ajax-update', [CustomerController::class, 'updateAjax'])->name('customers.ajax.update');
    Route::get('/customers/{customer}/chat', [CustomerController::class, 'startChat'])->name('customers.chat');
    Route::get('/customers/{customer}', [CustomerProfileController::class, 'show'])->name('customers.show');

    // Müşteri Aktiviteleri
    Route::post('/customers/{customer}/activities', [CustomerActivityController::class, 'store'])->name('customers.activities.store');
    Route::delete('/activities/{activity}', [CustomerActivityController::class, 'destroy'])->name('activities.destroy');
    Route::get('/omnichannel', [OmnichannelController::class, 'index'])->name('omnichannel.index');
    Route::get('/omnichannel/sync-sidebar', [OmnichannelController::class, 'syncSidebar'])->name('omnichannel.sidebar.sync');
    Route::get('/omnichannel/customer/{customer}', [OmnichannelController::class, 'getCustomer'])->name('omnichannel.customer.get');
    Route::post('/omnichannel/customer/{customer}', [OmnichannelController::class, 'updateCustomer'])->name('omnichannel.customer.update');
    Route::get('/omnichannel/{conversation}', [OmnichannelController::class, 'show'])->name('omnichannel.show');
    Route::get('/omnichannel/{conversation}/messages', [OmnichannelController::class, 'syncMessages'])->name('omnichannel.messages.sync');
    Route::post('/omnichannel/{conversation}/message', [OmnichannelController::class, 'storeMessage'])->name('omnichannel.message.store');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/api', [SettingController::class, 'store'])->name('settings.api.store');
});

