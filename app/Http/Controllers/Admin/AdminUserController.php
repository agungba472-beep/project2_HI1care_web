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
        // Tab 1: Pendaftar baru yang menunggu verifikasi
        $pendingUsers = User::where('status_akun', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Tab 2: Data master pasien (whitelist)
        $pasienMaster = PasienMaster::orderBy('created_at', 'desc')->get();

        // Tab 3: Tenaga kesehatan
        $nakes = Nakes::with('user')->get();

        // Tab 4: Pasien aktif (yang sudah diverifikasi)
        $activePatients = Pasien::with(['user', 'master'])
                            ->whereHas('user', function ($q) {
                                $q->where('status_akun', 'aktif');
                            })
                            ->get();

        return view('admin.users', compact('pendingUsers', 'pasienMaster', 'nakes', 'activePatients'));
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

    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->update(['status_akun' => 'ditolak']);

        return redirect()->back()->with('success', 'Akun ' . $user->nama . ' telah ditolak.');
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
        // 1. JURUS ULTIMATE: Bius penjaga MySQL agar tidak rewel!
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

        $user = User::withTrashed()->findOrFail($id);
        
        if ($user->role === 'pasien') {
            // withTrashed() memastikan data yang sudah "soft delete" tetap tertangkap
            $pasien = Pasien::withTrashed()->where('user_id', $user->id)->first();
            
            if ($pasien) {
                $pasienId = $pasien->id;
                $masterId = $pasien->pasien_master_id;
                
                $konsultasiIds = \App\Models\Konsultasi::withTrashed()->where('pasien_id', $pasienId)->pluck('id');
                if ($konsultasiIds->isNotEmpty()) {
                    DB::table('chat')->whereIn('konsultasi_id', $konsultasiIds)->delete();
                }
                
                // Sapu bersih menggunakan forceDelete()
                \App\Models\Konsultasi::withTrashed()->where('pasien_id', $pasienId)->forceDelete();
                \App\Models\RefillObat::withTrashed()->where('pasien_id', $pasienId)->forceDelete();
                \App\Models\Kepatuhan::withTrashed()->where('pasien_id', $pasienId)->forceDelete();
                \App\Models\DiaryHarian::withTrashed()->where('pasien_id', $pasienId)->forceDelete();
                \App\Models\AlarmArv::withTrashed()->where('pasien_id', $pasienId)->forceDelete();

                $pasien->forceDelete();
                
                // if ($masterId) {
                //     \App\Models\PasienMaster::withTrashed()->where('id', $masterId)->forceDelete();
                // }
            }
        } 
        elseif ($user->role === 'nakes') {
            $nakes = Nakes::withTrashed()->where('user_id', $user->id)->first();
            
            if ($nakes) {
                $nakesId = $nakes->id;
                
                $konsultasiIds = \App\Models\Konsultasi::withTrashed()->where('nakes_id', $nakesId)->pluck('id');
                if ($konsultasiIds->isNotEmpty()) {
                    DB::table('chat')->whereIn('konsultasi_id', $konsultasiIds)->delete();
                }
                
                \App\Models\Konsultasi::withTrashed()->where('nakes_id', $nakesId)->forceDelete();
                \App\Models\JadwalNakes::where('nakes_id', $nakesId)->delete(); // Hapus jadwal nakes juga
                
                $nakes->forceDelete();
            }
        }

        // Hapus akun utama
        $user->forceDelete();

        // 2. Bangunkan kembali penjaga MySQL
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        return redirect()->back()->with('success', 'Akun berhasil dihapus total menggunakan Jurus Ultimate!');
    }
    public function destroyMaster($id)
    {
        $master = PasienMaster::findOrFail($id);
        
        // Cari semua pasien yang terhubung dengan master ini menggunakan DB Facade
        $pasiens = DB::table('pasien')->where('pasien_master_id', $master->id)->get();
        $pasienIds = $pasiens->pluck('id')->toArray();
        $userIds = $pasiens->pluck('user_id')->toArray();
        
        if (!empty($pasienIds)) {
            // Hapus Chat terkait konsultasi pasien ini
            $konsultasiIds = DB::table('konsultasi')->whereIn('pasien_id', $pasienIds)->pluck('id');
            if ($konsultasiIds->isNotEmpty()) {
                DB::table('chat')->whereIn('konsultasi_id', $konsultasiIds)->delete();
            }
            
            // Hapus semua data medis
            DB::table('konsultasi')->whereIn('pasien_id', $pasienIds)->delete();
            DB::table('refill_obat')->whereIn('pasien_id', $pasienIds)->delete();
            DB::table('kepatuhan')->whereIn('pasien_id', $pasienIds)->delete();
            DB::table('diary_harian')->whereIn('pasien_id', $pasienIds)->delete();
            DB::table('alarm_arv')->whereIn('pasien_id', $pasienIds)->delete();
            
            // Hapus relasi pasien
            DB::table('pasien')->whereIn('id', $pasienIds)->delete();
            
            // Hapus akun user yang menggunakan master ini agar tidak menjadi yatim piatu
            if (!empty($userIds)) {
                DB::table('users')->whereIn('id', $userIds)->delete();
            }
        }

        // Terakhir baru hapus masternya dengan aman
        $master->delete();

        return redirect()->back()->with('success', 'Data master pasien beserta seluruh riwayat dan akun terkait berhasil dibersihkan.');
    }
    public function resetPassword(Request $request, $id)
{
    // Validasi input password baru
    $request->validate([
        'password' => 'required|min:6|confirmed', // Harus diisi, minimal 6 karakter, dan cocok dengan field confirmation
    ]);

    // Cari user berdasarkan ID
    $user = User::findOrFail($id);
    
    // Update password baru yang sudah di-hash
    $user->update([
        'password' => Hash::make($request->password)
    ]);

    // Kembalikan ke halaman sebelumnya dengan pesan sukses
    return redirect()->back()->with('success', 'Password untuk ' . $user->nama . ' berhasil diperbarui!');
}
}
