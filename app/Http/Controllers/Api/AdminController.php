<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notifikasi;
use App\Models\User;

class AdminController extends Controller
{
    public function approveUser($id)
    {
        $user = User::find($id);
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'message' => 'Akses ditolak'
            ], 403);
            }

        if (!$user) {
            return response()->json([
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->update([
            'status_akun' => 'aktif',
            'verified_at' => now()
        ]);

         Notifikasi::create([
        'user_id' => $user->id,
        'judul' => 'Akun Disetujui',
        'pesan' => 'Akun anda sudah aktif, silakan login'
    ]);

        return response()->json([
            'message' => 'User berhasil di-approve'
        ]);
    }
    public function rejectUser($id)
{
    $user = User::find($id);
    if (auth()->user()->role !== 'admin') {
    return response()->json([
        'message' => 'Akses ditolak'
    ], 403);
}
    if (!$user) {
        return response()->json([
            'message' => 'User tidak ditemukan'
        ], 404);
    }

    $user->update([
        'status_akun' => 'ditolak'
    ]);

    return response()->json([
        'message' => 'User ditolak'
    ]);
}
public function getPendingUsers()
{
    $users = User::where('status_akun', 'pending')->get();
    if (auth()->user()->role !== 'admin') {
    return response()->json([
        'message' => 'Akses ditolak'
    ], 403);
    }
    return response()->json($users);
}

}


