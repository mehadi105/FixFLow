<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth (preview UI — replace with Breeze later)
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

/*
|--------------------------------------------------------------------------
| FixFlow Preview Routes (frontend only — no controllers)
|--------------------------------------------------------------------------
| These closure routes let you preview Blade pages before backend logic exists.
| Later, replace these with proper controllers and auth middleware.
*/

// Dashboards
Route::view('/dashboard/customer', 'dashboard.customer', ['role' => 'customer'])->name('dashboard.customer');
Route::view('/dashboard/admin', 'dashboard.admin', ['role' => 'admin'])->name('dashboard.admin');
Route::view('/dashboard/technician', 'dashboard.technician', ['role' => 'technician'])->name('dashboard.technician');

// Default dashboard redirect for preview
Route::redirect('/dashboard', '/dashboard/customer');

// Repair Requests
Route::view('/repair-requests', 'repair-requests.index', ['role' => 'customer'])->name('repair-requests.index');
Route::view('/repair-requests/create', 'repair-requests.create', ['role' => 'customer'])->name('repair-requests.create');
Route::view('/repair-requests/{id}', 'repair-requests.show', ['role' => 'customer'])->name('repair-requests.show');

// Invoices
Route::view('/invoices', 'invoices.index', ['role' => 'customer'])->name('invoices.index');
Route::view('/invoices/{id}', 'invoices.show', ['role' => 'customer'])->name('invoices.show');

// Warranty
Route::view('/warranties', 'warranties.index', ['role' => 'customer'])->name('warranties.index');

// Reports (admin)
Route::view('/reports', 'reports.index', ['role' => 'admin'])->name('reports.index');

// Admin preview variants (same pages, admin sidebar)
Route::prefix('admin')->group(function () {
    Route::view('/repair-requests', 'repair-requests.index', ['role' => 'admin']);
    Route::view('/invoices', 'invoices.index', ['role' => 'admin']);
});
