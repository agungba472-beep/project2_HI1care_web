<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPasienController;
use App\Http\Controllers\Admin\AdminRefillController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminBroadcastController;
use App\Http\Controllers\Admin\AdminEdukasiController;
use App\Http\Controllers\Auth\WebAuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Autentikasi Web
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Rute Admin (Dilindungi Auth)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // (Opsional tapi disarankan) Anda bisa membuat Middleware 'Role:admin' nanti agar rute ini benar-benar terkunci
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::post('/users/store-nakes', [AdminUserController::class, 'storeNakes'])->name('users.storeNakes');
    Route::post('/master/store', [AdminUserController::class, 'storeMaster'])->name('master.store');
    Route::delete('/master/{id}', [AdminUserController::class, 'destroyMaster'])->name('master.destroy');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/pasien', [AdminPasienController::class, 'index'])->name('pasien.index');
    Route::get('/refill', [AdminRefillController::class, 'index'])->name('refill.index');
    
    Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('broadcast.index');
    Route::post('/broadcast', [AdminBroadcastController::class, 'send'])->name('broadcast.send');

    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::post('/broadcast/store', [AdminBroadcastController::class, 'store'])->name('broadcast.store');

    Route::get('/edukasi', [AdminEdukasiController::class, 'index'])->name('edukasi.index');
    Route::post('/edukasi', [AdminEdukasiController::class, 'store'])->name('edukasi.store');
    Route::delete('/edukasi/{id}', [AdminEdukasiController::class, 'destroy'])->name('edukasi.destroy');

    Route::get('/pasien/{id}', [AdminPasienController::class, 'show'])->name('pasien.show');
});