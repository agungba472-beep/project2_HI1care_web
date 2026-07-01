<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PatientApiController;
use App\Http\Controllers\Api\NakesApiController;
use App\Http\Controllers\Api\ChatController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']); // Registrasi Pasien (alias)
Route::post('/register-pasien', [AuthController::class, 'registerPasien']); // Registrasi Pasien (legacy)

/*
|--------------------------------------------------------------------------
| Protected Routes (Sanctum)
|--------------------------------------------------------------------------
*/
Route::get('/debug-tokens', function() {
    return \App\Models\User::whereNotNull('expo_push_token')->get(['id', 'username', 'expo_push_token']);
});

Route::middleware('auth:sanctum')->group(function () {

    // Broadcasting auth (untuk Pusher private channels)
    Broadcast::routes(['middleware' => ['auth:sanctum']]);

    // Auth & Profile
    Route::get('/user', [ProfileController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/profile/update', [ProfileController::class, 'update']);
    Route::post('/profile/photo', [ProfileController::class, 'uploadPhoto']);
    Route::get('/profile', [ProfileController::class, 'profile']);

    // --- ADMIN API ---
    Route::prefix('admin')->group(function () {
        Route::get('/pending-users', [AdminController::class, 'getPendingUsers']);
        Route::post('/approve/{id}', [AdminController::class, 'approveUser']);
        Route::post('/reject/{id}', [AdminController::class, 'rejectUser']);
    });

    // --- FITUR PASIEN (MOBILE) ---
    Route::prefix('patient')->group(function () {
        Route::get('/dashboard', [PatientApiController::class, 'getDashboard']);
        Route::get('/notifications', [PatientApiController::class, 'getNotifications']);
        Route::post('/notifications/mark-as-read', [PatientApiController::class, 'markNotificationsAsRead']);

        // Alarm & Kepatuhan (FR-P03, FR-P05)
        Route::get('/alarms', [PatientApiController::class, 'getAlarms']);
        Route::post('/alarms', [PatientApiController::class, 'storeAlarm']);
        Route::post('/alarms/settings', [PatientApiController::class, 'saveAlarmSettings']);
        Route::post('/alarms/{id}/taken', [PatientApiController::class, 'markAlarmAsTaken']);
        Route::post('/kepatuhan/track', [PatientApiController::class, 'trackKepatuhan']);
        Route::delete('/alarms/{id}', [PatientApiController::class, 'deleteAlarm']);

        // Diary Harian (FR-P04)
        Route::get('/diary', [PatientApiController::class, 'getDiary']);
        Route::post('/diary', [PatientApiController::class, 'storeDiary']);
        Route::delete('/diary/{id}', [PatientApiController::class, 'destroyDiary']);

        // Refill Obat (FR-P07)
        Route::get('/refill-history', [PatientApiController::class, 'getRefillHistory']);
        Route::post('/refill/request', [PatientApiController::class, 'requestRefill']);
        Route::post('/refill/{id}/photo', [PatientApiController::class, 'uploadRefillPhoto']);

        // Booking Konsultasi (FR-P08)
        Route::get('/nakes-schedules', [PatientApiController::class, 'getNakesSchedules']);
        Route::post('/booking', [PatientApiController::class, 'storeBooking']);

        // Konsultasi aktif pasien (untuk masuk ke chat)
        Route::get('/my-consultations', [PatientApiController::class, 'getMyConsultations']);
    });

    // ==========================================
    // RUTE KHUSUS TENAGA KESEHATAN (NAKES)
    // ==========================================
    Route::prefix('nakes')->group(function () {
        Route::get('/dashboard', [NakesApiController::class, 'getDashboard']);
        
        // Manajemen Konsultasi
        Route::get('/consultations/pending', [NakesApiController::class, 'getPendingConsultations']);
        Route::post('/consultations/{id}/respond', [NakesApiController::class, 'respondConsultation']);
        Route::post('/consultations/{id}/finish', [NakesApiController::class, 'finishConsultation']);
        
        // Live Chat
        Route::get('/active-chats', [ChatController::class, 'getActiveChats']);
        // Monitoring Pasien
        Route::get('/patients', [NakesApiController::class, 'getMyPatients']);
        Route::get('/patients/{id}', [NakesApiController::class, 'getPatientDetail']);
    });
    // --- CHAT API (Shared: Pasien & Nakes) ---
    Route::prefix('chat')->group(function () {
        Route::get('/{konsultasiId}/messages', [ChatController::class, 'getMessages']);
        Route::post('/send', [ChatController::class, 'sendMessage']);
        Route::post('/{konsultasiId}/takeover', [ChatController::class, 'takeOverChat']);
    });

    // --- FITUR UMUM ---
    Route::get('/edukasi', [PatientApiController::class, 'getEdukasi']);           // Modul Edukasi
    Route::get('/notifikasi', [PatientApiController::class, 'getNotifications']); // Notifikasi & Broadcast
    Route::get('/faskes', [PatientApiController::class, 'getFaskes']);             // Fasilitas Kesehatan
});