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
use App\Http\Controllers\SuperAdmin\AuthController as SuperAdminAuthController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\PlanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TenantRegistrationController;
use App\Http\Controllers\SuperAdmin\HomepageController as SuperAdminHomepageController;

/*
|--------------------------------------------------------------------------
| Super Admin Routes  —  /superadmin prefix, NO tenant middleware
|--------------------------------------------------------------------------
*/
Route::prefix('superadmin')->name('superadmin.')->group(function () {

    Route::get('/login',  [SuperAdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [SuperAdminAuthController::class, 'login'])->name('login.submit')->middleware('throttle:10,1');
    Route::post('/logout',[SuperAdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('super_admin')->group(function () {
        Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/tenants',            [TenantController::class, 'index'])->name('tenants');
        Route::get('/tenants/create',     [TenantController::class, 'create'])->name('tenants.create');
        Route::post('/tenants',           [TenantController::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{id}/edit',  [TenantController::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{id}',       [TenantController::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{id}',    [TenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('/tenants/{id}/toggle', [TenantController::class, 'toggleStatus'])->name('tenants.toggle');
        Route::post('/tenants/{id}/approve',[TenantController::class, 'approve'])->name('tenants.approve');
        Route::post('/tenants/{id}/reject', [TenantController::class, 'reject'])->name('tenants.reject');

        Route::get('/plans',            [PlanController::class, 'index'])->name('plans');
        Route::get('/plans/create',     [PlanController::class, 'create'])->name('plans.create');
        Route::post('/plans',           [PlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{id}/edit',  [PlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{id}',       [PlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{id}',    [PlanController::class, 'destroy'])->name('plans.destroy');

        // Homepage Management (platform-level, controlled by super admin)
        Route::get('/homepage',                        [SuperAdminHomepageController::class, 'index'])->name('homepage.index');
        Route::get('/homepage/plans',                  [SuperAdminHomepageController::class, 'plans'])->name('homepage.plans');
        Route::post('/homepage/plans',                 [SuperAdminHomepageController::class, 'storePlan'])->name('homepage.plans.store');
        Route::put('/homepage/plans/{plan}',           [SuperAdminHomepageController::class, 'updatePlan'])->name('homepage.plans.update');
        Route::delete('/homepage/plans/{plan}',        [SuperAdminHomepageController::class, 'destroyPlan'])->name('homepage.plans.destroy');
        Route::get('/homepage/{section}/edit',         [SuperAdminHomepageController::class, 'edit'])->name('homepage.edit');
        Route::put('/homepage/{section}',              [SuperAdminHomepageController::class, 'update'])->name('homepage.update');
        Route::post('/homepage/{section}/toggle',      [SuperAdminHomepageController::class, 'toggle'])->name('homepage.toggle');
        Route::post('/homepage/reorder',               [SuperAdminHomepageController::class, 'reorder'])->name('homepage.reorder');
        Route::delete('/homepage/{section}',           [SuperAdminHomepageController::class, 'destroy'])->name('homepage.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Tenant Routes  —  all other routes wrapped in ResolveTenant middleware
|--------------------------------------------------------------------------
*/
Route::middleware('tenant')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Admin Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/admin/login',   [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin/login',  [AdminAuthController::class, 'login'])->name('admin.login.submit')->middleware('throttle:5,1');
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Pending-approval page — accessible after login, before network is activated
    Route::get('/admin/pending', function () {
        return view('admin.pending');
    })->name('admin.pending')->middleware('auth');

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Routes (protected by network_admin middleware)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('network_admin')->group(function () {
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
        Route::get('/users',                        [UserController::class, 'index'])->name('users');
        Route::post('/users',                       [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}',                   [UserController::class, 'profile'])->name('users.profile');
        Route::post('/users/{id}/toggle-status',    [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::post('/users/{id}/add-balance',      [UserController::class, 'addBalance'])->name('users.add-balance');
        Route::post('/users/{id}/withdraw-balance', [UserController::class, 'withdrawBalance'])->name('users.withdraw-balance');
        Route::put('/users/{id}/settings',          [UserController::class, 'updateSettings'])->name('users.update-settings');

        // Packages
        Route::get('/packages',         [PackageController::class, 'index'])->name('packages');
        Route::post('/packages',        [PackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{id}',    [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{id}', [PackageController::class, 'destroy'])->name('packages.destroy');

        // Cards Store
        Route::get('/cards-store',         [CardController::class, 'index'])->name('cards-store');
        Route::post('/cards',              [CardController::class, 'store'])->name('cards.store');
        Route::post('/cards/bulk',         [CardController::class, 'bulkStore'])->name('cards.bulk-store');
        Route::post('/cards/{id}/sell',    [CardController::class, 'sell'])->name('cards.sell');
        Route::delete('/cards/{id}',       [CardController::class, 'destroy'])->name('cards.destroy');

        // Active Cards
        Route::get('/active-cards', [CardController::class, 'activeCards'])->name('active-cards');

        // Shipping (Recharge Requests)
        Route::get('/shipping',                     [ShippingController::class, 'index'])->name('shipping');
        Route::post('/shipping/{id}/approve',       [ShippingController::class, 'approve'])->name('shipping.approve');
        Route::post('/shipping/{id}/reject',        [ShippingController::class, 'reject'])->name('shipping.reject');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');

        // Sales Reports
        Route::get('/sales-reports', [SalesReportController::class, 'index'])->name('sales-reports');

        // Support (Tickets)
        Route::get('/support',              [SupportController::class, 'index'])->name('support');
        Route::post('/support/{id}/status', [SupportController::class, 'updateStatus'])->name('support.update-status');
        Route::get('/support/{id}',         [SupportController::class, 'show'])->name('support.show');
        Route::post('/support/{id}/reply',  [SupportController::class, 'reply'])->name('support.reply');

        // Balances
        Route::get('/balances', [BalanceController::class, 'index'])->name('balances');

        // Profile & Password
        Route::put('/profile',   [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password',  [ProfileController::class, 'changePassword'])->name('password.update');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        // Dealer
        Route::get('/dealer', function () {
            $url = \App\Models\Setting::get('dealer_url', 'https://example.com');
            return view('admin.dealer', compact('url'));
        })->name('dealer');

        // Admin Chat
        Route::get('/chat',            [AdminChatController::class, 'index'])->name('chat');
        Route::get('/chat/poll',       [AdminChatController::class, 'poll'])->name('chat.poll');
        Route::get('/chat/{clientId}', [AdminChatController::class, 'conversation'])->name('chat.conversation');
        Route::post('/chat/send',      [AdminChatController::class, 'send'])->name('chat.send');

    });

    /*
    |--------------------------------------------------------------------------
    | Client Auth Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/login',   [ClientAuthController::class, 'showLogin'])->name('client.login');
    Route::post('/login',  [ClientAuthController::class, 'login'])->name('client.login.submit')->middleware('throttle:5,1');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

    /*
    |--------------------------------------------------------------------------
    | Client Panel Routes (protected by client middleware)
    |--------------------------------------------------------------------------
    */
    Route::middleware('client')->group(function () {
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('client.dashboard');

        // AJAX API endpoints
        Route::post('/buy',                   [ClientDashboardController::class, 'buyCard'])->name('client.buy')->middleware('throttle:10,1');
        Route::post('/recharge',              [ClientDashboardController::class, 'submitRecharge'])->name('client.recharge.store');
        Route::put('/settings',               [ClientDashboardController::class, 'updateSettings'])->name('client.settings.update');
        Route::post('/cards/track-usage',     [ClientDashboardController::class, 'trackCardUsage'])->name('client.cards.track-usage');

        // Support Tickets
        Route::get('/tickets',               [ClientTicketController::class, 'index'])->name('client.tickets');
        Route::post('/tickets',              [ClientTicketController::class, 'store'])->name('client.tickets.store');
        Route::post('/tickets/{id}/reply',   [ClientTicketController::class, 'reply'])->name('client.tickets.reply');

        // Client Chat
        Route::get('/chat',        [ClientChatController::class, 'index'])->name('client.chat');
        Route::post('/chat/send',  [ClientChatController::class, 'send'])->name('client.chat.send');
        Route::get('/chat/poll',   [ClientChatController::class, 'poll'])->name('client.chat.poll');
    });

    /*
    |--------------------------------------------------------------------------
    | Notifications (any authenticated user in a tenant)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {
        Route::get('/notifications',              [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications');
        Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount']);
        Route::post('/notifications/mark-read',   [\App\Http\Controllers\NotificationController::class, 'markAllRead']);
    });
});

/*
|--------------------------------------------------------------------------
| Public Homepage — outside tenant middleware.
| HomeController handles subdomain validation internally.
| HomeController redirects authenticated users to their dashboard.
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Tenant Self-Registration  —  public, no auth, no tenant middleware
|--------------------------------------------------------------------------
*/
Route::get('/register',         [TenantRegistrationController::class, 'showForm'])->name('register');
Route::post('/register',        [TenantRegistrationController::class, 'submit'])->name('register.submit')->middleware('throttle:5,10');
Route::get('/register/pending', [TenantRegistrationController::class, 'pending'])->name('register.pending');
