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
    public function exportExcel(Request $request)
{
    // 1. Ambil data dengan filter yang sama seperti di halaman index
    $query = \App\Models\Kepatuhan::with(['pasien.user', 'pasien.pasienMaster']);

    if ($request->filter_bulan) {
        $query->whereMonth('tanggal', date('m', strtotime($request->filter_bulan)))
              ->whereYear('tanggal', date('Y', strtotime($request->filter_bulan)));
    }

    $data = $query->get();

    // 2. Tentukan nama file
    $fileName = 'Laporan_Kepatuhan_ARV_' . date('Y-m-d') . '.csv';

    // 3. Header untuk browser agar mengenali ini sebagai file unduhan
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // 4. Proses pembuatan baris data
    $callback = function() use($data) {
        $file = fopen('php://output', 'w');
        
        // Header Kolom di Excel
        fputcsv($file, ['No', 'Tanggal', 'Nama Pasien', 'No Reg HIV', 'Status Minum Obat', 'Jam']);

        foreach ($data as $key => $row) {
            fputcsv($file, [
                $key + 1,
                $row->tanggal,
                $row->pasien->user->nama,
                $row->pasien->pasienMaster->no_reg_hiv,
                $row->status, // misal: Sudah Minum / Belum
                $row->created_at->format('H:i')
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}
