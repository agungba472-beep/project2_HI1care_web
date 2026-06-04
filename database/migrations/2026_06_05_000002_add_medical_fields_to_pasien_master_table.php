<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pasien_master', function (Blueprint $table) {
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->decimal('berat_badan', 5, 2)->nullable();
            $table->decimal('tinggi_badan', 5, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pasien_master', function (Blueprint $table) {
            $table->dropColumn(['jenis_kelamin', 'berat_badan', 'tinggi_badan']);
        });
    }
};
