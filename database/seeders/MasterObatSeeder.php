<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('master_obats')->insert([
            ['kode_regimen' => 'TLD', 'nama_lengkap' => 'Tenofovir + Lamivudine + Dolutegravir', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['kode_regimen' => 'TLE', 'nama_lengkap' => 'Tenofovir + Lamivudine + Efavirenz', 'status_aktif' => 1, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}
