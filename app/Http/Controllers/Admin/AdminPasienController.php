<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPasienController extends Controller
{
    /**
     * Menampilkan daftar seluruh pasien HIV yang terdaftar.
     */
    public function index()
    {
        // Eager load relasi 'user' untuk mengambil email/status akun
        $patients = Pasien::with('user')->orderBy('created_at', 'desc')->get();
        
        return view('admin.pasien', compact('patients'));
    }

    /**
     * Menampilkan halaman detail komprehensif dari seorang pasien.
     */
    public function show($id)
    {
        $patient = Pasien::with(['user', 'kepatuhan', 'diaryHarian', 'refillObat'])->findOrFail($id);
        
        // Kalkulasi persentase kepatuhan (contoh logika sederhana)
        $totalDoses = $patient->kepatuhan->count();
        $takenDoses = $patient->kepatuhan->where('status', 'diminum')->count();
        
        $adherenceRate = $totalDoses > 0 ? round(($takenDoses / $totalDoses) * 100, 2) : 0;

        return view('admin.pasien_detail', compact('patient', 'adherenceRate'));
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

            // 2. Buat Profil Pasien
            Pasien::create([
                'user_id' => $user->id,
                'nama_lengkap' => $request->nama_lengkap,
                'no_rekam_medis' => $request->no_rekam_medis,
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_telepon' => $request->no_telepon,
                'fase_pengobatan' => 'Inisiasi' // Nilai default
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
}