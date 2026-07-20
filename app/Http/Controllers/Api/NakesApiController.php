<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Konsultasi;
use App\Models\Nakes;
use App\Models\Pasien;
use App\Models\Chat;
use App\Models\RiwayatRegimenPasien;
use App\Models\RiwayatIo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            
        // Hitung metrik baru
        $totalPasien = \App\Models\Pasien::count(); 
        $perluPerhatian = \App\Models\Pasien::whereIn('status_kepatuhan', ['merah', 'kuning'])->count();
        $pesanBaru = \App\Models\Notifikasi::where('user_id', $nakes->user_id)->where('status', 'belum_dibaca')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'profil' => $nakes->load('user'),
                'unread_notif_count' => $pesanBaru,
                'statistik' => [
                    'menunggu_persetujuan' => $pendingCount,
                    'jadwal_hari_ini' => $todayKonsultasi->count(),
                    'total_pasien' => $totalPasien,
                    'perlu_perhatian' => $perluPerhatian,
                    'pesan_baru' => $pesanBaru,
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
            ->whereIn('status', ['diterima', 'dijadwalkan', 'selesai'])
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

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;

        $diminumCount = $pasien->kepatuhan()
            ->whereIn('status', ['diminum', 'tepat waktu', 'hijau'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();

        $persentase = round(($diminumCount / $daysInMonth) * 100);
        
        $pasien->kepatuhan_percentage = $persentase;
        $pasien->kepatuhan_diminum_count = $diminumCount;

        return response()->json(['status' => 'success', 'data' => $pasien]);
    }

    public function finishConsultation($id)
    {
        $konsultasi = Konsultasi::find($id);
        if (!$konsultasi) return response()->json(['status' => 'error', 'message' => 'Sesi tidak ditemukan'], 404);
        
        $konsultasi->update(['status' => 'selesai', 'chat_status' => 'selesai']);
        return response()->json(['status' => 'success', 'message' => 'Sesi konsultasi telah berhasil diselesaikan.']);
    }

    // ===================================================================
    // 5. INPUT DATA KLINIS PASIEN
    // ===================================================================
    public function getRiwayatRegimen($id)
    {
        $riwayat = RiwayatRegimenPasien::with(['masterObat', 'nakes'])->where('pasien_id', $id)->orderBy('tanggal_mulai', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $riwayat]);
    }

    public function storeRiwayatRegimen(Request $request, $id)
    {
        $request->validate([
            'master_obat_id' => 'required|exists:master_obats,id',
            'tanggal_mulai' => 'required|date',
            'alasan_ganti' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                // Auto-close regimen lama
                $activeRegimen = RiwayatRegimenPasien::where('pasien_id', $id)->whereNull('tanggal_selesai')->first();
                if ($activeRegimen) {
                    $activeRegimen->update([
                        'tanggal_selesai' => $request->tanggal_mulai,
                        'alasan_ganti' => $request->alasan_ganti ?? 'Diganti dengan regimen baru'
                    ]);
                }

                // Insert regimen baru
                RiwayatRegimenPasien::create([
                    'pasien_id' => $id,
                    'master_obat_id' => $request->master_obat_id,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'ditetapkan_oleh' => auth()->id(),
                    'alasan_ganti' => $request->alasan_ganti
                ]);
            });

            return response()->json(['status' => 'success', 'message' => 'Regimen berhasil diperbarui.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui regimen.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getRiwayatIo($id)
    {
        $riwayat = RiwayatIo::with(['masterIo', 'nakes'])->where('pasien_id', $id)->orderBy('tanggal_diagnosis', 'desc')->get();
        return response()->json(['status' => 'success', 'data' => $riwayat]);
    }

    public function storeRiwayatIo(Request $request, $id)
    {
        $request->validate([
            'master_io_id' => 'required',
            'nama_io_baru' => 'required_if:master_io_id,lainnya',
            'tanggal_diagnosis' => 'required|date',
            'status' => 'required|in:aktif,sembuh',
            'tanggal_sembuh' => 'nullable|date',
            'catatan' => 'nullable|string'
        ]);

        $master_io_id = $request->master_io_id;
        
        if ($master_io_id === 'lainnya') {
            $newMasterIo = \App\Models\MasterIo::create([
                'nama_io' => $request->nama_io_baru,
                'status_aktif' => true,
            ]);
            $master_io_id = $newMasterIo->id;
        }

        RiwayatIo::create([
            'pasien_id' => $id,
            'master_io_id' => $master_io_id,
            'tanggal_diagnosis' => $request->tanggal_diagnosis,
            'status' => $request->status,
            'tanggal_sembuh' => $request->status === 'sembuh' ? $request->tanggal_sembuh : null,
            'catatan' => $request->catatan,
            'ditetapkan_oleh' => auth()->id(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Riwayat IO berhasil ditambahkan.']);
    }
}