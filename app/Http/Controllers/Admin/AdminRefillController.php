<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RefillObat;
use App\Models\Pasien;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminRefillController extends Controller
{
    /**
     * Menampilkan halaman monitoring refill ARV (FR-A03).
     * Mendukung filter berdasarkan status dan tanggal.
     */
    public function index(Request $request)
    {
        // Query utama: semua pengajuan refill dengan relasi pasien
        $query = RefillObat::with(['pasien.master', 'pasien.user']);

        // Filter: Status refill
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter: Tanggal refill (bulan tertentu)
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_refill', Carbon::parse($request->bulan)->month)
                  ->whereYear('tanggal_refill', Carbon::parse($request->bulan)->year);
        }

        // Urutkan: yang menunggu di atas, lalu disetujui, terakhir selesai
        $requests = $query->orderByRaw("FIELD(status, 'menunggu', 'disetujui', 'selesai')")
                          ->orderBy('tanggal_refill', 'asc')
                          ->get();

        // Hitung pasien yang mendekati jadwal refill (H-3)
        $h3Date = Carbon::now()->addDays(3)->toDateString();
        $today = Carbon::now()->toDateString();
        $upcomingCount = RefillObat::where('status', 'menunggu')
                        ->whereBetween('tanggal_refill', [$today, $h3Date])
                        ->count();

        return view('admin.refill', compact('requests', 'upcomingCount'));
    }

    /**
     * Memperbarui status pengajuan refill.
     * Menunggu → Disetujui → Selesai
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:menunggu,disetujui,selesai',
        ]);

        $refill = RefillObat::findOrFail($id);
        $newStatus = $request->status;

        $updateData = ['status' => $newStatus];

        // Jika disetujui, catat admin dan tanggal
        if ($newStatus === 'disetujui') {
            $updateData['admin_id'] = auth()->id();
        }

        // Jika selesai (obat sudah diambil), catat tanggal pengambilan
        if ($newStatus === 'selesai') {
            $updateData['tanggal_diambil'] = now()->toDateString();
            $updateData['admin_id'] = auth()->id();
        }

        $refill->update($updateData);

        // Kirim notifikasi ke pasien
        $messages = [
            'disetujui' => 'Pengajuan refill ARV Anda telah disetujui. Silakan ambil obat di apotek sesuai jadwal.',
            'selesai' => 'Refill ARV Anda telah dicatat selesai. Terima kasih!',
        ];

        if (isset($messages[$newStatus]) && $refill->pasien) {
            Notifikasi::create([
                'user_id' => $refill->pasien->user_id,
                'judul' => 'Update Status Refill ARV',
                'pesan' => $messages[$newStatus],
                'status' => 'unread'
            ]);
        }

        $statusLabel = ['menunggu' => 'Menunggu', 'disetujui' => 'Disetujui', 'selesai' => 'Selesai'];
        return redirect()->back()->with('success', 'Status refill berhasil diubah menjadi "' . ($statusLabel[$newStatus] ?? $newStatus) . '".');
    }
}