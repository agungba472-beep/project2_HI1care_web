<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefillObat;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class AdminRefillController extends Controller
{
    public function index()
    {
        // Menampilkan antrean refill yang berstatus 'pending' terlebih dahulu
        $requests = RefillObat::with('pasien')
                    ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
                    ->get();
                    
        return view('admin.refill.index', compact('requests'));
    }

    public function approve($id)
    {
        $refill = RefillObat::findOrFail($id);
        $refill->update([
            'status' => 'approved',
            'tanggal_diambil' => now(),
            'admin_id' => auth()->id()
        ]);

        // Trigger Notifikasi ke Mobile Pasien
        Notifikasi::create([
            'user_id' => $refill->pasien->user_id,
            'title' => 'Permintaan Refill Disetujui',
            'message' => 'Silakan ambil stok obat ARV Anda di apotek RS sesuai jadwal.',
            'type' => 'refill_approved'
        ]);

        return response()->json(['success' => true]);
    }
}