<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClosingStockController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseTitleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OpeningStockController;
use App\Http\Controllers\PeopleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

/* ── Authentication ── */
Route::get('/', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/* ── Protected ── */
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* ── Products ── */
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    /* ── Opening Stock ── */
    Route::get('/opening-stock', [OpeningStockController::class, 'index'])->name('opening_stock.index');
    Route::post('/opening-stock', [OpeningStockController::class, 'store'])->name('opening_stock.store');
    Route::delete('/opening-stock/{openingStock}', [OpeningStockController::class, 'destroy'])->name('opening_stock.destroy');

    /* ── Closing Stock ── */
    Route::get('/closing-stock', [ClosingStockController::class, 'index'])->name('closing_stock.index');
    Route::post('/closing-stock', [ClosingStockController::class, 'store'])->name('closing_stock.store');
    Route::delete('/closing-stock/{closingStock}', [ClosingStockController::class, 'destroy'])->name('closing_stock.destroy');

    /* ── Sales ── */
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');

    /* ── Expenses ── */
    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    /* ── Expense Titles ── */
    Route::post('/expense-titles', [ExpenseTitleController::class, 'store'])->name('expense_titles.store');
    Route::delete('/expense-titles/{expenseTitle}', [ExpenseTitleController::class, 'destroy'])->name('expense_titles.destroy');

    /* ── Purchases ── */
    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
    Route::post('/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
    Route::delete('/purchases/{purchase}', [PurchaseController::class, 'destroy'])->name('purchases.destroy');

    /* ── Suppliers ── */
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

    /* ── Reports ── */
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    /* ── People ── */
    Route::get('/people', [PeopleController::class, 'index'])->name('people.index');
    Route::post('/people', [PeopleController::class, 'store'])->name('people.store');
    Route::delete('/people/{person}', [PeopleController::class, 'destroy'])->name('people.destroy');

    /* ── Notifications ── */
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

});
