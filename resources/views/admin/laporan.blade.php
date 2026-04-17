@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-file-medical-alt header-icon"></i>
        <h1>Laporan & Rekapitulasi</h1>
        <p>Ringkasan data pasien HI!-CARE</p>
    </div>

    {{-- Stat Mini Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6 fade-up">
            <div class="hi-stat">
                <div class="hi-stat-accent" style="background: linear-gradient(180deg, var(--primary), var(--accent));"></div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="hi-stat-label">Total Pasien Terdaftar</div>
                        <div class="hi-stat-number" style="color: var(--primary);">{{ $totalPasien }}</div>
                    </div>
                    <div class="hi-stat-icon" style="background: rgba(8,145,178,0.1); color: var(--primary);">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 fade-up">
            <div class="hi-stat">
                <div class="hi-stat-accent" style="background: linear-gradient(180deg, #34d399, var(--success));"></div>
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="hi-stat-label">Total Refill Obat Selesai</div>
                        <div class="hi-stat-number" style="color: var(--success);">{{ $totalRefillSelesai }}</div>
                    </div>
                    <div class="hi-stat-icon" style="background: var(--success-light); color: var(--success);">
                        <i class="fas fa-pills"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-table"></i> Data Detail Pasien</span>
            <a href="{{ route('admin.laporan.export', ['filter_bulan' => request('filter_bulan')]) }}" class="hi-btn hi-btn-success hi-btn-sm">
                <i class="fas fa-file-excel"></i> Export ke Excel (.csv)
            </a>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th>No. Registrasi HIV</th>
                        <th>Nama Pasien</th>
                        <th>Status Kepatuhan</th>
                        <th>Tanggal Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataLaporan as $laporan)
                    <tr>
                        <td><span class="hi-code">{{ $laporan->master->no_reg_hiv ?? 'Belum ada' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                    $lIni = strtoupper(substr($laporan->user->nama ?? 'P', 0, 1));
                                    $lCol = $avatarColors[ord($lIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $lCol }}">{{ $lIni }}</div>
                                <span style="font-weight: 600;">{{ $laporan->user->nama ?? 'Data User Hilang' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($laporan->status_kepatuhan == 'hijau')
                                <span class="hi-badge hi-badge-success"><i class="fas fa-check-circle me-1"></i>Patuh</span>
                            @elseif($laporan->status_kepatuhan == 'kuning')
                                <span class="hi-badge hi-badge-warning"><i class="fas fa-exclamation-circle me-1"></i>Peringatan</span>
                            @elseif($laporan->status_kepatuhan == 'merah')
                                <span class="hi-badge hi-badge-danger"><i class="fas fa-times-circle me-1"></i>Drop-out</span>
                            @else
                                <span class="hi-badge hi-badge-muted">{{ strtoupper($laporan->status_kepatuhan ?? 'N/A') }}</span>
                            @endif
                        </td>
                        <td>
                            <span style="font-size: 0.82rem; color: var(--text-secondary);">
                                <i class="fas fa-calendar me-1" style="font-size: 0.7rem;"></i>
                                {{ $laporan->created_at->format('d M Y') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="hi-empty">
                                <i class="fas fa-chart-bar"></i>
                                <p>Belum ada data laporan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection