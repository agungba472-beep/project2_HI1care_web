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
        Schema::create('pasien_master', function (Blueprint $table) {
            $table->id();
            $table->string('no_reg_hiv')->unique();
            $table->string('nama');
            $table->date('tgl_lahir')->nullable();
            $table->string('alamat')->nullable();
            $table->boolean('is_registered')->default(false); // sudah daftar app atau belum
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien_master');
    }
};
