@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-capsules header-icon"></i>
        <h1>Monitoring Refill Obat</h1>
        <p>Pantau jadwal refill ARV dan kirim pengingat kepada pasien</p>
    </div>

    {{-- Refill Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-pills"></i> Data Refill Pasien</span>
            <span class="hi-badge hi-badge-info">{{ $upcomingRefills->count() }} pasien</span>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th>No. Reg HIV</th>
                        <th>Nama Pasien</th>
                        <th>Status Kepatuhan</th>
                        <th>Tgl Refill Berikutnya</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingRefills as $p)
                    <tr>
                        <td><span class="hi-code">{{ $p->master->no_reg_hiv }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                    $rIni = strtoupper(substr($p->user->nama ?? 'P', 0, 1));
                                    $rCol = $avatarColors[ord($rIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $rCol }}">{{ $rIni }}</div>
                                <span style="font-weight: 600;">{{ $p->user->nama }}</span>
                            </div>
                        </td>
                        <td>
                            @if($p->status_kepatuhan == 'hijau')
                                <span class="hi-badge hi-badge-success"><i class="fas fa-check-circle me-1"></i>Patuh</span>
                            @elseif($p->status_kepatuhan == 'kuning')
                                <span class="hi-badge hi-badge-warning"><i class="fas fa-exclamation-circle me-1"></i>Peringatan</span>
                            @elseif($p->status_kepatuhan == 'merah')
                                <span class="hi-badge hi-badge-danger"><i class="fas fa-times-circle me-1"></i>Drop-out</span>
                            @else
                                <span class="hi-badge hi-badge-muted">{{ strtoupper($p->status_kepatuhan) }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $tglRefill = $p->refill->last()->tgl_refill_berikutnya ?? null;
                            @endphp
                            @if($tglRefill)
                                <span style="font-weight: 700; color: var(--danger);">
                                    <i class="fas fa-calendar-exclamation me-1" style="font-size: 0.8rem;"></i>
                                    {{ $tglRefill }}
                                </span>
                            @else
                                <span style="color: var(--text-secondary);">-</span>
                            @endif
                        </td>
                        <td>
                            <button class="hi-btn hi-btn-primary hi-btn-sm">
                                <i class="fas fa-bell"></i> Kirim Pengingat
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="hi-empty">
                                <i class="fas fa-pills"></i>
                                <p>Tidak ada jadwal refill saat ini</p>
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