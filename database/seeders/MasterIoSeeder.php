<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterIoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('master_ios')->insert([
            ['nama_io' => 'Tuberkulosis (TB)', 'deskripsi' => 'Infeksi bakteri Mycobacterium tuberculosis', 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_io' => 'Kandidiasis', 'deskripsi' => 'Infeksi jamur pada mulut, tenggorokan, atau vagina', 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_io' => 'Pneumocystis Pneumonia (PCP)', 'deskripsi' => 'Infeksi paru-paru yang disebabkan oleh jamur', 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_io' => 'Toksoplasmosis', 'deskripsi' => 'Infeksi parasit pada otak atau mata', 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nama_io' => 'Herpes Zoster', 'deskripsi' => 'Cacar ular', 'status_aktif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
