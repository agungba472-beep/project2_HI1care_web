<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModulEdukasi;
use Illuminate\Http\Request;

class AdminEdukasiController extends Controller
{
    public function index()
    {
        $moduls = ModulEdukasi::latest()->get();
        return view('admin.edukasi', compact('moduls'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Validasi gambar
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            // Simpan gambar ke storage/app/public/edukasi_covers
            $coverPath = $request->file('cover')->store('edukasi_covers', 'public');
        }

        ModulEdukasi::create([
            'judul' => $request->judul,
            'cover' => $coverPath,
            'konten' => $request->konten,
        ]);

        return redirect()->back()->with('success', 'Modul edukasi beserta cover berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $modul = ModulEdukasi::findOrFail($id);

        $modul->judul = $request->judul;
        $modul->konten = $request->konten;

        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($modul->cover && \Storage::disk('public')->exists($modul->cover)) {
                \Storage::disk('public')->delete($modul->cover);
            }
            $modul->cover = $request->file('cover')->store('edukasi_covers', 'public');
        }

        $modul->save();

        return redirect()->back()->with('success', 'Modul edukasi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        ModulEdukasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Modul edukasi berhasil dihapus!');
    }
}