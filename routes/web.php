<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\BalanceController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\TicketController as ClientTicketController;
use App\Http\Controllers\Client\ChatController as ClientChatController;

/*
|--------------------------------------------------------------------------
| Admin Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware('throttle:5,1');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (protected by admin middleware)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Search API
    Route::get('/search', function (\Illuminate\Http\Request $request) {
        $q = $request->get('q', '');
        $users = \App\Models\User::where('role', 'client')
            ->where(fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%"))
            ->take(5)->get(['id', 'name', 'phone', 'balance']);
        return response()->json(['users' => $users]);
    })->name('search');

    // Users
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}', [UserController::class, 'profile'])->name('users.profile');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/add-balance', [UserController::class, 'addBalance'])->name('users.add-balance');
    Route::post('/users/{id}/withdraw-balance', [UserController::class, 'withdrawBalance'])->name('users.withdraw-balance');
    Route::put('/users/{id}/settings', [UserController::class, 'updateSettings'])->name('users.update-settings');

    // Packages
    Route::get('/packages', [PackageController::class, 'index'])->name('packages');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{id}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}', [PackageController::class, 'destroy'])->name('packages.destroy');

    // Cards Store
    Route::get('/cards-store', [CardController::class, 'index'])->name('cards-store');
    Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
    Route::post('/cards/bulk', [CardController::class, 'bulkStore'])->name('cards.bulk-store');
    Route::post('/cards/{id}/sell', [CardController::class, 'sell'])->name('cards.sell');
    Route::delete('/cards/{id}', [CardController::class, 'destroy'])->name('cards.destroy');

    // Active Cards
    Route::get('/active-cards', [CardController::class, 'activeCards'])->name('active-cards');

    // Shipping (Recharge Requests)
    Route::get('/shipping', [ShippingController::class, 'index'])->name('shipping');
    Route::post('/shipping/{id}/approve', [ShippingController::class, 'approve'])->name('shipping.approve');
    Route::post('/shipping/{id}/reject', [ShippingController::class, 'reject'])->name('shipping.reject');

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');

    // Sales Reports
    Route::get('/sales-reports', [SalesReportController::class, 'index'])->name('sales-reports');

    // Support (Tickets)
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::post('/support/{id}/status', [SupportController::class, 'updateStatus'])->name('support.update-status');

    // Balances
    Route::get('/balances', [BalanceController::class, 'index'])->name('balances');

    // Profile & Password
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'changePassword'])->name('password.update');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Dealer
    Route::get('/dealer', function() {
        $url = \App\Models\Setting::get('dealer_url', 'https://example.com');
        return view('admin.dealer', compact('url'));
    })->name('dealer');

    // Support ticket detail & reply
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');

    // Admin Chat
    Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');
    Route::get('/chat/poll', [AdminChatController::class, 'poll'])->name('chat.poll');
    Route::get('/chat/{clientId}', [AdminChatController::class, 'conversation'])->name('chat.conversation');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('chat.send');
});

/*
|--------------------------------------------------------------------------
| Client Auth Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [ClientAuthController::class, 'showLogin'])->name('client.login');
Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login.submit')->middleware('throttle:5,1');
Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

/*
|--------------------------------------------------------------------------
| Client Panel Routes (protected by client middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('client')->group(function () {
    Route::get('/', [ClientDashboardController::class, 'index'])->name('client.dashboard');

    // AJAX API endpoints (called via fetch from the single-page dashboard)
    Route::post('/buy', [ClientDashboardController::class, 'buyCard'])->name('client.buy');
    Route::post('/recharge', [ClientDashboardController::class, 'submitRecharge'])->name('client.recharge.store');
    Route::put('/settings', [ClientDashboardController::class, 'updateSettings'])->name('client.settings.update');

    // Card usage tracking
    Route::post('/cards/track-usage', [ClientDashboardController::class, 'trackCardUsage'])->name('client.cards.track-usage');

    // Support Tickets
    Route::get('/tickets', [ClientTicketController::class, 'index'])->name('client.tickets');
    Route::post('/tickets', [ClientTicketController::class, 'store'])->name('client.tickets.store');
    Route::post('/tickets/{id}/reply', [ClientTicketController::class, 'reply'])->name('client.tickets.reply');

    // Client Chat
    Route::get('/chat', [ClientChatController::class, 'index'])->name('client.chat');
    Route::post('/chat/send', [ClientChatController::class, 'send'])->name('client.chat.send');
    Route::get('/chat/poll', [ClientChatController::class, 'poll'])->name('client.chat.poll');
});

// Notifications routes (auth middleware)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead']);
});
