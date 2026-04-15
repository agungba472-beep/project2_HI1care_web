<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'pasien')->get();
        return view('admin.users', compact('users'));
    }

    public function approve($id)
    {
        $user = User::find($id);

        $user->update([
            'status_akun' => 'aktif',
            'verified_at' => now()
        ]);

        return back();
    }

    public function reject($id)
    {
        $user = User::find($id);

        $user->update([
            'status_akun' => 'ditolak'
        ]);

        return back();
    }
}
