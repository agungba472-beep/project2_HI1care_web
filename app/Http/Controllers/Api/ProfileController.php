<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * GET /api/user - Ambil data user yang sedang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Load relasi sesuai role
        if ($user->role === 'pasien') {
            $user->load('pasien.master');
        } elseif ($user->role === 'nakes') {
            $user->load('nakes');
        } elseif ($user->role === 'admin') {
            $user->load('admin');
        }

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * GET /api/profile - Ambil profil lengkap (legacy endpoint).
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        // Load relasi sesuai role
        if ($user->role === 'pasien') {
            $user->load('pasien.master');
        } elseif ($user->role === 'nakes') {
            $user->load('nakes');
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * POST /api/profile/update - Update profil user.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|unique:users,username,' . $user->id,
            'password' => 'sometimes|string|min:6|confirmed',
            'no_hp' => 'sometimes|string|max:20',
            'alamat' => 'sometimes|string|max:500',
            'tanggal_lahir' => 'sometimes|date',
            'jenis_kelamin' => 'sometimes|in:L,P',
            'berat_badan' => 'sometimes|numeric|min:1|max:300',
            'tinggi_badan' => 'sometimes|numeric|min:30|max:300',
        ]);

        $updateData = [];

        if ($request->has('nama')) {
            $updateData['nama'] = $request->nama;
        }

        if ($request->has('username')) {
            $updateData['username'] = $request->username;
        }

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if ($request->has('no_hp')) {
            $updateData['no_hp'] = $request->no_hp;
        }

        if (!empty($updateData)) {
            $user->update($updateData);
        }

        // Update pasien_master fields if user is pasien
        if ($user->role === 'pasien') {
            $masterData = [];

            if ($request->has('alamat')) {
                $masterData['alamat'] = $request->alamat;
            }
            if ($request->has('tanggal_lahir')) {
                $masterData['tgl_lahir'] = $request->tanggal_lahir;
            }
            if ($request->has('jenis_kelamin')) {
                $masterData['jenis_kelamin'] = $request->jenis_kelamin;
            }
            if ($request->has('berat_badan')) {
                $masterData['berat_badan'] = $request->berat_badan;
            }
            if ($request->has('tinggi_badan')) {
                $masterData['tinggi_badan'] = $request->tinggi_badan;
            }

            if (!empty($masterData) && $user->pasien && $user->pasien->master) {
                $user->pasien->master->update($masterData);
            }
        }

        // Reload relasi
        if ($user->role === 'pasien') {
            $user->load('pasien.master');
        } elseif ($user->role === 'nakes') {
            $user->load('nakes');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profil berhasil diperbarui',
            'data' => $user
        ]);
    }

    /**
     * POST /api/profile/photo - Upload foto profil.
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $user = $request->user();

        // Hapus foto lama jika ada
        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        // Simpan foto baru
        $path = $request->file('photo')->store('photos', 'public');

        $user->update(['photo' => $path]);

        return response()->json([
            'status' => 'success',
            'message' => 'Foto profil berhasil diperbarui',
            'photo_url' => $user->photo_url,
        ]);
    }
}
