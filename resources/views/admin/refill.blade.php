@extends('layouts.v_template')

@section('title', 'Monitoring Refill ARV - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-capsules header-icon"></i>
        <h1>Monitoring Refill Obat ARV</h1>
        <p>Pantau jadwal pengambilan obat, setujui pengajuan, dan kelola status refill pasien</p>
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

    {{-- Info Card: H-3 Reminder --}}
    @if($upcomingCount > 0)
    <div class="fade-up" style="background: linear-gradient(135deg, #fef3c7, #fff7ed); border: 1px solid #f59e0b; border-radius: 14px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
        <div style="width: 44px; height: 44px; border-radius: 12px; background: #f59e0b; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div>
            <div style="font-weight: 700; font-size: 0.9rem; color: #92400e;">⚠ Perhatian: {{ $upcomingCount }} Pengajuan Mendekati Jadwal (H-3)</div>
            <div style="font-size: 0.78rem; color: #a16207;">Segera verifikasi pengajuan refill yang jadwal pengambilannya dalam 3 hari ke depan.</div>
        </div>
    </div>
    @endif

    {{-- Filter Card --}}
    <div class="hi-card fade-up" style="margin-bottom: 1.5rem;">
        <div class="hi-card-header">
            <span><i class="fas fa-filter"></i> Filter Data Refill</span>
        </div>
        <div class="hi-card-body">
            <form action="{{ route('admin.refill.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><i class="fas fa-heartbeat me-1"></i>Status Refill</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>⏳ Menunggu</option>
                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>✅ Disetujui</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>✔️ Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><i class="fas fa-calendar me-1"></i>Bulan Refill</label>
                        <input type="month" class="form-control" name="bulan" value="{{ request('bulan') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="hi-btn hi-btn-primary hi-btn-sm">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.refill.index') }}" class="hi-btn hi-btn-outline hi-btn-sm">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Refill Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-pills"></i> Daftar Pengajuan Refill ARV</span>
            <span class="hi-badge hi-badge-info">{{ $requests->count() }} pengajuan</span>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Pasien</th>
                        <th>No. Reg HIV</th>
                        <th>Tgl Refill</th>
                        <th>Tgl Diambil</th>
                        <th>Siklus</th>
                        <th>Status</th>
                        <th style="width: 200px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $index => $refill)
                    @php
                        $isUrgent = $refill->status === 'menunggu' && $refill->tanggal_refill && \Carbon\Carbon::parse($refill->tanggal_refill)->diffInDays(now(), false) >= -3 && \Carbon\Carbon::parse($refill->tanggal_refill)->isFuture();
                    @endphp
                    <tr style="{{ $isUrgent ? 'background: rgba(245, 158, 11, 0.05); border-left: 3px solid #f59e0b;' : '' }}">
                        <td>
                            <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                    $nama = $refill->pasien->master->nama ?? ($refill->pasien->user->nama ?? 'P');
                                    $rIni = strtoupper(substr($nama, 0, 1));
                                    $rCol = $avatarColors[ord($rIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $rCol }}">{{ $rIni }}</div>
                                <span style="font-weight: 600;">{{ $nama }}</span>
                            </div>
                        </td>
                        <td><span class="hi-code">{{ $refill->pasien->master->no_reg_hiv ?? '-' }}</span></td>
                        <td>
                            <div style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-calendar me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                {{ \Carbon\Carbon::parse($refill->tanggal_refill)->format('d M Y') }}
                            </div>
                            @if($isUrgent)
                                <small style="color: #f59e0b; font-weight: 600; font-size: 0.68rem;">
                                    <i class="fas fa-clock"></i> H-{{ \Carbon\Carbon::parse($refill->tanggal_refill)->diffInDays(now()) }}
                                </small>
                            @endif
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-secondary);">
                            @if($refill->tanggal_diambil)
                                <i class="fas fa-check me-1" style="color: var(--success);"></i>
                                {{ \Carbon\Carbon::parse($refill->tanggal_diambil)->format('d M Y') }}
                            @else
                                <span style="opacity: 0.5;">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="hi-badge hi-badge-info">
                                <i class="fas fa-sync-alt me-1"></i> Siklus {{ $refill->siklus_ke }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusBadge = match($refill->status) {
                                    'menunggu' => ['class' => 'hi-badge-warning', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu'],
                                    'disetujui' => ['class' => 'hi-badge-info', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'],
                                    'selesai' => ['class' => 'hi-badge-success', 'icon' => 'fa-check-double', 'label' => 'Selesai'],
                                    default => ['class' => 'hi-badge-muted', 'icon' => 'fa-question', 'label' => ucfirst($refill->status)],
                                };
                            @endphp
                            <span class="hi-badge {{ $statusBadge['class'] }}">
                                <i class="fas {{ $statusBadge['icon'] }} me-1"></i> {{ $statusBadge['label'] }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @if($refill->status === 'menunggu')
                                    <form action="{{ route('admin.refill.updateStatus', $refill->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="disetujui">
                                        <button type="submit" class="hi-btn hi-btn-primary hi-btn-sm" onclick="return confirm('Setujui pengajuan refill ini?')">
                                            <i class="fas fa-check"></i> Setujui
                                        </button>
                                    </form>
                                @elseif($refill->status === 'disetujui')
                                    <form action="{{ route('admin.refill.updateStatus', $refill->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="status" value="selesai">
                                        <button type="submit" class="hi-btn hi-btn-success hi-btn-sm" onclick="return confirm('Tandai refill ini sebagai selesai (obat sudah diambil)?')">
                                            <i class="fas fa-check-double"></i> Selesai
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size: 0.72rem; color: var(--text-secondary);"><i class="fas fa-check-circle me-1" style="color: var(--success);"></i>Completed</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="hi-empty">
                                <i class="fas fa-pills"></i>
                                <p>Tidak ada pengajuan refill saat ini. Coba ubah filter atau tunggu pengajuan dari pasien.</p>
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