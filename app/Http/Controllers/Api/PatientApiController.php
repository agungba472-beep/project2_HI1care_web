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

    // ===================================================================
    // DASHBOARD PASIEN
    // ===================================================================

    public function getDashboard()
    {
        $user = auth()->user();
        $pasien = Pasien::where('user_id', $user->id)
            ->with(['master', 'kepatuhan' => function ($q) {
                $q->latest('last_update')->take(5);
            }])
            ->first();

        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $jadwalHariIni = AlarmArv::where('pasien_id', $pasien->id)
            ->whereDate('tanggal', now()->toDateString())
            ->orderBy('waktu', 'asc')
            ->get()
            ->map(function($alarm) {
                return [
                    'id'    => $alarm->id,
                    'jam'   => \Carbon\Carbon::parse($alarm->waktu)->format('H:i'),
                    'judul' => 'Jadwal Minum Obat ARV 💊',
                    'nada'  => $alarm->nada_dering ?? 'Default',
                    'status'=> $alarm->status
                ];
            });

        $unreadNotifCount = Notifikasi::where('user_id', $user->id)
            ->where('status', 'belum_dibaca')
            ->count();

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;

        $diminumCount = Kepatuhan::where('pasien_id', $user->pasien->id)
            ->whereIn('status', ['diminum', 'tepat waktu', 'hijau'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();

        $persentase = round(($diminumCount / $daysInMonth) * 100);

        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => $user,
                'pasien_info' => $pasien,
                'recent_edukasi' => ModulEdukasi::latest()->take(3)->get(),
                'jadwal_hari_ini' => $jadwalHariIni,
                'kepatuhan_terbaru' => $pasien->kepatuhan->first(),
                'status_kepatuhan' => $pasien->status_kepatuhan ?? 'hijau',
                'kepatuhan_percentage' => $persentase,
                'kepatuhan_diminum_count' => $diminumCount,
                'unread_notif_count' => $unreadNotifCount
            ]
        ]);
    }

    // ===================================================================
    // ALARM ARV (FR-P03)
    // ===================================================================

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

    public function markAlarmAsTaken(Request $request, $id)
    {
        $request->validate([
            'foto_bukti' => 'nullable|image|max:5120', // Maksimal 5MB
            'status' => 'nullable|in:diminum,terlewat'
        ]);

        $pasien = $this->getPasien();
        
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $alarm = AlarmArv::where('id', $id)->where('pasien_id', $pasien->id)->first();

        if (!$alarm) {
            return response()->json(['status' => 'error', 'message' => 'Alarm tidak ditemukan'], 404);
        }

        $alarm->update(['status' => 'sudah']);

        $fotoPath = null;
        if ($request->hasFile('foto_bukti')) {
            // Simpan foto ke folder storage/app/public/bukti dengan disk 'public'
            $path = $request->file('foto_bukti')->store('bukti', 'public');
            $fotoPath = $path;
        }

        $kepatuhanStatus = $request->input('status', 'diminum');

        Kepatuhan::create([
            'pasien_id'   => $pasien->id,
            'status'      => $kepatuhanStatus,
            'last_update' => now(),
            'foto_bukti'  => $fotoPath,
        ]);

        $this->updateStatusKepatuhan($pasien);

        return response()->json([
            'status'  => 'success',
            'message' => 'Obat berhasil ditandai sebagai diminum beserta foto bukti (jika ada)',
            'data'    => $alarm,
        ]);
    }

    public function saveAlarmSettings(Request $request)
    {
        $request->validate([
            'waktu'       => 'required|date_format:H:i',
            'tanggal'     => 'required|date_format:Y-m-d',
            'nada_dering' => 'nullable|string|max:100', // Sekarang nullable
            'is_everyday' => 'nullable|boolean',
        ]);

        $pasien = $this->getPasien();

        if (!$pasien) {
            return response()->json(['status'  => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        // Hapus alarm 'belum' di masa depan agar tidak duplikat jadwal
        AlarmArv::where('pasien_id', $pasien->id)
            ->where('status', 'belum')
            ->where('tanggal', '>=', now()->toDateString())
            ->delete();

        $isEveryday = filter_var($request->is_everyday, FILTER_VALIDATE_BOOLEAN);
        $startDate = \Carbon\Carbon::parse($request->tanggal);
        
        $limit = $isEveryday ? 30 : 1; 

        // SIMPAN NADA DERING PER ALARM!
        for ($i = 0; $i < $limit; $i++) {
            AlarmArv::create([
                'pasien_id'   => $pasien->id,
                'waktu'       => $request->waktu,
                'tanggal'     => $startDate->copy()->addDays($i)->toDateString(),
                'status'      => 'belum',
                'nada_dering' => $request->nada_dering ?? 'Default', // Default jika kosong
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan alarm berhasil disimpan',
        ]);
    }

    // FITUR BARU: Hapus Alarm
    public function deleteAlarm($id)
    {
        $pasien = $this->getPasien();
        
        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);
        }

        $alarm = AlarmArv::where('id', $id)->where('pasien_id', $pasien->id)->first();

        if (!$alarm) {
            return response()->json(['status' => 'error', 'message' => 'Alarm tidak ditemukan'], 404);
        }

        $alarm->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal alarm berhasil dihapus'
        ]);
    }


    // ===================================================================
    // KEPATUHAN MINUM OBAT (FR-P05)
    // ===================================================================

    public function trackKepatuhan(Request $request)
    {
        $request->validate(['status' => 'required|in:diminum,terlewat,tunda']);
        $pasien = $this->getPasien();
        
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $log = Kepatuhan::create(['pasien_id' => $pasien->id, 'status' => $request->status, 'last_update' => now()]);
        $this->updateStatusKepatuhan($pasien);

        return response()->json(['status' => 'success', 'message' => 'Status kepatuhan berhasil diperbarui', 'data' => $log]);
    }

    private function updateStatusKepatuhan(Pasien $pasien)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $terlewatCount = Kepatuhan::where('pasien_id', $pasien->id)
            ->whereIn('status', ['terlewat', 'tunda'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();

        $status = 'merah';
        if ($terlewatCount == 0) $status = 'hijau';
        elseif ($terlewatCount <= 2) $status = 'kuning';

        $pasien->update(['status_kepatuhan' => $status]);
    }


    // ===================================================================
    // DIARY HARIAN (FR-P04)
    // ===================================================================

    public function getDiary()
    {
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $diary = DiaryHarian::where('pasien_id', $pasien->id)->orderByDesc('tanggal')->paginate(10);
        return response()->json(['status' => 'success', 'data' => $diary]);
    }

    public function storeDiary(Request $request)
    {
        $request->validate(['kondisi' => 'required|string', 'gejala' => 'nullable|string', 'catatan' => 'nullable|string']);
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $diary = DiaryHarian::create([
            'pasien_id' => $pasien->id,
            'tanggal' => now()->toDateString(),
            'kondisi' => $request->kondisi,
            'gejala' => $request->gejala,
            'catatan' => $request->catatan,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Diary berhasil disimpan', 'data' => $diary], 201);
    }

    public function destroyDiary($id)
    {
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $diary = DiaryHarian::where('id', $id)->where('pasien_id', $pasien->id)->first();
        if (!$diary) {
            return response()->json(['status' => 'error', 'message' => 'Catatan tidak ditemukan'], 404);
        }
        
        $diary->delete();
        return response()->json(['status' => 'success', 'message' => 'Diary berhasil dihapus']);
    }


    // ===================================================================
    // REFILL OBAT (FR-P07)
    // ===================================================================

    public function getRefillHistory()
    {
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $refills = RefillObat::where('pasien_id', $pasien->id)->orderByDesc('tanggal_refill')->get();
        return response()->json(['status' => 'success', 'data' => $refills]);
    }

    public function requestRefill(Request $request)
    {
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $pending = RefillObat::where('pasien_id', $pasien->id)
            ->whereIn('status', ['menunggu', 'disetujui'])
            ->first();

        if ($pending) {
            return response()->json(['status' => 'error', 'message' => 'Anda masih memiliki permintaan refill yang belum diproses atau sedang berjalan'], 422);
        }

        $lastSiklus = RefillObat::where('pasien_id', $pasien->id)->max('siklus_ke') ?? 0;

        $refill = RefillObat::create([
            'pasien_id' => $pasien->id,
            'tanggal_refill' => now()->toDateString(),
            'siklus_ke' => $lastSiklus + 1,
            'status' => 'menunggu',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Permintaan refill obat berhasil diajukan', 'data' => $refill], 201);
    }

    public function uploadRefillPhoto(Request $request, $id)
    {
        $request->validate([
            'foto_bukti' => 'required|image|max:5120' // Maksimal 5MB
        ]);

        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $refill = RefillObat::where('id', $id)->where('pasien_id', $pasien->id)->first();
        if (!$refill) return response()->json(['status' => 'error', 'message' => 'Pengajuan refill tidak ditemukan'], 404);

        if ($refill->status !== 'disetujui') {
            return response()->json(['status' => 'error', 'message' => 'Anda belum bisa mengunggah foto karena status pengajuan belum disetujui admin'], 422);
        }

        if ($request->hasFile('foto_bukti')) {
            $path = $request->file('foto_bukti')->store('bukti', 'public');
            $refill->foto_bukti = $path;
            $refill->save();
        }

        return response()->json(['status' => 'success', 'message' => 'Bukti foto berhasil diunggah', 'data' => $refill]);
    }


    // ===================================================================
    // BOOKING KONSULTASI (FR-P08)
    // ===================================================================

    public function getNakesSchedules()
    {
        $schedules = JadwalNakes::with(['nakes.user'])->orderBy('hari')->orderBy('jam_mulai')->get();
        return response()->json(['status' => 'success', 'data' => $schedules]);
    }

    public function storeBooking(Request $request)
    {
        $request->validate([
            'nakes_id' => 'required|exists:nakes,id', 
            'tanggal' => 'required|date|after_or_equal:today', 
            'waktu' => 'required',
            'kategori' => 'nullable|in:booking,livechat'
        ]);
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

        $kategori = $request->kategori ?? 'booking';

        $existingBooking = Konsultasi::where('pasien_id', $pasien->id)
            ->where('nakes_id', $request->nakes_id)
            ->where('kategori', $kategori)
            ->whereIn('status', ['pending', 'diterima', 'dijadwalkan'])
            ->first();

        if ($existingBooking) {
            return response()->json([
                'status' => 'success', 
                'message' => 'Melanjutkan sesi chat yang sudah ada', 
                'data' => $existingBooking
            ], 200);
        }

        if ($kategori === 'booking') {
            $jadwal = JadwalNakes::where('nakes_id', $request->nakes_id)
                ->where('hari', \Carbon\Carbon::parse($request->tanggal)->translatedFormat('l'))
                ->where('jam_mulai', '<=', $request->waktu)
                ->first();

            if ($jadwal && $jadwal->kuota <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Maaf, kuota konsultasi untuk jadwal ini sudah penuh pada hari ini.'
                ], 422);
            }
        }

        $booking = Konsultasi::create([
            'pasien_id' => $pasien->id, 
            'nakes_id' => $request->nakes_id, 
            'tanggal' => $request->tanggal, 
            'waktu' => $request->waktu, 
            'status' => 'pending',
            'kategori' => $kategori
        ]);

        if ($kategori === 'booking' && isset($jadwal) && $jadwal->kuota > 0) {
            $jadwal->decrement('kuota');
        }

        return response()->json(['status' => 'success', 'message' => 'Booking konsultasi berhasil dibuat', 'data' => $booking], 201);
    }

    public function getMyConsultations()
    {
        $pasien = $this->getPasien();
        if (!$pasien) return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan'], 404);

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
                    'nakes_user_id' => $k->nakes?->user?->id,
                    'tanggal'       => $k->tanggal,
                    'waktu'         => $k->waktu,
                    'status'        => $k->status,
                    'kategori'      => $k->kategori,
                    'chat_status'   => $k->chat_status,
                    'last_message'  => $k->latestChat?->pesan ?? 'Belum ada pesan',
                    'updated_at'    => $k->updated_at,
                ];
            });

        return response()->json(['status' => 'success', 'data' => $konsultasi]);
    }


    // ===================================================================
    // MODUL EDUKASI & NOTIFIKASI
    // ===================================================================

    public function getEdukasi()
    {
        $edukasi = ModulEdukasi::latest()->paginate(10);
        
        $edukasi->getCollection()->transform(function ($item) {
            if ($item->cover && !str_starts_with($item->cover, 'http')) {
                $item->cover = url('file/' . $item->cover);
            }
            return $item;
        });

        return response()->json(['status' => 'success', 'data' => $edukasi]);
    }

    public function getNotifications()
    {
        $user = auth()->user();
        $notifikasi = Notifikasi::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($notif) {
                return [
                    'id'         => $notif->id,
                    'judul'      => $notif->judul,
                    'pesan'      => $notif->pesan,
                    'is_read'    => $notif->status === 'dibaca' ? 1 : 0,
                    'status'     => $notif->status,
                    'created_at' => $notif->created_at ? $notif->created_at->diffForHumans() : 'Baru saja'
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $notifikasi
        ]);
    }

    public function markNotificationsAsRead(Request $request)
    {
        Notifikasi::where('user_id', $request->user()->id)
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'dibaca']);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai dibaca'
        ]);
    }


    // ===================================================================
    // FASILITAS KESEHATAN
    // ===================================================================

    public function getFaskes()
    {
        $faskes = Faskes::orderBy('nama')->get();
        return response()->json(['status' => 'success', 'data' => $faskes]);
    }

}