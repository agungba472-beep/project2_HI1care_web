<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom foto_bukti ke tabel kepatuhan dan refill_obat
     * agar sistem bisa menyimpan foto bukti minum obat dari HP pasien.
     */
    public function up(): void
    {
        Schema::table('kepatuhan', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->after('status');
        });

        Schema::table('refill_obat', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kepatuhan', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });

        Schema::table('refill_obat', function (Blueprint $table) {
            $table->dropColumn('foto_bukti');
        });
    }
};
