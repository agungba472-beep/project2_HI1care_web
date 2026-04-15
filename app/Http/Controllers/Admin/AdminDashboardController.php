<?php
// app/Http/Controllers/Admin/AdminDashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use App\Models\User;
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
            'kepatuhan_merah' => Pasien::where('status_kepatuhan', 'merah')->count(),
        ];

        // Data Grafik Tren Kepatuhan (Contoh pengelompokan per bulan)
        $chartData = Pasien::select('status_kepatuhan', DB::raw('count(*) as total'))
            ->groupBy('status_kepatuhan')
            ->get();

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}