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
    Schema::create('broadcast', function (Blueprint $table) {
        $table->id();
        $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
        $table->string('judul'); // Kolom yang tadi kita tambahkan
        $table->text('pesan');
        
        // Baris ini SANGAT PENTING untuk mengatasi error "created_at"
        $table->timestamps(); 
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broadcast');
    }
};
