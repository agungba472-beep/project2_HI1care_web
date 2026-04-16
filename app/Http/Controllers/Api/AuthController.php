<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Pasien;
use App\Models\PasienMaster;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // VALIDASI
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        // CEK USER
        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Username tidak ditemukan'
            ], 404);
        }

        // CEK PASSWORD (Cukup username dan password saja)
        if (!Auth::attempt([
            'username' => $request->username,
            'password' => $request->password
        ])) {
            return response()->json([
                'message' => 'Password salah'
            ], 401);
        }

        // CEK STATUS AKUN
        if ($user->status_akun !== 'aktif') {
            return response()->json([
                'message' => 'Akun Anda belum aktif atau ditangguhkan'
            ], 403);
        }

        // BUAT TOKEN
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'role' => $user->role
            ]
        ]);
    }

    public function registerPasien(Request $request)
    {
        // VALIDASI INPUT
        $request->validate([
            'no_reg_hiv' => 'required',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6'
        ]);

        // CEK NO REG DI MASTER
        $pasienMaster = PasienMaster::where('no_reg_hiv', $request->no_reg_hiv)->first();

        if (!$pasienMaster) {
            return response()->json([
                'message' => 'Nomor registrasi HIV tidak ditemukan. Pastikan Anda sudah terdaftar di Puskesmas.'
            ], 404);
        }

        // CEK SUDAH TERDAFTAR
        if ($pasienMaster->is_registered) {
            return response()->json([
                'message' => 'Nomor Registrasi ini sudah digunakan untuk membuat akun.'
            ], 400);
        }

        // BUAT USER
        $user = User::create([
            'nama' => $pasienMaster->nama,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'pasien',
            'status_akun' => 'aktif' // <--- UBAH JADI AKTIF (Karena data master sudah valid)
        ]);

        // BUAT DATA PASIEN
        Pasien::create([
            'user_id' => $user->id,
            'pasien_master_id' => $pasienMaster->id,
            // Jika di tabel pasien ada kolom nakes_id, bisa sekalian dipanggil di sini:
            // 'nakes_id' => $pasienMaster->nakes_id, 
            'status_kepatuhan' => 'hijau'
        ]);

        // UPDATE MASTER
        $pasienMaster->update([
            'is_registered' => true
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil! Akun Anda sudah aktif dan siap digunakan.'
        ], 201); // Gunakan 201 Created
    }
}