<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Nakes;
use App\Models\PasienMaster;
use App\Models\Pasien;
use App\Models\RefillObat;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Admin
        $adminUser = User::create([
            'nama' => 'Admin Puskesmas', 
            'username' => 'admin', 
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);
        Admin::create(['user_id' => $adminUser->id]);

        // 2. Buat Akun Nakes
        $nakesUser = User::create([
            'nama' => 'dr. Salsa Amelia',
            'username' => 'nakes1',
            'password' => Hash::make('password123'),
            'role' => 'nakes',
            'status_akun' => 'aktif',
        ]);
        Nakes::create([
            'user_id' => $nakesUser->id,
            'nama'    => $nakesUser->nama,
            'profesi' => 'Dokter Umum',
            'no_sip'  => 'SIP-123456789',
            'no_hp'   => '081234567890'
        ]);

        // 3. Buat Data Master Pasien
        $master1 = PasienMaster::create(['no_reg_hiv' => 'HIV-001', 'nama' => 'Budi Santoso', 'is_registered' => true]);
        $master2 = PasienMaster::create(['no_reg_hiv' => 'HIV-002', 'nama' => 'Siti Aminah', 'is_registered' => true]);
        $master3 = PasienMaster::create(['no_reg_hiv' => 'HIV-003', 'nama' => 'Agung Hidayat', 'is_registered' => true]);
        $master4 = PasienMaster::create(['no_reg_hiv' => 'HIV-004', 'nama' => 'Jaenal Arafat', 'is_registered' => false]);

        // 4. Buat Akun Pasien & Jadwal Refill Obat
        
        // Pasien 1: Patuh (Hijau), Refill masih lama
        $pasienUser1 = User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi001',
            'password' => Hash::make('password123'),
            'role' => 'pasien',
            'status_akun' => 'aktif',
        ]);
        $pasien1 = Pasien::create(['user_id' => $pasienUser1->id, 'pasien_master_id' => $master1->id, 'status_kepatuhan' => 'hijau']);
        RefillObat::create([
            'pasien_id' => $pasien1->id, 
            'tanggal_refill' => Carbon::now()->addDays(15)->toDateString(),
            'siklus_ke' => 1,
            'status' => 'menunggu'
        ]);

        // Pasien 2: Patuh (Hijau), Refill H-2
        $pasienUser2 = User::create([
            'nama' => 'Siti Aminah',
            'username' => 'siti002',
            'password' => Hash::make('password123'),
            'role' => 'pasien',
            'status_akun' => 'aktif',
        ]);
        $pasien2 = Pasien::create(['user_id' => $pasienUser2->id, 'pasien_master_id' => $master2->id, 'status_kepatuhan' => 'hijau']);
        RefillObat::create([
            'pasien_id' => $pasien2->id, 
            'tanggal_refill' => Carbon::now()->addDays(2)->toDateString(),
            'siklus_ke' => 3,
            'status' => 'menunggu'
        ]);

        // Pasien 3: Risiko Tinggi (Merah)
        $pasienUser3 = User::create([
            'nama' => 'Agung Hidayat',
            'username' => 'agung003',
            'password' => Hash::make('password123'),
            'role' => 'pasien',
            'status_akun' => 'pending',
        ]);
        $pasien3 = Pasien::create(['user_id' => $pasienUser3->id, 'pasien_master_id' => $master3->id, 'status_kepatuhan' => 'merah']);
        RefillObat::create([
            'pasien_id' => $pasien3->id, 
            'tanggal_refill' => Carbon::now()->subDays(1)->toDateString(), // Terlambat 1 hari
            'siklus_ke' => 5,
            'status' => 'menunggu'
        ]);
    }
}