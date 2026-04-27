<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminBroadcastController extends Controller
{
    /**
     * Menampilkan halaman Broadcast beserta riwayat pesan (FR-A04).
     */
    public function index()
    {
        $broadcasts = Broadcast::with('admin')
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('admin.broadcast', compact('broadcasts'));
    }

    /**
     * Mengirim pesan broadcast ke seluruh pasien aktif (FR-A04).
     * 1. Simpan ke tabel broadcast (riwayat)
     * 2. Insert ke tabel notifikasi untuk setiap pasien aktif
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'pesan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // 1. Simpan riwayat broadcast
            $broadcast = Broadcast::create([
                'admin_id' => auth()->id() ?? 1,
                'judul' => $request->judul,
                'pesan' => $request->pesan,
            ]);

            // 2. Ambil seluruh user pasien aktif
            $pasienAktif = User::where('role', 'pasien')
                               ->where('status_akun', 'aktif')
                               ->get();

            // 3. Mass insert ke tabel notifikasi
            $notifikasiData = [];
            $now = now();

            foreach ($pasienAktif as $user) {
                $notifikasiData[] = [
                    'user_id' => $user->id,
                    'judul' => $request->judul,
                    'pesan' => $request->pesan,
                    'status' => 'belum_dibaca',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Gunakan chunk insert agar efisien untuk jumlah pasien besar
            foreach (array_chunk($notifikasiData, 100) as $chunk) {
                Notifikasi::insert($chunk);
            }

            DB::commit();

            $jumlahPenerima = count($notifikasiData);
            return redirect()->back()->with('success', "Pesan broadcast berhasil dikirim ke {$jumlahPenerima} pasien aktif!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengirim broadcast: ' . $e->getMessage());
        }
    }
}
