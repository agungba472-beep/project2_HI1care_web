<?php
use Illuminate\Support\Facades\Broadcast;

// Channel otorisasi untuk chat per sesi konsultasi
// Hanya pasien pemilik atau nakes yang ditunjuk yang boleh masuk
Broadcast::channel('konsultasi.{konsultasiId}', function ($user, $konsultasiId) {
    $konsultasi = \App\Models\Konsultasi::with(['pasien', 'nakes'])->find($konsultasiId);

    if (!$konsultasi) return false;

    // Cek apakah user adalah pasien pemilik konsultasi
    $pasien = \App\Models\Pasien::where('user_id', $user->id)->first();
    if ($pasien && $konsultasi->pasien_id === $pasien->id) return true;

    // Cek apakah user adalah nakes yang ditunjuk
    $nakes = \App\Models\Nakes::where('user_id', $user->id)->first();
    if ($nakes && $konsultasi->nakes_id === $nakes->id) return true;

    return false;
});