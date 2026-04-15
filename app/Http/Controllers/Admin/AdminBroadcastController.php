<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Broadcast;
use App\Models\User;
use App\Models\Notifikasi;

class AdminBroadcastController extends Controller
{
    public function index()
    {
        return view('admin.broadcast');
    }

    public function send(Request $request)
    {
        $request->validate([
            'pesan' => 'required'
        ]);

        $admin = auth()->user()->admin;

        Broadcast::create([
            'admin_id' => $admin->id,
            'pesan' => $request->pesan
        ]);

        // kirim ke semua pasien
        $users = User::where('role', 'pasien')->get();

        foreach ($users as $user) {
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Pengumuman',
                'pesan' => $request->pesan
            ]);
        }

        return back()->with('success', 'Broadcast berhasil dikirim');
    }
}
