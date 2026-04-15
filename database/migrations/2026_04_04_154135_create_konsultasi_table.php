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
        Schema::create('konsultasi', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pasien_id')->constrained('pasien');
        $table->foreignId('nakes_id')->constrained('nakes');
        $table->date('tanggal');
        $table->time('waktu');
        $table->enum('status', ['dijadwalkan','selesai','batal'])->default('dijadwalkan');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konsultasi');
    }
};
