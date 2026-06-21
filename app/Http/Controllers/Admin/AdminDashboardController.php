<?php
// app/Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
use App\Models\RefillObat;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Sinkronisasi status kepatuhan semua pasien berdasarkan bulan berjalan
        $patients = Pasien::all();
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;

        foreach ($patients as $patient) {
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

            $newStatus = 'merah';
            if ($terlewatCount == 0) {
                $newStatus = 'hijau';
            } elseif ($terlewatCount <= 2) {
                $newStatus = 'kuning';
            } else {
                $newStatus = 'merah';
            }

            if ($patient->status_kepatuhan !== $newStatus) {
                $patient->status_kepatuhan = $newStatus;
                $patient->save();
            }
        }

        // Statistik Ringkasan
        $stats = [
            'total_pasien' => Pasien::count(),
            'pending_verifikasi' => User::where('status_akun', 'pending')->count(),
            'kepatuhan_hijau' => Pasien::where('status_kepatuhan', 'hijau')->count(),
            'kepatuhan_kuning' => Pasien::where('status_kepatuhan', 'kuning')->count(),
            'kepatuhan_merah' => Pasien::where('status_kepatuhan', 'merah')->count(),
            'total_refill' => RefillObat::count(),
            'refill_pending' => RefillObat::where('status', 'pending')->count(),
            'total_broadcast' => Broadcast::count(),
        ];

        // JIKA PERMINTAAN DATANG DARI AJAX (REAL-TIME UPDATE)
        if ($request->ajax()) {
            return response()->json([
                'stats' => $stats
            ]);
        }

        // Data Grafik Tren Kepatuhan (Hanya untuk load pertama kali)
        $chartData = Pasien::select('status_kepatuhan', DB::raw('count(*) as total'))
            ->groupBy('status_kepatuhan')
            ->get();

        $recentPasien = Pasien::with('user', 'master')->latest()->take(5)->get();
        $recentRefill = RefillObat::with('pasien.user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'chartData', 'recentPasien', 'recentRefill'));
    }
}