<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\RiwayatRegimenPasien;
use App\Models\RiwayatIo;
use App\Models\MasterObat;
use App\Models\MasterIo;

class AdminPasienController extends Controller
{
    /**
     * Menampilkan daftar seluruh pasien HIV yang terdaftar.
     * Mendukung filter pencarian nama dan status kepatuhan (FR-A02).
     */
    public function index(Request $request)
    {
        $query = Pasien::with(['user', 'master']);

        // Filter: Pencarian berdasarkan nama (via relasi master)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('master', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // Filter: Status kepatuhan (exact match)
        if ($request->filled('status')) {
            $query->where('status_kepatuhan', $request->status);
        }

        $patients = $query->orderBy('created_at', 'desc')->get();
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;

        foreach ($patients as $patient) {
            $diminumCount = $patient->kepatuhan()
                ->whereIn('status', ['diminum', 'tepat waktu', 'hijau'])
                ->whereMonth('last_update', $currentMonth)
                ->whereYear('last_update', $currentYear)
                ->count();
            
            $terlewatCount = $patient->kepatuhan()
                ->whereIn('status', ['terlewat', 'tunda'])
                ->whereMonth('last_update', $currentMonth)
                ->whereYear('last_update', $currentYear)
                ->count();

            $newStatus = 'merah';
            if ($terlewatCount == 0) {
                $newStatus = 'hijau';
            } elseif ($terlewatCount <= 2) {
                $newStatus = 'kuning';
            } else {
                $newStatus = 'merah';
            }

            if ($patient->status_kepatuhan !== $newStatus) {
                $patient->status_kepatuhan = $newStatus;
                $patient->save();
            }
        }
        
        return view('admin.pasien', compact('patients'));
    }

    /**
     * Menampilkan halaman detail komprehensif dari seorang pasien.
     */
    public function show(Request $request, $id)
    {
        $filterMonth = $request->input('month');
        $filterYear = $request->input('year', now()->year);

        $patient = Pasien::with([
            'user', 
            'master', 
            'kepatuhan' => function($query) use ($filterMonth, $filterYear) {
                if ($filterMonth) {
                    $query->whereMonth('last_update', $filterMonth)
                          ->whereYear('last_update', $filterYear);
                }
                $query->latest('last_update');
            }, 
            'diaryHarian' => function($query) use ($filterMonth, $filterYear) {
                if ($filterMonth) {
                    $query->whereMonth('tanggal', $filterMonth)
                          ->whereYear('tanggal', $filterYear);
                }
                $query->latest('tanggal');
            }, 
            'refillObat' => function($query) {
                $query->latest();
            }
        ])->findOrFail($id);
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Perhitungan target hari dinamis (bukan statis sebulan penuh)
        $targetDays = now()->day;
        if ($patient->created_at && $patient->created_at->month == $currentMonth && $patient->created_at->year == $currentYear) {
            $targetDays = now()->day - $patient->created_at->day + 1;
        }
        $targetDays = max(1, $targetDays); // Hindari pembagian dengan nol

        $diminumCount = $patient->kepatuhan()
            ->whereIn('status', ['diminum', 'tepat waktu', 'hijau'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();
        
        $adherenceRate = round(($diminumCount / $targetDays) * 100);
        $adherenceRate = min(100, $adherenceRate); // Maksimal 100%

        $terlewatCount = $patient->kepatuhan()
            ->whereIn('status', ['terlewat', 'tunda'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();

        $statusWarna = 'merah';
        if ($terlewatCount == 0) {
            $statusWarna = 'hijau';
        } elseif ($terlewatCount <= 2) {
            $statusWarna = 'kuning';
        }

        $riwayatRegimen = RiwayatRegimenPasien::with('masterObat', 'nakes')
            ->where('pasien_id', $patient->user_id)
            ->orderBy('tanggal_mulai', 'desc')
            ->get();
            
        $riwayatIo = RiwayatIo::with('masterIo', 'nakes')
            ->where('pasien_id', $patient->user_id)
            ->orderBy('tanggal_diagnosis', 'desc')
            ->get();
            
        $masterObats = MasterObat::where('status_aktif', true)->get();
        $masterIos = MasterIo::where('status_aktif', true)->get();

        return view('admin.pasien_detail', compact('patient', 'adherenceRate', 'diminumCount', 'statusWarna', 'filterMonth', 'filterYear', 'targetDays', 'riwayatRegimen', 'riwayatIo', 'masterObats', 'masterIos'));
    }

    /**
     * Menambahkan data pasien baru (Akun + Profil).
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username|max:255',
            'password' => 'required|string|min:6',
            'tanggal_lahir' => 'required|date',
            'no_telepon' => 'required|string|max:20',
            'no_rekam_medis' => 'nullable|string|max:50',
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat Akun User untuk Login
            $user = User::create([
                'nama' => $request->nama_lengkap,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'pasien',
                'status_akun' => 'aktif'
            ]);

            // 2. Buat Data Master Pasien
            $master = \App\Models\PasienMaster::create([
                'no_reg_hiv' => $request->no_rekam_medis ?? ('REG-' . time()),
                'nama' => $request->nama_lengkap,
                'tgl_lahir' => $request->tanggal_lahir,
                'is_registered' => true
            ]);

            // 3. Buat Relasi Profil Pasien
            Pasien::create([
                'user_id' => $user->id,
                'pasien_master_id' => $master->id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Data Pasien berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan pasien: ' . $e->getMessage());
        }
    }

    /**
     * Menonaktifkan akun pasien (Soft Delete atau Update Status).
     */
    public function toggleStatus($id)
    {
        try {
            DB::beginTransaction();
            
            $patient = Pasien::findOrFail($id);
            $user = User::findOrFail($patient->user_id);
            
            // Toggle status aktif (1: Aktif, 0: Non-aktif)
            $user->is_active = !$user->is_active;
            $user->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => 'Status pasien berhasil diperbarui!',
                'new_status' => $user->is_active
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function storeRiwayatRegimen(Request $request, $id)
    {
        $request->validate([
            'master_obat_id' => 'required|exists:master_obats,id',
            'tanggal_mulai' => 'required|date',
            'alasan_ganti' => 'nullable|string'
        ]);

        $patient = Pasien::findOrFail($id);

        try {
            DB::transaction(function () use ($request, $patient) {
                // Auto-close regimen lama
                $activeRegimen = RiwayatRegimenPasien::where('pasien_id', $patient->user_id)->whereNull('tanggal_selesai')->first();
                if ($activeRegimen) {
                    $activeRegimen->update([
                        'tanggal_selesai' => $request->tanggal_mulai,
                        'alasan_ganti' => $request->alasan_ganti ?? 'Diganti dengan regimen baru'
                    ]);
                }

                RiwayatRegimenPasien::create([
                    'pasien_id' => $patient->user_id,
                    'master_obat_id' => $request->master_obat_id,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'ditetapkan_oleh' => auth()->id(),
                    'alasan_ganti' => $request->alasan_ganti
                ]);
            });
            return redirect()->back()->with('success', 'Riwayat Regimen berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui regimen: ' . $e->getMessage());
        }
    }

    public function storeRiwayatIo(Request $request, $id)
    {
        $request->validate([
            'master_io_id' => 'required|exists:master_ios,id',
            'tanggal_diagnosis' => 'required|date',
            'status' => 'required|in:aktif,sembuh',
            'tanggal_sembuh' => 'nullable|date',
            'catatan' => 'nullable|string'
        ]);

        $patient = Pasien::findOrFail($id);

        RiwayatIo::create([
            'pasien_id' => $patient->user_id,
            'master_io_id' => $request->master_io_id,
            'tanggal_diagnosis' => $request->tanggal_diagnosis,
            'status' => $request->status,
            'tanggal_sembuh' => $request->status === 'sembuh' ? $request->tanggal_sembuh : null,
            'catatan' => $request->catatan,
            'ditetapkan_oleh' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Riwayat IO berhasil ditambahkan.');
    }
}