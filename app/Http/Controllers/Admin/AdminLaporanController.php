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

    // 1. FITUR EXCEL (Format Data Asli/CSV)
    public function exportExcel(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        $fileName = "Laporan_Kepatuhan_ODHA_" . date('Y-m-d') . ".csv"; 

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($dataLaporan) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan BOM agar Excel membaca karakter UTF-8 dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom
            $columns = ['No', 'No. Reg HIV', 'Nama Pasien', 'Tanggal Lahir', 'Status Kepatuhan', 'Siklus Refill'];
            fputcsv($file, $columns, ';'); // Menggunakan titik koma (;) standar format regional Indonesia

            // Baris data
            foreach ($dataLaporan as $index => $pasien) {
                $kepatuhan = strtoupper($pasien->status_kepatuhan ?? 'HIJAU');
                $lastRefill = $pasien->refill->sortByDesc('siklus_ke')->first();
                $nama = $pasien->master->nama ?? ($pasien->user->name ?? ($pasien->user->nama ?? '-'));
                $siklus = $lastRefill ? 'Siklus ke-' . $lastRefill->siklus_ke : 'Belum Pernah';
                
                $row = [
                    $index + 1,
                    $pasien->master->no_reg_hiv ?? '-',
                    $nama,
                    $pasien->master->tgl_lahir ?? '-',
                    $kepatuhan,
                    $siklus
                ];

                fputcsv($file, $row, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // 2. FITUR CETAK PRINT / PDF (Format Cetak Cantik)
    public function cetakPrint(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        $isWord = false; // Penanda bahwa ini dibuka di Browser/PDF
        return view('admin.laporan_cetak', compact('dataLaporan', 'isWord'));
    }

    // 3. FITUR UNDUH WORD (Format Ringan Khusus Microsoft Word)
    public function exportWord(Request $request)
    {
        $dataLaporan = Pasien::with(['master', 'user', 'refill'])->orderBy('created_at', 'desc')->get();
        $fileName = "Laporan_Kepatuhan_ODHA_" . date('Y-m-d') . ".doc";
        
        header("Content-type: application/vnd.ms-word");
        header("Content-Disposition: attachment;Filename={$fileName}");
        
        $isWord = true; // Penanda bahwa ini diunduh sebagai Word
        return view('admin.laporan_cetak', compact('dataLaporan', 'isWord'));
    }

    // 4. FITUR CETAK REKAM MEDIS (PDF PER PASIEN)
    public function cetakDetailPasien($id)
    {
        $patient = Pasien::with(['user', 'master', 'kepatuhan', 'diaryHarian', 'refillObat'])->findOrFail($id);
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;

        $diminumCount = $patient->kepatuhan()
            ->whereIn('status', ['diminum', 'tepat waktu', 'hijau'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();
        
        $adherenceRate = round(($diminumCount / $daysInMonth) * 100);

        $terlewatCount = $patient->kepatuhan()
            ->whereIn('status', ['terlewat', 'tunda'])
            ->whereMonth('last_update', $currentMonth)
            ->whereYear('last_update', $currentYear)
            ->count();

        $statusWarna = 'merah';
        if ($terlewatCount == 0) {
            $statusWarna = 'hijau';
        } elseif ($terlewatCount <= 2) {
            $statusWarna = 'kuning';
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan_cetak_detail', compact('patient', 'adherenceRate', 'diminumCount', 'statusWarna'));
        
        return $pdf->stream('Rekam_Medis_' . ($patient->master->nama ?? 'Pasien') . '.pdf');
    }
}