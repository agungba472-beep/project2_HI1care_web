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
        // 1. Query pasien dengan filter yang sama seperti halaman Monitoring Kepatuhan
        $query = Pasien::with(['user', 'master']);

        // Filter: Pencarian berdasarkan nama (via relasi master)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('master', function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%');
            });
        }

        // Filter: Status kepatuhan (exact match)
        if ($request->filled('status')) {
            $query->where('status_kepatuhan', $request->status);
        }

        $data = $query->orderBy('created_at', 'desc')->get();

        // 2. Tentukan nama file
        $fileName = 'Laporan_Kepatuhan_Pasien_' . date('Y-m-d') . '.csv';

        // 3. Header untuk browser agar mengenali file unduhan
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
            fputcsv($file, ['No', 'No Reg HIV', 'Nama Pasien', 'Status Kepatuhan', 'Tanggal Lahir', 'Alamat', 'Status Akun']);

            foreach ($data as $key => $row) {
                fputcsv($file, [
                    $key + 1,
                    $row->master->no_reg_hiv ?? '-',
                    $row->master->nama ?? ($row->user->nama ?? '-'),
                    ucfirst($row->status_kepatuhan ?? 'hijau'),
                    $row->master->tgl_lahir ?? '-',
                    $row->master->alamat ?? '-',
                    ($row->user && $row->user->is_active) ? 'Aktif' : 'Non-aktif',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
