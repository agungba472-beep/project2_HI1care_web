<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Nakes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
        // Pastikan nama view sesuai dengan resources/views/admin/users.blade.php
    }

    public function indexNakes()
    {
        // Mengambil user dengan role 'nakes' beserta detail profilnya
        $nakesList = Nakes::with('user')->get();
        return view('admin.nakes', compact('nakesList'));
    }

    public function storeNakes(Request $request)
    {
        $request->validate([
            'nama_nakes' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'spesialisasi' => 'required',
            'password' => 'required|min:8'
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Buat Akun User
            $user = User::create([
                'name' => $request->nama_nakes,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'nakes',
                'is_active' => true
            ]);

            // 2. Buat Profil Nakes
            Nakes::create([
                'user_id' => $user->id,
                'nama_nakes' => $request->nama_nakes,
                'spesialisasi' => $request->spesialisasi,
                'no_sip' => $request->no_sip, // Surat Izin Praktik
                'no_telepon' => $request->no_telepon,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tenaga Kesehatan berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftarkan Nakes: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'aktif']);

        // Logika tambahan: Kirim notifikasi ke email/aplikasi jika diperlukan
        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diverifikasi.');
    }
}