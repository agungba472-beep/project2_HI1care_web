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
        Schema::create('riwayat_ios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('master_io_id')->constrained('master_ios')->onDelete('cascade');
            $table->date('tanggal_diagnosis');
            $table->enum('status', ['aktif', 'sembuh']);
            $table->date('tanggal_sembuh')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('ditetapkan_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_ios');
    }
};
