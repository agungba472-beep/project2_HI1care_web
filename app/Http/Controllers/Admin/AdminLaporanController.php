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
        $totalPasien = Pasien::count();
        $totalRefillSelesai = RefillObat::where('status', 'selesai')->count(); 
        
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();

        return view('admin.laporan', compact('totalPasien', 'totalRefillSelesai', 'dataLaporan'));
    }

    // 1. FITUR EXCEL (CSV)
    public function exportExcel(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        $fileName = "Laporan_Kepatuhan_ODHA_" . date('Y-m-d') . ".csv";

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('No', 'No. Reg HIV', 'Nama Pasien', 'Tanggal Lahir', 'Status Kepatuhan', 'Siklus Refill Terakhir');

        $callback = function() use($dataLaporan, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($dataLaporan as $index => $pasien) {
                $lastRefill = $pasien->refill->sortByDesc('siklus_ke')->first();
                $row['No']  = $index + 1;
                $row['No. Reg HIV'] = $pasien->master->no_reg_hiv ?? '-';
                $row['Nama Pasien'] = $pasien->master->nama ?? ($pasien->user->nama ?? '-');
                $row['Tanggal Lahir'] = $pasien->master->tgl_lahir ?? '-';
                $row['Status Kepatuhan'] = strtoupper($pasien->status_kepatuhan ?? 'HIJAU');
                $row['Siklus Refill Terakhir'] = $lastRefill ? 'Siklus ke-' . $lastRefill->siklus_ke : 'Belum Pernah';

                fputcsv($file, array($row['No'], $row['No. Reg HIV'], $row['Nama Pasien'], $row['Tanggal Lahir'], $row['Status Kepatuhan'], $row['Siklus Refill Terakhir']));
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 2. FITUR CETAK PRINT / PDF
    public function cetakPrint(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        return view('admin.laporan_cetak', compact('dataLaporan'));
    }

    // 3. FITUR UNDUH WORD (.doc)
    public function exportWord(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        $fileName = "Laporan_Kepatuhan_ODHA_" . date('Y-m-d') . ".doc";
        
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename={$fileName}");
        
        return view('admin.laporan_cetak', compact('dataLaporan'));
    }
}