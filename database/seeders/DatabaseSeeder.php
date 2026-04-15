<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Wajib dipanggil untuk enkripsi password

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Data User untuk Administrator
        $userAdmin = User::create([
            'nama' => 'Administrator Utama',
            'username' => 'admin',
            'password' => Hash::make('admin123'), // Password akan dienkripsi
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);

        // 2. Hubungkan data User di atas ke dalam tabel Admin
        Admin::create([
            'user_id' => $userAdmin->id,
        ]);

        // -------------------------------------------------------------
        // (Opsional) Kita buatkan juga 1 dummy Pasien yang butuh verifikasi
        // agar grafik di dashboard Anda nanti tidak kosong!
        // -------------------------------------------------------------
        $userPasien = User::create([
            'nama' => 'Budi Santoso',
            'username' => 'budi_pasien',
            'password' => Hash::make('pasien123'),
            'role' => 'pasien',
            'status_akun' => 'pending', // Menunggu verifikasi admin
        ]);

        // Karena pasien butuh relasi ke PasienMaster, kita skip dulu detailnya
        // atau kita buatkan jika Anda sudah setup PasienMaster.
        // Untuk sekarang, fokus ke Admin dulu agar bisa tembus login!
    }
}