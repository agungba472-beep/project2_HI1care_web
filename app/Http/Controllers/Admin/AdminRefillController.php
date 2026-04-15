<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\RefillObat;

class AdminRefillController extends Controller
{
    public function index()
    {
        $refill = RefillObat::with('pasien.user')->get();

        return view('admin.refill', compact('refill'));
    }
}