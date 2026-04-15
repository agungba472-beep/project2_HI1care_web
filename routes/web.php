<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPasienController;
use App\Http\Controllers\Admin\AdminRefillController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminBroadcastController;
Route::get('/', function () {
    return view('welcome');
});


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class, 'reject'])->name('users.reject');

    Route::get('/pasien', [AdminPasienController::class, 'index'])->name('pasien.index');

    Route::get('/refill', [AdminRefillController::class, 'index'])->name('refill.index');

    Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('broadcast.index');
    Route::post('/broadcast', [AdminBroadcastController::class, 'send'])->name('broadcast.send');

    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');

});