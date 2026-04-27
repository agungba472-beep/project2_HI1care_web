<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('refill_obat', function (Blueprint $table) {
            // Tambah kolom baru
            $table->date('tanggal_diambil')->nullable()->after('tanggal_refill');
            $table->foreignId('admin_id')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });

        // Ubah enum status: tambah 'disetujui'
        DB::statement("ALTER TABLE refill_obat MODIFY COLUMN status ENUM('menunggu','disetujui','selesai') DEFAULT 'menunggu'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum ke semula
        DB::statement("ALTER TABLE refill_obat MODIFY COLUMN status ENUM('menunggu','selesai') DEFAULT 'menunggu'");

        Schema::table('refill_obat', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
            $table->dropColumn(['tanggal_diambil', 'admin_id']);
        });
    }
};
