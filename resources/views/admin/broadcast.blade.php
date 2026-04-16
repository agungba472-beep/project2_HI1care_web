@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-bullhorn header-icon"></i>
        <h1>Pesan Broadcast</h1>
        <p>Kirim informasi dan modul edukasi ke semua pasien</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="hi-alert hi-alert-success fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Form Buat Pesan --}}
        <div class="col-lg-4 fade-up">
            <div class="hi-card">
                <div class="hi-card-header">
                    <span><i class="fas fa-paper-plane"></i> Buat Pesan Baru</span>
                </div>
                <div class="hi-card-body">
                    <form action="{{ route('admin.broadcast.store') }}" method="POST" class="hi-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Judul Pesan</label>
                            <input type="text" class="form-control" name="judul" placeholder="Contoh: Jadwal Pengambilan Obat" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi Pesan / Edukasi</label>
                            <textarea class="form-control" name="pesan" rows="6" placeholder="Tuliskan pesan lengkap di sini..." required></textarea>
                        </div>
                        <button type="submit" class="hi-btn hi-btn-primary" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Kirim Broadcast
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Riwayat --}}
        <div class="col-lg-8 fade-up">
            <div class="hi-card">
                <div class="hi-card-header">
                    <span><i class="fas fa-history"></i> Riwayat Pesan Terkirim</span>
                    <span class="hi-badge hi-badge-info">{{ $broadcasts->count() }} pesan</span>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Judul Pesan</th>
                                <th>Cuplikan Isi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcasts as $b)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--success);"></div>
                                        <span style="font-size: 0.82rem; color: var(--text-secondary);">
                                            {{ $b->created_at->format('d M Y') }}
                                            <span style="opacity: 0.6;">{{ $b->created_at->format('H:i') }}</span>
                                        </span>
                                    </div>
                                </td>
                                <td style="font-weight: 600;">{{ $b->judul }}</td>
                                <td style="color: var(--text-secondary); font-size: 0.82rem;">{{ Str::limit($b->pesan, 60, '...') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3">
                                    <div class="hi-empty">
                                        <i class="fas fa-paper-plane"></i>
                                        <p>Belum ada pesan broadcast terkirim</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection