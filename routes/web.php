<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DatabaseManagementController;
use App\Http\Controllers\InventoryIssueController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/welcome', function () {
    return redirect()->route('login');
});

Route::middleware(['guest'])->group(function () {
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'registerSave'])->name('register.save');

    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'loginAction'])->name('login.action');

    // Password Reset Routes
    Route::get('forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products Routes (PC Parts) - Inventory & Superadmin
    Route::controller(ProductController::class)->prefix('products')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('products');
        Route::get('create', 'create')->name('products.create');
        Route::post('store', 'store')->name('products.store');
        Route::get('show/{id}', 'show')->name('products.show');
        Route::get('edit/{id}', 'edit')->name('products.edit');
        Route::put('edit/{id}', 'update')->name('products.update');
        Route::delete('destroy/{id}', 'destroy')->name('products.destroy');
        Route::get('low-stock', 'lowStock')->name('products.low-stock');
        Route::get('archived', 'archived')->name('products.archived');
        Route::post('restore/{id}', 'restore')->name('products.restore');
        Route::delete('permanent-delete/{id}', 'permanentDelete')->name('products.permanent-delete');
    });

    // Suppliers Routes - Inventory & Superadmin
    Route::controller(SupplierController::class)->prefix('suppliers')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('suppliers');
        Route::get('create', 'create')->name('suppliers.create');
        Route::post('store', 'store')->name('suppliers.store');
        Route::get('show/{id}', 'show')->name('suppliers.show');
        Route::get('edit/{id}', 'edit')->name('suppliers.edit');
        Route::put('edit/{id}', 'update')->name('suppliers.update');
        Route::delete('destroy/{id}', 'destroy')->name('suppliers.destroy');
        Route::get('archived', 'archived')->name('suppliers.archived');
        Route::post('restore/{id}', 'restore')->name('suppliers.restore');
        Route::delete('permanent-delete/{id}', 'permanentDelete')->name('suppliers.permanent-delete');
    });

    // User Management Routes (Superadmin & Admin)
    Route::controller(UserController::class)->prefix('users')->middleware('admin')->group(function () {
        Route::get('', 'index')->name('users.index');
        Route::get('create', 'create')->name('users.create');
        Route::post('', 'store')->name('users.store');
        Route::get('{id}', 'show')->name('users.show');
        Route::get('{id}/edit', 'edit')->name('users.edit');
        Route::put('{id}', 'update')->name('users.update');
        Route::post('{id}/approve', 'approve')->name('users.approve');
        Route::delete('{id}', 'destroy')->name('users.destroy');
        Route::get('archived/list', 'archived')->name('users.archived');
        Route::post('restore/{id}', 'restore')->name('users.restore');
        Route::delete('permanent-delete/{id}', 'permanentDelete')->name('users.permanent-delete');
    });

    // Stock In Routes - Inventory & Superadmin
    Route::controller(StockInController::class)->prefix('stock-in')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('stock-in.index');
        Route::get('create', 'create')->name('stock-in.create');
        Route::post('', 'store')->name('stock-in.store');
        Route::get('{id}', 'show')->name('stock-in.show');
        Route::delete('{id}', 'destroy')->name('stock-in.destroy');
    });

    // Stock Out (Inventory Issue) Routes - Inventory & Superadmin
    Route::controller(InventoryIssueController::class)->prefix('inventory-issues')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('inventory-issues');
        Route::get('create', 'create')->name('inventory-issues.create');
        Route::post('store', 'store')->name('inventory-issues.store');
        Route::get('show/{id}', 'show')->name('inventory-issues.show');
        Route::get('/inventory-issues', [InventoryIssueController::class, 'index'])->name('inventory-issues.index');
    });

    // Stock Out Orders (Purchase Order style) - Inventory & Superadmin
    Route::controller(StockOutOrderController::class)->prefix('stock-out-orders')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('stock-out-orders.index');
        Route::get('create', 'create')->name('stock-out-orders.create');
        Route::post('', 'store')->name('stock-out-orders.store');
        Route::get('{id}', 'show')->name('stock-out-orders.show');
        Route::patch('{stockOutOrder}/status', 'updateStatus')->name('stock-out-orders.update-status');
        Route::delete('{stockOutOrder}', 'destroy')->name('stock-out-orders.destroy');
    });

    // Categories Routes - Inventory & Superadmin
    Route::controller(CategoryController::class)->prefix('categories')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('categories.index');
        Route::get('archived', 'archived')->name('categories.archived');
        Route::post('restore/{id}', 'restore')->name('categories.restore');
        Route::delete('permanent-delete/{id}', 'permanentDelete')->name('categories.permanent-delete');
        Route::get('create', 'create')->name('categories.create');
        Route::post('', 'store')->name('categories.store');
        Route::get('{id}', 'show')->name('categories.show');
        Route::get('{id}/edit', 'edit')->name('categories.edit');
        Route::put('{id}', 'update')->name('categories.update');
        Route::delete('{id}', 'destroy')->name('categories.destroy');
    });

    // Stock Adjustments Routes - Inventory & Superadmin
    Route::controller(StockAdjustmentController::class)->prefix('stock-adjustments')->middleware('inventory')->group(function () {
        Route::get('', 'index')->name('stock-adjustments.index');
        Route::get('create', 'create')->name('stock-adjustments.create');
        Route::post('', 'store')->name('stock-adjustments.store');
        Route::get('{id}', 'show')->name('stock-adjustments.show');
        Route::delete('{id}', 'destroy')->name('stock-adjustments.destroy');
    });

    // Audit Logs Routes - Superadmin & Admin
    Route::controller(AuditLogController::class)->prefix('audit-logs')->middleware('admin')->group(function () {
        Route::get('', 'index')->name('audit-logs.index');
        Route::get('{id}', 'show')->name('audit-logs.show');
    });

    // Inventory Reports Routes - Inventory & Superadmin
    Route::prefix('inventory-reports')->middleware('inventory')->group(function () {
        Route::get('', [InventoryReportController::class, 'index'])->name('inventory-reports.index');
        Route::get('valuation', [InventoryReportController::class, 'valuation'])->name('inventory-reports.valuation');
        Route::get('movement', [InventoryReportController::class, 'movement'])->name('inventory-reports.movement');
        Route::get('low-stock', [InventoryReportController::class, 'lowStock'])->name('inventory-reports.low-stock');
        Route::get('category', [InventoryReportController::class, 'categoryReport'])->name('inventory-reports.category');
        Route::get('export/{type}', [InventoryReportController::class, 'export'])->name('inventory-reports.export');
    });

    // Custom Reports Routes - Superadmin & Admin
    Route::prefix('reports')->middleware('admin')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/{report}/download', [ReportController::class, 'downloadPdf'])->name('reports.download');
    });

    // Database Management Routes (Superadmin & Security)
    Route::prefix('database')->middleware('role:superadmin,security')->group(function () {
        Route::get('/', [DatabaseManagementController::class, 'index'])->name('database.index');
        Route::get('/backups', [DatabaseManagementController::class, 'backups'])->name('database.backups');
        Route::post('/backups', [DatabaseManagementController::class, 'createBackup'])->name('database.backup.create');
        Route::get('/backups/{id}/download', [DatabaseManagementController::class, 'downloadBackup'])->name('database.backup.download');
        Route::delete('/backups/{id}', [DatabaseManagementController::class, 'deleteBackup'])->name('database.backup.delete');
        Route::get('/backups/{id}/restore', [DatabaseManagementController::class, 'showRestore'])->name('database.backup.restore.show');
        Route::post('/backups/{id}/restore', [DatabaseManagementController::class, 'restoreBackup'])->name('database.backup.restore');
        Route::post('/optimize', [DatabaseManagementController::class, 'optimize'])->name('database.optimize');
        Route::get('/tables/{table}', [DatabaseManagementController::class, 'showTable'])->name('database.table.show');
        Route::get('/tables/{table}/export', [DatabaseManagementController::class, 'exportTable'])->name('database.table.export');
    });

    // Security Management Routes (Superadmin & Security)
    Route::prefix('security')->middleware('role:superadmin,security')->group(function () {
        Route::get('/', [SecurityController::class, 'index'])->name('security.index');
        Route::get('/login-history', [SecurityController::class, 'loginHistory'])->name('security.login-history');
        Route::get('/settings', [SecurityController::class, 'settings'])->name('security.settings');
        Route::put('/settings', [SecurityController::class, 'updateSettings'])->name('security.settings.update');
        Route::get('/active-sessions', [SecurityController::class, 'activeSessions'])->name('security.active-sessions');
        Route::delete('/sessions/{session}', [SecurityController::class, 'terminateSession'])->name('security.session.terminate');
        Route::post('/users/{user}/unlock', [SecurityController::class, 'unlockUser'])->name('security.user.unlock');
        Route::post('/users/{user}/toggle-status', [SecurityController::class, 'toggleUserStatus'])->name('security.user.toggle-status');
        Route::post('/users/{user}/force-password-change', [SecurityController::class, 'forcePasswordChange'])->name('security.user.force-password-change');
        Route::get('/export-logs', [SecurityController::class, 'exportLogs'])->name('security.export-logs');
    });

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [App\Http\Controllers\AuthController::class, 'updatePassword'])->name('profile.password');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/suppliers/for-product', [App\Http\Controllers\SupplierController::class, 'suppliersForProduct'])->name('suppliers.for-product');
