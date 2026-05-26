<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlarmArv;
use App\Models\DiaryHarian;
use App\Models\Faskes;
use App\Models\JadwalNakes;
use App\Models\Kepatuhan;
use App\Models\Konsultasi;
use App\Models\ModulEdukasi;
use App\Models\Nakes;
use App\Models\Notifikasi;
use App\Models\Pasien;
use App\Models\RefillObat;
use Illuminate\Http\Request;

class PatientApiController extends Controller
{
    /**
     * Helper: Ambil data pasien dari user yang login.
     */
    private function getPasien()
    {
        return Pasien::where('user_id', auth()->id())->first();
    }

    /**
     * Dashboard Pasien
     * Menampilkan info pasien, edukasi terbaru, dan status kepatuhan terakhir.
     */
    public function getDashboard()
    {
        $user = auth()->user();
        $pasien = Pasien::where('user_id', $user->id)
            ->with(['master', 'kepatuhan' => function ($q) {
                $q->latest('last_update')->take(5);
            }])
            ->first();

        if (!$pasien) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data pasien tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'pasien_info' => $pasien,
                'recent_edukasi' => ModulEdukasi::latest()->take(3)->get(),
                'alarm_hari_ini' => AlarmArv::where('pasien_id', $pasien->id)
                    ->whereDate('tanggal', now()->toDateString())
                    ->get(),
                'kepatuhan_terbaru' => $pasien->kepatuhan->first(),
            ]
        ]);
    }

    // ===================================================================
    // ALARM ARV (FR-P03)
    // ===================================================================

    /**
     * Ambil semua alarm milik pasien.
     */
    public function getAlarms()
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $alarms = AlarmArv::where('pasien_id', $pasien->id)
            ->orderBy('waktu')
            ->get();

        return response()->json(['status' => 'success', 'data' => $alarms]);
    }

    /**
     * Simpan alarm baru.
     */
    public function storeAlarm(Request $request)
    {
        $request->validate([
            'waktu' => 'required',    // Format jam, misal "08:00"
            'tanggal' => 'nullable|date',
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $alarm = AlarmArv::create([
            'pasien_id' => $pasien->id,
            'waktu' => $request->waktu,
            'status' => 'belum',
            'tanggal' => $request->tanggal ?? now()->toDateString(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Alarm berhasil ditambahkan',
            'data' => $alarm
        ], 201);
    }

    // ===================================================================
    // KEPATUHAN MINUM OBAT (FR-P05)
    // ===================================================================

    /**
     * Catat status kepatuhan minum obat.
     */
    public function trackKepatuhan(Request $request)
    {
        $request->validate([
            'status' => 'required|in:diminum,terlewat,tunda',
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $log = Kepatuhan::create([
            'pasien_id' => $pasien->id,
            'status' => $request->status,
            'last_update' => now(),
        ]);

        // Update status kepatuhan di tabel pasien berdasarkan riwayat
        $this->updateStatusKepatuhan($pasien);

        return response()->json([
            'status' => 'success',
            'message' => 'Status kepatuhan berhasil diperbarui',
            'data' => $log
        ]);
    }

    /**
     * Hitung dan update status kepatuhan pasien (hijau/kuning/merah).
     */
    private function updateStatusKepatuhan(Pasien $pasien)
    {
        // Ambil 30 catatan terakhir
        $recentLogs = Kepatuhan::where('pasien_id', $pasien->id)
            ->latest('last_update')
            ->take(30)
            ->get();

        if ($recentLogs->isEmpty()) return;

        $total = $recentLogs->count();
        $diminum = $recentLogs->where('status', 'diminum')->count();
        $persentase = ($diminum / $total) * 100;

        if ($persentase >= 80) {
            $status = 'hijau';
        } elseif ($persentase >= 50) {
            $status = 'kuning';
        } else {
            $status = 'merah';
        }

        $pasien->update(['status_kepatuhan' => $status]);
    }

    // ===================================================================
    // DIARY HARIAN (FR-P04)
    // ===================================================================

    /**
     * Ambil riwayat diary pasien.
     */
    public function getDiary()
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $diary = DiaryHarian::where('pasien_id', $pasien->id)
            ->orderByDesc('tanggal')
            ->paginate(10);

        return response()->json(['status' => 'success', 'data' => $diary]);
    }

    /**
     * Simpan diary harian baru.
     * Menggunakan kolom yang sesuai model: tanggal, kondisi, gejala, catatan
     */
    public function storeDiary(Request $request)
    {
        $request->validate([
            'kondisi' => 'required|string',
            'gejala' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $diary = DiaryHarian::create([
            'pasien_id' => $pasien->id,
            'tanggal' => now()->toDateString(),
            'kondisi' => $request->kondisi,
            'gejala' => $request->gejala,
            'catatan' => $request->catatan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Diary berhasil disimpan',
            'data' => $diary
        ], 201);
    }

    // ===================================================================
    // REFILL OBAT (FR-P07)
    // ===================================================================

    /**
     * Ambil riwayat refill obat.
     */
    public function getRefillHistory()
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $refills = RefillObat::where('pasien_id', $pasien->id)
            ->orderByDesc('tanggal_refill')
            ->get();

        return response()->json(['status' => 'success', 'data' => $refills]);
    }

    /**
     * Ajukan permintaan refill obat baru.
     */
    public function requestRefill(Request $request)
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        // Cek apakah ada refill yang statusnya masih menunggu (UBAH DARI 'pending')
        $pending = RefillObat::where('pasien_id', $pasien->id)
            ->where('status', 'menunggu') 
            ->first();

        if ($pending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda masih memiliki permintaan refill yang belum diproses'
            ], 422);
        }

        // Hitung siklus berikutnya secara otomatis
        $lastSiklus = RefillObat::where('pasien_id', $pasien->id)->max('siklus_ke') ?? 0;

        $refill = RefillObat::create([
            'pasien_id' => $pasien->id,
            'tanggal_refill' => now()->toDateString(),
            'siklus_ke' => $lastSiklus + 1,
            'status' => 'menunggu', // UBAH DARI 'pending'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Permintaan refill obat berhasil diajukan',
            'data' => $refill
        ], 201);
    }

    // ===================================================================
    // BOOKING KONSULTASI (FR-P08)
    // ===================================================================

    /**
     * Lihat jadwal nakes yang tersedia.
     */
    public function getNakesSchedules()
    {
        $schedules = JadwalNakes::with(['nakes.user'])
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    /**
     * Booking konsultasi dengan nakes.
     */
    public function storeBooking(Request $request)
    {
        $request->validate([
            'nakes_id' => 'required|exists:nakes,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'waktu' => 'required',
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        // Cek duplikasi booking
        $existingBooking = Konsultasi::where('pasien_id', $pasien->id)
            ->where('nakes_id', $request->nakes_id)
            ->where('tanggal', $request->tanggal)
            ->where('status', '!=', 'batal')
            ->first();

        if ($existingBooking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah memiliki booking dengan nakes ini pada tanggal tersebut'
            ], 422);
        }

        $booking = Konsultasi::create([
            'pasien_id' => $pasien->id,
            'nakes_id' => $request->nakes_id,
            'tanggal' => $request->tanggal,
            'waktu' => $request->waktu,
            'status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking konsultasi berhasil dibuat',
            'data' => $booking
        ], 201);
    }

    /**
     * Daftar konsultasi aktif milik pasien (untuk masuk ke chat).
     */
    public function getMyConsultations()
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $konsultasi = Konsultasi::where('pasien_id', $pasien->id)
            ->whereIn('status', ['pending', 'diterima', 'dijadwalkan'])
            ->with(['nakes.user:id,nama', 'latestChat'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($k) {
                return [
                    'id'            => $k->id,
                    'nakes_nama'    => $k->nakes?->user?->nama ?? $k->nakes?->nama ?? 'Nakes',
                    'nakes_profesi' => $k->nakes?->profesi ?? '-',
                    'tanggal'       => $k->tanggal,
                    'waktu'         => $k->waktu,
                    'status'        => $k->status,
                    'chat_status'   => $k->chat_status,
                    'last_message'  => $k->latestChat?->pesan ?? 'Belum ada pesan',
                    'updated_at'    => $k->updated_at,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $konsultasi]);
    }

    // ===================================================================
    // MODUL EDUKASI
    // ===================================================================

    /**
     * Ambil semua modul edukasi.
     */
    public function getEdukasi()
    {
        $edukasi = ModulEdukasi::latest()->paginate(10);

        return response()->json(['status' => 'success', 'data' => $edukasi]);
    }

    // ===================================================================
    // NOTIFIKASI & BROADCAST
    // ===================================================================

    /**
     * Ambil notifikasi milik user yang login.
     */
    public function getNotifications()
    {
        $notifikasi = Notifikasi::where('user_id', auth()->id())
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(['status' => 'success', 'data' => $notifikasi]);
    }

    // ===================================================================
    // FASILITAS KESEHATAN
    // ===================================================================

    /**
     * Ambil semua fasilitas kesehatan.
     */
    public function getFaskes()
    {
        $faskes = Faskes::orderBy('nama')->get();

        return response()->json(['status' => 'success', 'data' => $faskes]);
    }

    // ===================================================================
    // PENGATURAN ALARM & NADA DERING
    // ===================================================================

    /**
     * POST /api/patient/alarms/settings
     * Simpan pengaturan alarm (waktu, tanggal) + nada dering pilihan pasien.
     */
    /**
     * Tandai alarm spesifik sebagai sudah diminum.
     */
    public function markAlarmAsTaken($id)
    {
        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $alarm = AlarmArv::where('id', $id)
            ->where('pasien_id', $pasien->id)
            ->first();

        if (!$alarm) {
            return response()->json(['status' => 'error', 'message' => 'Alarm tidak ditemukan'], 404);
        }

        // Update status alarm
        $alarm->update(['status' => 'sudah']);

        // Catat juga ke tabel kepatuhan
        Kepatuhan::create([
            'pasien_id'   => $pasien->id,
            'status'      => 'diminum',
            'last_update' => now(),
        ]);

        // Update status kepatuhan pasien
        $this->updateStatusKepatuhan($pasien);

        return response()->json([
            'status'  => 'success',
            'message' => 'Obat berhasil ditandai sebagai diminum',
            'data'    => $alarm,
        ]);
    }

    public function saveAlarmSettings(Request $request)
    {
        $request->validate([
            'waktu'       => 'required|date_format:H:i',
            'tanggal'     => 'required|date_format:Y-m-d',
            'nada_dering' => 'required|string|max:100',
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data pasien tidak ditemukan'
            ], 404);
        }

        // 1. Update nada_dering di tabel pasien
        $pasien->update([
            'nada_dering' => $request->nada_dering,
        ]);

        // 2. Simpan alarm baru di tabel alarm_arv
        $alarm = AlarmArv::create([
            'pasien_id' => $pasien->id,
            'waktu'     => $request->waktu,
            'tanggal'   => $request->tanggal,
            'status'    => 'belum',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan alarm dan nada dering berhasil disimpan',
            'data'    => [
                'alarm'       => $alarm,
                'nada_dering' => $pasien->nada_dering,
            ],
        ]);
    }
}
