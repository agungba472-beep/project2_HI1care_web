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