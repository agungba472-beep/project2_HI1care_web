@extends('layouts.v_template')

@section('title', 'Broadcast Pesan - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-bullhorn header-icon"></i>
        <h1>Broadcast Pesan</h1>
        <p>Kirim pengumuman massal ke seluruh pasien aktif — informasi jadwal, ketersediaan obat, dan edukasi</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="hi-alert hi-alert-success fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="hi-alert hi-alert-danger fade-up">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- ==================== FORM KIRIM BROADCAST ==================== --}}
        <div class="col-lg-4 fade-up">
            <div class="hi-card" style="position: sticky; top: 1rem;">
                <div class="hi-card-header">
                    <span><i class="fas fa-paper-plane"></i> Buat Pesan Baru</span>
                </div>
                <div class="hi-card-body">
                    <form action="{{ route('admin.broadcast.store') }}" method="POST" class="hi-form" id="broadcastForm">
                        @csrf

                        {{-- Info Banner --}}
                        <div style="background: linear-gradient(135deg, #dbeafe, #eff6ff); border: 1px solid #93c5fd; border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.25rem;">
                            <div style="font-size: 0.78rem; color: #1e40af; font-weight: 600;">
                                <i class="fas fa-info-circle me-1"></i> Informasi
                            </div>
                            <div style="font-size: 0.72rem; color: #3b82f6; margin-top: 0.2rem;">
                                Pesan ini akan dikirim sebagai notifikasi ke <strong>seluruh pasien</strong> yang berstatus akun aktif.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul Pesan</label>
                            <input type="text" class="form-control" name="judul" placeholder="Contoh: Jadwal Pengambilan Obat Bulan Mei" required value="{{ old('judul') }}">
                            @error('judul')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Isi Pesan / Pengumuman</label>
                            <textarea class="form-control" name="pesan" rows="6" placeholder="Tuliskan pesan lengkap yang akan diterima pasien di notifikasi mereka..." required>{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="hi-btn hi-btn-primary" style="width: 100%;" onclick="return confirm('Anda yakin ingin mengirim broadcast ini ke SELURUH pasien aktif?')">
                            <i class="fas fa-paper-plane"></i> Kirim Broadcast
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ==================== RIWAYAT BROADCAST ==================== --}}
        <div class="col-lg-8 fade-up" style="animation-delay: 0.1s;">
            <div class="hi-card">
                <div class="hi-card-header">
                    <span><i class="fas fa-history"></i> Riwayat Pesan Broadcast</span>
                    <span class="hi-badge hi-badge-info">{{ $broadcasts->count() }} pesan terkirim</span>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th>Tanggal Kirim</th>
                                <th>Judul Pesan</th>
                                <th>Isi Pesan</th>
                                <th>Pengirim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($broadcasts as $index => $b)
                            <tr>
                                <td>
                                    <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--success); flex-shrink: 0;"></div>
                                        <div>
                                            <div style="font-weight: 600; font-size: 0.82rem;">
                                                {{ $b->created_at->format('d M Y') }}
                                            </div>
                                            <div style="font-size: 0.7rem; color: var(--text-secondary);">
                                                {{ $b->created_at->format('H:i') }} WIB
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">
                                        {{ $b->judul }}
                                    </span>
                                </td>
                                <td style="max-width: 280px;">
                                    <div style="font-size: 0.82rem; color: var(--text-secondary); line-height: 1.5;">
                                        {{ Str::limit($b->pesan, 80, '...') }}
                                    </div>
                                    @if(strlen($b->pesan) > 80)
                                        <button class="btn btn-link p-0" style="font-size: 0.7rem; text-decoration: none; color: var(--primary);" type="button" data-bs-toggle="collapse" data-bs-target="#pesan-{{ $b->id }}">
                                            <i class="fas fa-chevron-down me-1"></i>Lihat selengkapnya
                                        </button>
                                        <div class="collapse" id="pesan-{{ $b->id }}">
                                            <div style="background: var(--surface); padding: 0.75rem; border-radius: 8px; margin-top: 0.5rem; font-size: 0.8rem; color: var(--text-primary); line-height: 1.6;">
                                                {!! nl2br(e($b->pesan)) !!}
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($b->admin)
                                        <div class="d-flex align-items-center gap-2">
                                            @php
                                                $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                                $aIni = strtoupper(substr($b->admin->nama ?? 'A', 0, 1));
                                                $aCol = $avatarColors[ord($aIni) % count($avatarColors)];
                                            @endphp
                                            <div class="hi-avatar" style="background:{{ $aCol }}; width: 28px; height: 28px; font-size: 0.65rem;">{{ $aIni }}</div>
                                            <div>
                                                <div style="font-weight: 600; font-size: 0.8rem;">{{ $b->admin->nama }}</div>
                                                <div style="font-size: 0.68rem; color: var(--text-secondary);">Admin</div>
                                            </div>
                                        </div>
                                    @else
                                        <span style="font-size: 0.78rem; color: var(--text-secondary);">Sistem</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="hi-empty">
                                        <i class="fas fa-paper-plane"></i>
                                        <p>Belum ada pesan broadcast yang pernah dikirim. Buat pesan pertama dari form di samping.</p>
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