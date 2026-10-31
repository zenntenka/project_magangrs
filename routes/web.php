<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route Tiket Helpdesk Pelapor (Hanya: index, create, store, show)
    Route::resource('tickets', TicketController::class)->only(['index', 'create', 'store', 'show']);
});

// Route Group Khusus Panel Teknisi (Middleware: auth + role:teknisi)
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/', [\App\Http\Controllers\TeknisiController::class, 'dashboard'])->name('dashboard');
    Route::get('/tickets', [\App\Http\Controllers\TeknisiController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\TeknisiController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/take', [\App\Http\Controllers\TeknisiController::class, 'take'])->name('tickets.take');
    Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\TeknisiController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\TeknisiController::class, 'storeComment'])->name('tickets.comments.store');
});

// Route Group Khusus Panel Admin (Middleware: auth + role:admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Master Data (Unit, Kategori, Pengguna)
    Route::resource('units', \App\Http\Controllers\Admin\UnitController::class)->except(['show']);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

    // Kelola Tiket & Penugasan Teknisi
    Route::get('/tickets', [\App\Http\Controllers\Admin\TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/{ticket}', [\App\Http\Controllers\Admin\TicketController::class, 'show'])->name('tickets.show');
    Route::post('/tickets/{ticket}/assign', [\App\Http\Controllers\Admin\TicketController::class, 'assign'])->name('tickets.assign');
    Route::patch('/tickets/{ticket}/status', [\App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('tickets.status');
    Route::post('/tickets/{ticket}/comments', [\App\Http\Controllers\Admin\TicketController::class, 'storeComment'])->name('tickets.comments.store');

    // Modul Laporan Helpdesk IT & Ekspor Data
    Route::get('/laporan', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('laporan.excel');

    // Riwayat Masalah per Unit
    Route::get('/unit-riwayat', [\App\Http\Controllers\Admin\UnitHistoryController::class, 'index'])->name('unit-riwayat.index');
    Route::get('/unit-riwayat/{unit}', [\App\Http\Controllers\Admin\UnitHistoryController::class, 'show'])->name('unit-riwayat.show');
});

require __DIR__.'/auth.php';

