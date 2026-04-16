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
        ]);

        ModulEdukasi::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            // Jika ada field 'penulis_id' atau 'admin_id' di database Anda, tambahkan di sini:
            // 'admin_id' => auth()->user()->id 
        ]);

        return redirect()->back()->with('success', 'Modul edukasi berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        ModulEdukasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Modul edukasi berhasil dihapus!');
    }
}