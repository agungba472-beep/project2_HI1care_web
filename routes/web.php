<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPasienController;
use App\Http\Controllers\Admin\AdminRefillController;
use App\Http\Controllers\Admin\AdminLaporanController;
use App\Http\Controllers\Admin\AdminBroadcastController;
use App\Http\Controllers\Admin\AdminEdukasiController;
use App\Http\Controllers\Admin\AdminJadwalController;
use App\Http\Controllers\Auth\WebAuthController;

Route::get('/', function () {
    return redirect('/login');
});

// Route untuk menampilkan file gambar dari storage (bypass 403 Forbidden Hostinger)
Route::get('/file/{path}', function (\Illuminate\Http\Request $request, $path) {
    $kandidatPath = [
        // Lokasi 1: storage/app/public (default Laravel, HARUSNYA selalu ini)
        storage_path('app/public/' . $path),
        // Lokasi 2: public/storage (kalau symlink kebetulan jalan normal)
        public_path('storage/' . $path),
        // Lokasi 3: sibling laravel directory (dugaan struktur folder khusus Hostinger)
        dirname(base_path(), 2) . '/laravel/storage/app/public/' . $path,
    ];

    $filePath = null;
    foreach ($kandidatPath as $kandidat) {
        if (file_exists($kandidat)) {
            $filePath = $kandidat;
            break;
        }
    }

    if (!$filePath) {
        // PENTING: catat ke log supaya lain kali file hilang, tinggal cek
        // storage/logs/laravel.log - tidak perlu tebak-tebak / upload ulang lagi.
        \Log::warning('[file-route] File tidak ditemukan di semua kandidat path', [
            'path_diminta' => $path,
            'kandidat_dicoba' => $kandidatPath,
        ]);
        abort(404);
    }

    // Dukung cache 304 (browser/app tidak perlu download ulang kalau file belum berubah)
    $mtime = filemtime($filePath);
    $etag = md5($filePath . $mtime);
    
    // Hapus tanda kutip dari request header karena ETag sering dikirim dengan tanda kutip
    $clientEtag = str_replace('"', '', $request->header('If-None-Match', ''));
    
    if ($clientEtag === $etag) {
        return response('', 304);
    }

    return response()->file($filePath, [
        'Content-Type' => mime_content_type($filePath),
        'Cache-Control' => 'public, max-age=31536000', // Cache 1 tahun penuh
        'ETag' => '"' . $etag . '"', // Sesuai standar HTTP wajib pakai tanda kutip
    ]);
})->where('path', '.*')->name('storage.file');

// Rute Autentikasi Web
Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [WebAuthController::class, 'login']);
Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

// Rute Clear Cache / Session Terjebak
Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    session()->flush();
    try {
        \Illuminate\Support\Facades\DB::table('sessions')->truncate();
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\DB::table('sessions')->delete();
    }
    return redirect()->route('login')->with('success', 'Cache dan seluruh sesi nyangkut berhasil dibersihkan! Silakan login kembali.');
})->name('clear-cache');

// Rute Admin (Dilindungi Auth)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // (Opsional tapi disarankan) Anda bisa membuat Middleware 'Role:admin' nanti agar rute ini benar-benar terkunci
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/change-password', [AdminUserController::class, 'changeOwnPassword'])->name('change-own-password');
    Route::post('/users/{id}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::post('/users/{id}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::post('/users/store-nakes', [AdminUserController::class, 'storeNakes'])->name('users.storeNakes');
    Route::post('/master/store', [AdminUserController::class, 'storeMaster'])->name('master.store');
    Route::delete('/master/{id}', [AdminUserController::class, 'destroyMaster'])->name('master.destroy');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::put('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('/pasien', [AdminPasienController::class, 'index'])->name('pasien.index');
    Route::post('/pasien/store', [AdminPasienController::class, 'store'])->name('pasien.store');
    Route::get('/refill', [AdminRefillController::class, 'index'])->name('refill.index');
    Route::post('/refill/{id}/update-status', [AdminRefillController::class, 'updateStatus'])->name('refill.updateStatus');
    
    Route::get('/broadcast', [AdminBroadcastController::class, 'index'])->name('broadcast.index');

    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [AdminLaporanController::class, 'exportExcel'])->name('laporan.export');
    Route::get('/laporan/cetak', [AdminLaporanController::class, 'cetakPrint'])->name('laporan.cetak');
    Route::get('/laporan/export-word', [AdminLaporanController::class, 'exportWord'])->name('laporan.word');
    Route::get('/laporan/cetak-detail/{id}', [AdminLaporanController::class, 'cetakDetailPasien'])->name('laporan.cetakDetail');
    Route::post('/broadcast/store', [AdminBroadcastController::class, 'store'])->name('broadcast.store');

    Route::get('/edukasi', [AdminEdukasiController::class, 'index'])->name('edukasi.index');
    Route::post('/edukasi', [AdminEdukasiController::class, 'store'])->name('edukasi.store');
    Route::put('/edukasi/{id}', [AdminEdukasiController::class, 'update'])->name('edukasi.update');
    Route::delete('/edukasi/{id}', [AdminEdukasiController::class, 'destroy'])->name('edukasi.destroy');

    Route::get('/jadwal', [AdminJadwalController::class, 'index'])->name('jadwal.index');
    Route::post('/jadwal', [AdminJadwalController::class, 'store'])->name('jadwal.store');
    Route::delete('/jadwal/{id}', [AdminJadwalController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/pasien/{id}', [AdminPasienController::class, 'show'])->name('pasien.show');
});