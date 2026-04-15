<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pasien;

class AdminLaporanController extends Controller
{
    public function index()
    {
        $pasien = Pasien::with('user')->get();
        return view('admin.laporan', compact('pasien'));
    }
}
