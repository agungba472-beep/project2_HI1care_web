<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MasterObat;
use App\Models\MasterIo;

class AdminMasterKlinisController extends Controller
{
    // --- MASTER OBAT ---

    public function indexObat()
    {
        $obats = MasterObat::orderBy('kode_regimen')->get();
        return view('admin.master_obat', compact('obats'));
    }

    public function storeObat(Request $request)
    {
        $request->validate([
            'kode_regimen' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'kandungan'    => 'nullable|string',
        ]);

        MasterObat::create([
            'kode_regimen' => $request->kode_regimen,
            'nama_lengkap' => $request->nama_lengkap,
            'kandungan'    => $request->kandungan,
            'status_aktif' => true,
        ]);

        return redirect()->back()->with('success', 'Master Regimen Obat berhasil ditambahkan');
    }

    public function updateObat(Request $request, $id)
    {
        $request->validate([
            'kode_regimen' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:255',
            'kandungan'    => 'nullable|string',
            'status_aktif' => 'required|boolean',
        ]);

        $obat = MasterObat::findOrFail($id);
        $obat->update($request->only('kode_regimen', 'nama_lengkap', 'kandungan', 'status_aktif'));

        return redirect()->back()->with('success', 'Master Regimen Obat berhasil diperbarui');
    }

    // --- MASTER IO ---

    public function indexIo()
    {
        $ios = MasterIo::orderBy('nama_io')->get();
        return view('admin.master_io', compact('ios'));
    }

    public function storeIo(Request $request)
    {
        $request->validate([
            'nama_io'   => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        MasterIo::create([
            'nama_io'   => $request->nama_io,
            'deskripsi' => $request->deskripsi,
            'status_aktif' => true,
        ]);

        return redirect()->back()->with('success', 'Master Infeksi Oportunistik berhasil ditambahkan');
    }

    public function updateIo(Request $request, $id)
    {
        $request->validate([
            'nama_io'   => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'required|boolean',
        ]);

        $io = MasterIo::findOrFail($id);
        $io->update($request->only('nama_io', 'deskripsi', 'status_aktif'));

        return redirect()->back()->with('success', 'Master Infeksi Oportunistik berhasil diperbarui');
    }
}
