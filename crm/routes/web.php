<?php

use App\Http\Controllers\ApiSettingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\MetaWebhookController;
use App\Http\Controllers\OmnichannelController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\QuickReplyController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TelegramSettingController;
use App\Http\Controllers\TelegramWebhookController;
use App\Http\Controllers\SalesFunnelController;
use App\Http\Controllers\CustomerActivityController;

Route::match(['get', 'post'], '/webhooks/meta', [MetaWebhookController::class, 'handle'])->name('webhooks.meta');
Route::post('/webhooks/telegram/{token}', [TelegramWebhookController::class, 'handle'])->name('webhooks.telegram');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile/update', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::resource('tasks', \App\Http\Controllers\TaskController::class);
    Route::resource('knowledge-bases', \App\Http\Controllers\KnowledgeBaseController::class);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::post('/customers/{customer}/ajax-update', [CustomerController::class, 'updateAjax'])->name('customers.ajax.update');
    Route::post('/customers/{customer}/assign', [CustomerController::class, 'assignToMe'])->name('customers.assign');
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
    Route::post('/omnichannel/{conversation}/toggle-ai', [OmnichannelController::class, 'toggleAi'])->name('omnichannel.toggle-ai');

    // Satış Hunisi
    Route::get('/sales-funnel', [SalesFunnelController::class, 'index'])->name('sales-funnel.index');

    // Raporlar (Reports Modülü)
    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/', [\App\Http\Controllers\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'sales'])->name('sales');
        Route::get('/funnel', [\App\Http\Controllers\ReportController::class, 'funnel'])->name('funnel');
        Route::get('/activities', [\App\Http\Controllers\ReportController::class, 'activities'])->name('activities');
        Route::get('/time', [\App\Http\Controllers\ReportController::class, 'time'])->name('time');
        Route::get('/revenue', [\App\Http\Controllers\ReportController::class, 'revenue'])->name('revenue');
        Route::get('/marketing', [\App\Http\Controllers\ReportController::class, 'marketing'])->name('marketing');
        Route::get('/ai-insights', [\App\Http\Controllers\ReportController::class, 'aiInsights'])->name('ai');
    });

    // Hazır Mesaj Ayarları
    Route::get('/settings/quick-replies', [QuickReplyController::class, 'index'])->name('settings.quick-replies.index');
    Route::get('/settings/quick-replies/data', [QuickReplyController::class, 'getData'])->name('settings.quick-replies.data');
    Route::post('/settings/quick-replies', [QuickReplyController::class, 'store'])->name('settings.quick-replies.store');
    Route::get('/settings/quick-replies/{quickReply}', [QuickReplyController::class, 'show'])->name('settings.quick-replies.show');
    Route::delete('/settings/quick-replies/{quickReply}', [QuickReplyController::class, 'destroy'])->name('settings.quick-replies.destroy');

    // Kullanıcı Yönetimi
    Route::get('/settings/users', [UserController::class, 'index'])->name('settings.users.index');
    Route::get('/settings/users/data', [UserController::class, 'getData'])->name('settings.users.data');
    Route::post('/settings/users', [UserController::class, 'store'])->name('settings.users.store');
    Route::get('/settings/users/{user}', [UserController::class, 'show'])->name('settings.users.show');
    Route::delete('/settings/users/{user}', [UserController::class, 'destroy'])->name('settings.users.destroy');

    // Ürünler ve Hizmetler Modülü
    Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
    Route::get('/products/data', [\App\Http\Controllers\ProductController::class, 'getData'])->name('products.data');
    Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
    Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/settings/api', [ApiSettingController::class, 'index'])->name('settings.api.index');
    Route::post('/settings/api', [ApiSettingController::class, 'store'])->name('settings.api.store');

    // Yapay Zeka Ayarları (Ayrı Sayfa)
    Route::get('/settings/ai', [\App\Http\Controllers\AiSettingController::class, 'index'])->name('settings.ai.index');
    Route::post('/settings/ai', [\App\Http\Controllers\AiSettingController::class, 'store'])->name('settings.ai.store');

    // Telegram API Ayarları
    Route::get('/settings/telegram', [TelegramSettingController::class, 'index'])->name('settings.telegram.index');
    Route::post('/settings/telegram', [TelegramSettingController::class, 'store'])->name('settings.telegram.store');
});
