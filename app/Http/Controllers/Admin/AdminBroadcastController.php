<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use Illuminate\Http\Request;

class AdminBroadcastController extends Controller
{
    // Menampilkan halaman Broadcast beserta riwayat pesan
    public function index()
    {
        // Mengambil data broadcast dari yang terbaru
        $broadcasts = Broadcast::orderBy('created_at', 'desc')->get();
        return view('admin.broadcast', compact('broadcasts'));
    }

    // Menyimpan pesan broadcast baru ke database
    public function store(Request $request)
{
    // 1. Validasi inputan form
    $request->validate([
        'judul' => 'required|string|max:255',
        'pesan' => 'required|string',
    ]);

    // 2. Simpan ke database dengan menyertakan admin_id
    Broadcast::create([
        // Mengambil ID admin yang sedang login. 
        // (Sensei tambahkan "?? 1" sebagai data cadangan agar tidak error jika kamu sedang mengetesnya tanpa login)
        'admin_id' => auth()->id() ?? 1, 
        
        'judul' => $request->judul,
        'pesan' => $request->pesan,
    ]);

    // 3. Kembali ke halaman sebelumnya dengan pesan sukses
    return redirect()->back()->with('success', 'Pesan Broadcast berhasil dikirim dan disimpan!');
}
}
