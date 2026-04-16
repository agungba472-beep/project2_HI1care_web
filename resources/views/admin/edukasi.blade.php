@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-book-medical header-icon"></i>
        <h1>Manajemen Modul Edukasi</h1>
        <p>Kelola informasi dan artikel edukasi untuk pasien</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="hi-alert hi-alert-success fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Module List --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-book-open"></i> Daftar Modul Edukasi</span>
            <button type="button" class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#addEdukasiModal">
                <i class="fas fa-plus"></i> Tambah Modul
            </button>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Edukasi</th>
                        <th style="width: 160px;">Tanggal Dibuat</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($moduls as $index => $modul)
                    <tr>
                        <td>
                            <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $modul->judul }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.15rem;">
                                    {{ Str::limit($modul->konten ?? '', 80, '...') }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <span style="font-size: 0.82rem; color: var(--text-secondary);">
                                <i class="fas fa-calendar me-1" style="font-size: 0.7rem;"></i>
                                {{ $modul->created_at->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.edukasi.destroy', $modul->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus modul ini?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="hi-empty">
                                <i class="fas fa-book-open"></i>
                                <p>Belum ada modul edukasi. Klik tombol "Tambah Modul" untuk membuat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Tambah Edukasi --}}
<div class="modal fade hi-modal" id="addEdukasiModal" tabindex="-1" aria-labelledby="addEdukasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEdukasiModalLabel"><i class="fas fa-plus-circle me-2"></i>Tambah Modul Edukasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.edukasi.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Artikel / Edukasi</label>
                        <input type="text" class="form-control" id="judul" name="judul" required placeholder="Contoh: Pentingnya Minum ARV Tepat Waktu">
                    </div>
                    <div class="mb-3">
                        <label for="konten" class="form-label">Isi Konten</label>
                        <textarea class="form-control" id="konten" name="konten" rows="8" required placeholder="Tuliskan isi edukasi di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan Modul</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection