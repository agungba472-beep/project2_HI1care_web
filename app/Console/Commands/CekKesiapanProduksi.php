<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Command diagnostik untuk memastikan konfigurasi production sudah aman
 * sebelum di-launch ke banyak user. Jalankan via SSH:
 *   php artisan app:cek-kesiapan
 * Atau kalau tidak ada akses SSH di Hostinger, pakai route /diagnostik
 * yang dibuat di routes/web.php (dilindungi token rahasia).
 */
class CekKesiapanProduksi extends Command
{
    protected $signature = 'app:cek-kesiapan';
    protected $description = 'Cek konfigurasi production yang rawan bermasalah sebelum launch ke banyak user';

    public function handle(): int
    {
        $hasil = self::jalankanSemuaCek();

        $adaMasalah = false;
        foreach ($hasil as $item) {
            $ikon = $item['status'] === 'ok' ? '✅' : ($item['status'] === 'peringatan' ? '⚠️ ' : '❌');
            $this->line("{$ikon} {$item['nama']}: {$item['pesan']}");
            if ($item['status'] !== 'ok') {
                $adaMasalah = true;
            }
        }

        $this->newLine();
        if ($adaMasalah) {
            $this->warn('Ada beberapa hal yang perlu diperbaiki sebelum launch ke 100 user. Lihat detail di atas.');
        } else {
            $this->info('Semua pengecekan dasar lolos. Tetap pantau Pusher dashboard & resource Hostinger setelah launch.');
        }

        return self::SUCCESS;
    }

    /**
     * Logic pengecekan dipisah jadi static method supaya bisa dipakai ulang
     * baik dari command (SSH) maupun dari route web (tanpa SSH).
     */
    public static function jalankanSemuaCek(): array
    {
        $hasil = [];

        // 1. Cek driver database - SQLite berisiko "database is locked" kalau
        // banyak user nulis data bersamaan (misal centang minum obat serentak).
        $driverDb = config('database.default');
        $hasil[] = [
            'nama' => 'Driver Database',
            'status' => $driverDb === 'sqlite' ? 'error' : 'ok',
            'pesan' => $driverDb === 'sqlite'
                ? "Masih pakai SQLite ('{$driverDb}'). WAJIB ganti ke mysql di .env sebelum launch ke banyak user - SQLite mengunci seluruh file saat ada yang menulis data, rawan error 'database is locked' kalau user serentak."
                : "Pakai '{$driverDb}' - aman untuk banyak user bersamaan.",
        ];

        // 2. Cek koneksi database benar-benar bisa connect
        try {
            DB::connection()->getPdo();
            $hasil[] = [
                'nama' => 'Koneksi Database',
                'status' => 'ok',
                'pesan' => 'Berhasil terhubung ke database.',
            ];
        } catch (\Throwable $e) {
            $hasil[] = [
                'nama' => 'Koneksi Database',
                'status' => 'error',
                'pesan' => 'Gagal terhubung ke database: ' . $e->getMessage(),
            ];
        }

        // 3. Cek queue connection - kalau 'database' atau 'redis', butuh worker
        // yang jalan terus (queue:work) atau scheduler cron. Kalau tidak ada
        // yang menjalankannya, notifikasi WhatsApp/broadcast bisa nyangkut
        // tak terkirim selamanya.
        $driverQueue = config('queue.default');
        $hasil[] = [
            'nama' => 'Driver Queue',
            'status' => in_array($driverQueue, ['database', 'redis']) ? 'peringatan' : 'ok',
            'pesan' => in_array($driverQueue, ['database', 'redis'])
                ? "Pakai '{$driverQueue}' - pastikan cron job Hostinger untuk 'php artisan schedule:run' sudah aktif (lihat routes/console.php), kalau tidak, job antrean (WhatsApp/broadcast) bisa tidak pernah terkirim."
                : "Pakai '{$driverQueue}' - job dieksekusi langsung, tidak butuh worker terpisah.",
        ];

        // 4. Cek token Fonnte tersedia (tanpa expose isi tokennya di output)
        $adaTokenFonnte = filled(env('FONNTE_TOKEN'));
        $hasil[] = [
            'nama' => 'Token Fonnte',
            'status' => $adaTokenFonnte ? 'ok' : 'error',
            'pesan' => $adaTokenFonnte
                ? 'FONNTE_TOKEN terisi di .env.'
                : 'FONNTE_TOKEN kosong/belum diset di .env - notifikasi WhatsApp tidak akan terkirim.',
        ];

        // 5. Cek APP_DEBUG - wajib false di production (kalau true, error
        // detail termasuk query & path server bisa kelihatan publik).
        $appDebug = config('app.debug');
        $hasil[] = [
            'nama' => 'APP_DEBUG',
            'status' => $appDebug ? 'error' : 'ok',
            'pesan' => $appDebug
                ? 'APP_DEBUG masih true - WAJIB false di production, kalau tidak informasi sensitif server bisa bocor ke publik saat error.'
                : 'APP_DEBUG sudah false - aman.',
        ];

        // 6. Cek folder storage bisa ditulis (upload gambar edukasi dkk)
        try {
            $bisaTulis = Storage::disk('public')->put('cek_kesiapan_tmp.txt', 'ok');
            if ($bisaTulis) {
                Storage::disk('public')->delete('cek_kesiapan_tmp.txt');
            }
            $hasil[] = [
                'nama' => 'Storage Public Writable',
                'status' => $bisaTulis ? 'ok' : 'error',
                'pesan' => $bisaTulis ? 'Folder storage/app/public bisa ditulis.' : 'Folder storage/app/public TIDAK bisa ditulis - upload gambar akan gagal.',
            ];
        } catch (\Throwable $e) {
            $hasil[] = [
                'nama' => 'Storage Public Writable',
                'status' => 'error',
                'pesan' => 'Gagal cek storage: ' . $e->getMessage(),
            ];
        }

        // 7. Cek konfigurasi Pusher terisi (kalau dipakai untuk broadcasting)
        $broadcastDriver = config('broadcasting.default');
        if ($broadcastDriver === 'pusher') {
            $pusherLengkap = filled(config('broadcasting.connections.pusher.key'))
                && filled(config('broadcasting.connections.pusher.secret'));
            $hasil[] = [
                'nama' => 'Konfigurasi Pusher',
                'status' => $pusherLengkap ? 'ok' : 'error',
                'pesan' => $pusherLengkap
                    ? 'PUSHER_APP_KEY & PUSHER_APP_SECRET terisi.'
                    : 'PUSHER_APP_KEY / PUSHER_APP_SECRET kosong - fitur chat real-time tidak akan berfungsi.',
            ];
        }

        return $hasil;
    }
}
