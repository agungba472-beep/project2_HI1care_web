<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // Sesuaikan dengan path file HTML login Anda
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if ($user) {
            // FITUR FORCE LOGOUT: Hapus semua sesi lama pengguna ini sebelum login baru
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $user->id)
                ->delete();
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Validasi Role (RBAC) - Hanya admin yang boleh masuk dashboard web
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('admin/dashboard');
            }
            
            // Jika pasien/nakes mencoba login ke web admin, tolak aksesnya
            Auth::logout();
            return back()->withErrors([
                'username' => 'Akses ditolak. Anda bukan Administrator.',
            ]);
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}