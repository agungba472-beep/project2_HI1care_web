<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalNakes;
use App\Models\Nakes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminJadwalController extends Controller
{
    /**
     * Menampilkan daftar jadwal praktik tenaga kesehatan.
     */
    public function index()
    {
        $jadwals = JadwalNakes::with('nakes')
                    ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
                    ->orderBy('jam_mulai')
                    ->get();

        $nakesList = Nakes::orderBy('nama')->get();

        return view('admin.jadwal', compact('jadwals', 'nakesList'));
    }

    /**
     * Menyimpan jadwal praktik baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nakes_id' => 'required|exists:nakes,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota_pasien' => 'required|integer|min:1|max:100',
        ]);

        try {
            DB::beginTransaction();

            JadwalNakes::create([
                'nakes_id' => $request->nakes_id,
                'hari' => $request->hari,
                'jam_mulai' => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'kuota_pasien' => $request->kuota_pasien,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Jadwal praktik berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus jadwal praktik.
     */
    public function destroy($id)
    {
        $jadwal = JadwalNakes::findOrFail($id);
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal praktik berhasil dihapus.');
    }
}
