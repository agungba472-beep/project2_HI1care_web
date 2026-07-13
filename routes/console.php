<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Proses antrean job (WhatsApp/broadcast) tiap menit. Dipanggil oleh cron
// job Hostinger ('php artisan schedule:run' tiap menit - lihat catatan
// setup di bawah). --stop-when-empty supaya proses berhenti sendiri kalau
// antrean kosong, cocok untuk shared hosting yang tidak mendukung proses
// PHP yang jalan terus-menerus (long-running worker).
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();

/**
 * CATATAN SETUP CRON DI HOSTINGER (wajib, kalau tidak, baris di atas
 * tidak akan pernah jalan otomatis):
 * 1. Buka hPanel -> Advanced -> Cron Jobs
 * 2. Tambah cron job baru, jadwal "Every Minute" (* * * * *)
 * 3. Command:
 *    cd /home/USERNAME/domains/DOMAINMU/public_html && php artisan schedule:run >> /dev/null 2>&1
 *    (sesuaikan path dengan lokasi project Laravel kamu yang sebenarnya)
 */
