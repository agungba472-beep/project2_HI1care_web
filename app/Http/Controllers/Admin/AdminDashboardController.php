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
    public function index()
    {
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

        // Data Grafik Tren Kepatuhan
        $chartData = Pasien::select('status_kepatuhan', DB::raw('count(*) as total'))
            ->groupBy('status_kepatuhan')
            ->get();

        // Pasien terbaru
        $recentPasien = Pasien::with('user', 'master')->latest()->take(5)->get();

        // Refill terbaru
        $recentRefill = RefillObat::with('pasien.user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'chartData', 'recentPasien', 'recentRefill'));
    }
}