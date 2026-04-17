<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasienMaster;
use App\Models\Pasien;
use App\Models\Nakes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $nakes = Nakes::with('user')->get();
        $pasienMaster = PasienMaster::all();
        $users = User::all();

        return view('admin.users', compact('nakes', 'pasienMaster', 'users'));
    }

    public function indexNakes()
    {
        // Mengambil user dengan role 'nakes' beserta detail profilnya
        $nakesList = Nakes::with('user')->get();
        return view('admin.nakes', compact('nakesList'));
    }

    public function storeNakes(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|unique:users,username',
            'password' => 'required|min:6'
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Buat Akun User
            $user = User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'role' => 'nakes',
                'status_akun' => 'aktif'
            ]);

            // 2. Buat Profil Nakes
            Nakes::create([
                'user_id' => $user->id,
                'nama'    => $request->nama,
                'profesi' => $request->spesialisasi ?? 'Umum',
                'no_sip'  => $request->no_sip,
                'no_hp'   => $request->no_hp,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Tenaga Kesehatan berhasil didaftarkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mendaftarkan Nakes: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'aktif']);

        // Logika tambahan: Kirim notifikasi ke email/aplikasi jika diperlukan
        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' berhasil diverifikasi.');
    }

    public function storeMaster(Request $request)
    {
        $request->validate([
            'no_reg_hiv' => 'required|string|unique:pasien_master,no_reg_hiv',
            'nama' => 'required|string|max:255',
        ]);

        PasienMaster::create([
            'no_reg_hiv' => $request->no_reg_hiv,
            'nama' => $request->nama,
            'is_registered' => false,
        ]);

        return redirect()->back()->with('success', 'Data master pasien berhasil ditambahkan.');
    }
    public function destroy($id)
    {
        // 1. Cari data user berdasarkan ID
        $user = User::findOrFail($id);

        // 2. Hapus data berelasi (Opsional, tapi sangat disarankan jika database tidak otomatis Cascade)
        if ($user->role === 'nakes') {
            Nakes::where('user_id', $user->id)->delete();
        } elseif ($user->role === 'pasien') {
            Pasien::where('user_id', $user->id)->delete();
        }

        // 3. Hapus akun utamanya
        $user->delete();

        // 4. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Akun pengguna dan data terkait berhasil dihapus permanen!');
    }

    public function destroyMaster($id)
    {
        $master = PasienMaster::findOrFail($id);
        $master->delete();

        return redirect()->back()->with('success', 'Data master pasien berhasil dihapus.');
    }
}