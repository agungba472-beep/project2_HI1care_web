<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\RefillObat;
use Illuminate\Http\Request;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        // Contoh Pengambilan Data Rekapitulasi (Card Ringkasan)
        $totalPasien = Pasien::count();
        $totalRefillSelesai = RefillObat::where('status', 'approved')->count(); // Sesuaikan status dengan database-mu
        
        // Mengambil data lengkap untuk tabel laporan 
        // (Bisa kamu tambahkan filter tanggal nantinya menggunakan $request)
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->get();

        return view('admin.laporan', compact('totalPasien', 'totalRefillSelesai', 'dataLaporan'));
    }
}
