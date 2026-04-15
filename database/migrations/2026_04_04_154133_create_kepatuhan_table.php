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
        Schema::create('kepatuhan', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pasien_id')->constrained('pasien')->cascadeOnDelete();
        $table->enum('status', ['hijau','kuning','merah']);
        $table->timestamp('last_update')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kepatuhan');
    }
};
