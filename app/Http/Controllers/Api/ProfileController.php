<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function profile(Request $request)
    {
        $user = $request->user();

        //  load relasi sesuai role
        if ($user->role === 'pasien') {
            $user->load('pasien.master');
        } elseif ($user->role === 'nakes') {
            $user->load('nakes');
        }

        return response()->json([
            'user' => $user
        ]);
    }
}
