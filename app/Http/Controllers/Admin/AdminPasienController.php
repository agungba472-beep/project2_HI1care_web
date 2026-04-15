<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Pasien;

class AdminPasienController extends Controller
{
    public function index()
    {
        $pasien = Pasien::with('user', 'master')->get();

        return view('admin.pasien', compact('pasien'));
    }
}
