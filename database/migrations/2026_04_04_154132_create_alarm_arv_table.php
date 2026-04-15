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
        Schema::create('alarm_arv', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
        $table->time('waktu');
        $table->enum('status', ['belum','sudah'])->default('belum');
        $table->date('tanggal');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alarm_arv');
    }
};
