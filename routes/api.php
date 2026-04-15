<?php
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register-pasien', [AuthController::class, 'registerPasien']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });
});

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin/pending-users', [AdminController::class, 'getPendingUsers']);

    Route::post('/admin/approve/{id}', [AdminController::class, 'approveUser']);

    Route::post('/admin/reject/{id}', [AdminController::class, 'rejectUser']);

});

Route::middleware('auth:sanctum')->get('/profile', [ProfileController::class, 'profile']);