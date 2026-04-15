<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Pasien;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::count();
        $totalPasien = Pasien::count();
        $pending = User::where('status_akun', 'pending')->count();

        $hijau = Pasien::where('status_kepatuhan','hijau')->count();
        $kuning = Pasien::where('status_kepatuhan','kuning')->count();
        $merah = Pasien::where('status_kepatuhan','merah')->count();

        return view('admin.dashboard', compact(
            'totalUser',
            'totalPasien',
            'pending'
        ));
    }
}
