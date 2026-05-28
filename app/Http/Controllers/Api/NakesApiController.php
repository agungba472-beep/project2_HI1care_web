<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\Nakes;
use App\Models\Pasien;
use App\Models\Chat;
use Illuminate\Http\Request;

class NakesApiController extends Controller
{
    /**
     * Helper: Ambil data Nakes dari user yang login
     */
    private function getNakes()
    {
        return Nakes::where('user_id', auth()->id())->first();
    }

    // ===================================================================
    // 1. DASHBOARD NAKES
    // ===================================================================
    public function getDashboard()
    {
        $nakes = $this->getNakes();
        if (!$nakes) return response()->json(['status' => 'error', 'message' => 'Data Nakes tidak ditemukan'], 404);

        $today = now()->toDateString();

        // Hitung statistik
        $pendingCount = Konsultasi::where('nakes_id', $nakes->id)->where('status', 'pending')->count();
        $todayKonsultasi = Konsultasi::where('nakes_id', $nakes->id)
            ->where('tanggal', $today)
            ->whereIn('status', ['diterima', 'dijadwalkan'])
            ->with(['pasien.user', 'pasien.master'])
            ->orderBy('waktu')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'profil' => $nakes->load('user'),
                'statistik' => [
                    'menunggu_persetujuan' => $pendingCount,
                    'jadwal_hari_ini' => $todayKonsultasi->count(),
                ],
                'jadwal_hari_ini' => $todayKonsultasi
            ]
        ]);
    }

    // ===================================================================
    // 2. MANAJEMEN KONSULTASI (PERMINTAAN MASUK)
    // ===================================================================
    public function getPendingConsultations()
    {
        $nakes = $this->getNakes();
        if (!$nakes) return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);

        $permintaan = Konsultasi::where('nakes_id', $nakes->id)
            ->where('status', 'pending')
            ->with(['pasien.user', 'pasien.master'])
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();

        return response()->json(['status' => 'success', 'data' => $permintaan]);
    }

    public function respondConsultation(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak'
        ]);

        $nakes = $this->getNakes();
        $konsultasi = Konsultasi::where('id', $id)->where('nakes_id', $nakes->id)->first();

        if (!$konsultasi) return response()->json(['status' => 'error', 'message' => 'Data konsultasi tidak ditemukan'], 404);

        $konsultasi->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status konsultasi berhasil diperbarui menjadi ' . $request->status,
            'data' => $konsultasi
        ]);
    }

    // ===================================================================
    // 3. DAFTAR KONSULTASI AKTIF (UNTUK LIVE CHAT)
    // ===================================================================
    public function getActiveChats()
    {
        $nakes = $this->getNakes();
        if (!$nakes) return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        
        $chats = Konsultasi::where('nakes_id', $nakes->id)
            ->whereIn('status', ['diterima', 'dijadwalkan'])
            ->with(['pasien.user', 'pasien.master', 'latestChat'])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json(['status' => 'success', 'data' => $chats]);
    }

    // ===================================================================
    // 4. MONITORING PASIEN (KEPATUHAN, DIARY, REFILL)
    // ===================================================================
    public function getMyPatients()
    {
        $nakes = $this->getNakes();
        if (!$nakes) return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        
        // Ambil ID pasien yang pernah / sedang konsultasi dengan Nakes ini
        $pasienIds = Konsultasi::where('nakes_id', $nakes->id)->pluck('pasien_id')->unique();

        $pasiens = Pasien::whereIn('id', $pasienIds)
            ->with(['user', 'master'])
            ->get()
            ->map(function ($p) {
                // Ambil status kepatuhan terbarunya
                $p->kepatuhan_terbaru = $p->status_kepatuhan;
                return $p;
            });

        return response()->json(['status' => 'success', 'data' => $pasiens]);
    }

    public function getPatientDetail($id)
    {
        $nakes = $this->getNakes();
        if (!$nakes) return response()->json(['status' => 'error', 'message' => 'Akses ditolak'], 403);
        
        // Validasi apakah Nakes ini berhak melihat data pasien ini
        $pernahKonsultasi = Konsultasi::where('nakes_id', $nakes->id)->where('pasien_id', $id)->exists();
        if (!$pernahKonsultasi) {
            return response()->json(['status' => 'error', 'message' => 'Anda tidak memiliki akses ke rekam medis pasien ini'], 403);
        }

        $pasien = Pasien::where('id', $id)
            ->with([
                'user', 
                'master',
                'diary' => function($q) { $q->orderByDesc('tanggal')->take(10); },
                'refill' => function($q) { $q->orderByDesc('tanggal_refill')->take(5); },
                'kepatuhan' => function($q) { $q->latest('last_update')->take(30); }
            ])
            ->first();

        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        return response()->json(['status' => 'success', 'data' => $pasien]);
    }
}