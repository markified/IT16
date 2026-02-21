<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\InventoryIssueController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PurchaseOrderReceivingController;
use App\Http\Controllers\ReportViewController;
use App\Http\Controllers\ReportController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/welcome', function () {
    return view('login');
})->name('login');

Route::middleware(['guest'])->group(function () {
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'registerSave'])->name('register.save');

    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'loginAction'])->name('login.action');
});

Route::get('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('employee/dashboard', [DashboardController::class, 'employeeDashboard'])->name('employee.dashboard');
    // Products Routes
    Route::controller(ProductController::class)->prefix('products')->group(function () {
        Route::get('', 'index')->name('products');
        Route::get('create', 'create')->name('products.create');
        Route::post('store', 'store')->name('products.store');
        Route::get('show/{id}', 'show')->name('products.show');
        Route::get('edit/{id}', 'edit')->name('products.edit');
        Route::put('edit/{id}', 'update')->name('products.update');
        Route::delete('destroy/{id}', 'destroy')->name('products.destroy');
        Route::get('low-stock', 'lowStock')->name('products.low-stock');
    });

    // Suppliers Routes
    Route::controller(SupplierController::class)->prefix('suppliers')->group(function () {
        Route::get('', 'index')->name('suppliers');
        Route::get('create', 'create')->name('suppliers.create');
        Route::post('store', 'store')->name('suppliers.store');
        Route::get('show/{id}', 'show')->name('suppliers.show');
        Route::get('edit/{id}', 'edit')->name('suppliers.edit');
        Route::put('edit/{id}', 'update')->name('suppliers.update');
        Route::delete('destroy/{id}', 'destroy')->name('suppliers.destroy');
    });

    // Department Routes
    Route::controller(DepartmentController::class)->prefix('departments')->group(function () {
        Route::get('', 'index')->name('departments');
        Route::get('create', 'create')->name('departments.create');
        Route::post('store', 'store')->name('departments.store');
        Route::get('show/{id}', 'show')->name('departments.show');
        Route::get('edit/{id}', 'edit')->name('departments.edit');
        Route::put('edit/{id}', 'update')->name('departments.update');
        Route::delete('destroy/{id}', 'destroy')->name('departments.destroy');
    });

    // Employee Routes
    Route::controller(EmployeeController::class)->prefix('employees')->group(function () {
        Route::get('', 'index')->name('employees');  // Changed back to original 'employees' name
        Route::get('create', 'create')->name('employees.create');
        Route::post('store', 'store')->name('employees.store');
        Route::get('show/{id}', 'show')->name('employees.show');
        Route::get('edit/{id}', 'edit')->name('employees.edit');
        Route::put('edit/{id}', 'update')->name('employees.update');
        Route::delete('destroy/{id}', 'destroy')->name('employees.destroy');
    });

    // Purchase Order Routes
    Route::controller(PurchaseOrderController::class)->prefix('purchase-orders')->group(function () {
        Route::get('', 'index')->name('purchase-orders.index');
        Route::get('create', 'create')->name('purchase-orders.create');
        Route::post('', 'store')->name('purchase-orders.store');
        Route::get('{purchaseOrder}', 'show')->name('purchase-orders.show');
        Route::get('{purchaseOrder}/edit', 'edit')->name('purchase-orders.edit');
        Route::put('{purchaseOrder}', 'update')->name('purchase-orders.update');
        Route::delete('{purchaseOrder}', 'destroy')->name('purchase-orders.destroy');

        Route::get('generate/low-stock', 'generateForLowStock')->name('purchase-orders.generate-low-stock');
        Route::patch('{purchaseOrder}/status', 'updateStatus')->name('purchase-orders.update-status');
        Route::get('{purchaseOrder}/receive', 'showReceiveForm')->name('purchase-orders.receive-form');
        Route::post('{purchaseOrder}/receive', 'processReceive')->name('purchase-orders.process-receive');
    });

    // Purchase Order Receiving Routes
    Route::get('receivings', [PurchaseOrderReceivingController::class, 'index'])->name('receivings.index');
    Route::get('order-details/{orderDetail}/receivings/create', [PurchaseOrderReceivingController::class, 'create'])->name('receivings.create');
    Route::post('order-details/{orderDetail}/receivings', [PurchaseOrderReceivingController::class, 'store'])->name('receivings.store');
    Route::get('receivings/{receiving}', [PurchaseOrderReceivingController::class, 'show'])->name('receivings.show');
    Route::delete('receivings/{receiving}', [PurchaseOrderReceivingController::class, 'destroy'])->name('receivings.destroy');
    Route::get('receivings/{purchaseOrder}/receive', [PurchaseOrderController::class, 'showReceiveForm'])->name('receivings.receive-form');
    Route::get('stock-in', [PurchaseOrderReceivingController::class, 'stockInList'])->name('stock-in.index');
    // Inventory Issue Routes
    Route::controller(InventoryIssueController::class)->prefix('inventory-issues')->group(function () {
        Route::get('', 'index')->name('inventory-issues');
        Route::get('create', 'create')->name('inventory-issues.create');
        Route::post('store', 'store')->name('inventory-issues.store');
        Route::get('show/{id}', 'show')->name('inventory-issues.show');
        Route::get('/inventory-issues', [InventoryIssueController::class, 'index'])->name('inventory-issues.index');
    });



    // Reports Routes

    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/create', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/', [ReportController::class, 'store'])->name('reports.store');
        Route::get('/{report}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('reports.edit');
        Route::put('/{report}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    });

    Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile'])->name('profile');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/suppliers/for-product', [App\Http\Controllers\SupplierController::class, 'suppliersForProduct'])->name('suppliers.for-product');
