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

    /**
     * Kompres & resize gambar cover sebelum disimpan, supaya loading di app
     * pasien lebih cepat (terutama yang jaringannya lambat). Pakai GD bawaan
     * PHP - tidak butuh composer package tambahan (Imagick/Intervention).
     * Lebar maksimal 1000px, dikonversi ke JPEG kualitas 75.
     */
    private function kompresGambar($file): string
    {
        $namaBaru = 'edukasi_covers/' . uniqid() . '.jpg';
        $tujuanFull = storage_path('app/public/' . $namaBaru);
        if (!is_dir(dirname($tujuanFull))) {
            mkdir(dirname($tujuanFull), 0755, true);
        }

        [$lebarAsli, $tinggiAsli, $tipe] = getimagesize($file->getRealPath());

        $sumber = match ($tipe) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => imagecreatefrompng($file->getRealPath()),
            default => null,
        };

        // Kalau format tidak dikenali GD, fallback simpan asli tanpa kompresi
        if (!$sumber) {
            $file->storeAs('edukasi_covers', basename($namaBaru), 'public');
            return $namaBaru;
        }

        $lebarBaru = min(1000, $lebarAsli);
        $tinggiBaru = (int) ($tinggiAsli * ($lebarBaru / $lebarAsli));

        $tujuanGambar = imagecreatetruecolor($lebarBaru, $tinggiBaru);
        // Latar putih dulu (untuk PNG transparan yang dikonversi ke JPEG)
        $putih = imagecolorallocate($tujuanGambar, 255, 255, 255);
        imagefill($tujuanGambar, 0, 0, $putih);
        imagecopyresampled($tujuanGambar, $sumber, 0, 0, 0, 0, $lebarBaru, $tinggiBaru, $lebarAsli, $tinggiAsli);

        imagejpeg($tujuanGambar, $tujuanFull, 75);
        imagedestroy($sumber);
        imagedestroy($tujuanGambar);

        return $namaBaru;
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
            $coverPath = $this->kompresGambar($request->file('cover'));
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
            $modul->cover = $this->kompresGambar($request->file('cover'));
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