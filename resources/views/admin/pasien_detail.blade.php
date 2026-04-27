@extends('layouts.v_template')

@section('title', 'Detail Pasien - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<style>
    /* ===== Detail Page Specific Styles ===== */
    .profile-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(30, 58, 95, 0.05);
        margin-bottom: 1.5rem;
    }
    .profile-card-header {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 60%, var(--accent) 100%);
        padding: 2rem 2rem 1.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .profile-card-header::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -10%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(165,243,252,0.12) 0%, transparent 70%);
        border-radius: 50%;
    }
    .profile-card-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        left: 20%;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(6,182,212,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .profile-avatar-lg {
        width: 72px;
        height: 72px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.6rem;
        color: #fff;
        border: 3px solid rgba(255,255,255,0.25);
        backdrop-filter: blur(4px);
        position: relative;
        z-index: 1;
    }
    .profile-name {
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 0.15rem;
        position: relative;
        z-index: 1;
    }
    .profile-subtitle {
        font-size: 0.82rem;
        opacity: 0.75;
        position: relative;
        z-index: 1;
    }
    .profile-card-body {
        padding: 1.5rem 2rem;
    }
    .profile-info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 0.6rem 0;
    }
    .profile-info-item .info-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .profile-info-item .info-label {
        font-size: 0.72rem;
        font-weight: 500;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .profile-info-item .info-value {
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    /* ===== Adherence Ring ===== */
    .adherence-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 12px rgba(30, 58, 95, 0.05);
        margin-bottom: 1.5rem;
    }
    .adherence-ring {
        width: 140px;
        height: 140px;
        margin: 0 auto 1rem;
        position: relative;
    }
    .adherence-ring svg {
        transform: rotate(-90deg);
    }
    .adherence-ring .ring-value {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
    }
    .adherence-ring .ring-value small {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
    }
    .adherence-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.25rem;
    }
    .adherence-status {
        font-size: 0.78rem;
        font-weight: 700;
        padding: 0.3rem 0.85rem;
        border-radius: 50px;
        display: inline-block;
    }

    /* ===== Stat Summary Row ===== */
    .stat-summary {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .stat-item {
        flex: 1;
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 1.25rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(14, 116, 144, 0.08);
    }
    .stat-item .stat-num {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }
    .stat-item .stat-lbl {
        font-size: 0.7rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    /* ===== Alert Badge for Diary ===== */
    .alert-kritis {
        background: var(--danger-light);
        color: var(--danger);
        border: 1px solid rgba(220, 38, 38, 0.15);
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        animation: pulse-ring 2s ease-in-out infinite;
    }
    @keyframes pulse-ring {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .stat-summary {
            flex-wrap: wrap;
        }
        .stat-item {
            min-width: 45%;
        }
    }
</style>

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-user-injured header-icon"></i>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1>Detail Pasien</h1>
                <p>Informasi lengkap, riwayat kepatuhan, diary kesehatan, dan refill ARV</p>
            </div>
            <a href="{{ route('admin.pasien.index') }}" class="hi-btn" style="background: rgba(255,255,255,0.15); color: #fff; border: 1.5px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                <i class="fas fa-arrow-left"></i> Kembali ke Monitoring
            </a>
        </div>
    </div>

    {{-- ===== Top Section: Profile + Adherence ===== --}}
    <div class="row fade-up" style="animation-delay: 0.1s;">
        {{-- Profile Card --}}
        <div class="col-lg-8">
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="d-flex align-items-center gap-3">
                        @php
                            $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                            $patientName = $patient->master->nama ?? ($patient->user->nama ?? 'Pasien');
                            $pIni = strtoupper(substr($patientName, 0, 1));
                            $pCol = $avatarColors[ord($pIni) % count($avatarColors)];
                        @endphp
                        <div class="profile-avatar-lg" style="background: {{ $pCol }};">{{ $pIni }}</div>
                        <div>
                            <div class="profile-name">{{ $patientName }}</div>
                            <div class="profile-subtitle">
                                <i class="fas fa-id-card me-1"></i> No. Reg HIV: {{ $patient->master->no_reg_hiv ?? '-' }}
                            </div>
                            <div class="mt-1">
                                @if($patient->user && $patient->user->is_active)
                                    <span class="hi-badge hi-badge-success" style="font-size: 0.68rem;">
                                        <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> Akun Aktif
                                    </span>
                                @else
                                    <span class="hi-badge hi-badge-danger" style="font-size: 0.68rem;">
                                        <i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i> Akun Non-aktif
                                    </span>
                                @endif

                                @php
                                    $statusKepatuhan = $patient->status_kepatuhan ?? 'hijau';
                                    $skBadge = match($statusKepatuhan) {
                                        'hijau' => 'hi-badge-success',
                                        'kuning' => 'hi-badge-warning',
                                        'merah' => 'hi-badge-danger',
                                        default => 'hi-badge-info',
                                    };
                                    $skLabel = match($statusKepatuhan) {
                                        'hijau' => 'Patuh',
                                        'kuning' => 'Waspada',
                                        'merah' => 'Beresiko',
                                        default => '-',
                                    };
                                @endphp
                                <span class="hi-badge {{ $skBadge }}" style="font-size: 0.68rem;">
                                    {{ ucfirst($statusKepatuhan) }} — {{ $skLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="profile-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: var(--info-light); color: var(--info);">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div>
                                    <div class="info-label">Tanggal Lahir</div>
                                    <div class="info-value">
                                        {{ $patient->master->tgl_lahir ? \Carbon\Carbon::parse($patient->master->tgl_lahir)->format('d F Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: var(--success-light); color: var(--success);">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <div class="info-label">Alamat</div>
                                    <div class="info-value">{{ $patient->master->alamat ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: var(--warning-light); color: var(--warning);">
                                    <i class="fas fa-pills"></i>
                                </div>
                                <div>
                                    <div class="info-label">Fase Pengobatan</div>
                                    <div class="info-value">
                                        <span class="hi-badge hi-badge-info">{{ $patient->fase_pengobatan ?? 'Inisiasi' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: var(--danger-light); color: var(--danger);">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div>
                                    <div class="info-label">Username Akun</div>
                                    <div class="info-value">
                                        <span class="hi-code">{{ $patient->user->username ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Adherence Rate Card --}}
        <div class="col-lg-4">
            <div class="adherence-card fade-up" style="animation-delay: 0.15s;">
                <div class="adherence-label">
                    <i class="fas fa-chart-pie me-1"></i> Tingkat Kepatuhan (Adherence Rate)
                </div>

                @php
                    $ringColor = $adherenceRate >= 80 ? '#059669' : ($adherenceRate >= 50 ? '#d97706' : '#dc2626');
                    $ringBg = $adherenceRate >= 80 ? '#d1fae5' : ($adherenceRate >= 50 ? '#fef3c7' : '#fee2e2');
                    $ringText = $adherenceRate >= 80 ? 'Baik' : ($adherenceRate >= 50 ? 'Waspada' : 'Kritis');
                    $circumference = 2 * 3.14159 * 54;
                    $offset = $circumference - ($adherenceRate / 100) * $circumference;
                @endphp

                <div class="adherence-ring">
                    <svg width="140" height="140" viewBox="0 0 140 140">
                        {{-- Background ring --}}
                        <circle cx="70" cy="70" r="54"
                            fill="none" stroke="{{ $ringBg }}" stroke-width="12" />
                        {{-- Progress ring --}}
                        <circle cx="70" cy="70" r="54"
                            fill="none" stroke="{{ $ringColor }}" stroke-width="12"
                            stroke-linecap="round"
                            stroke-dasharray="{{ $circumference }}"
                            stroke-dashoffset="{{ $offset }}"
                            style="transition: stroke-dashoffset 1.5s ease;" />
                    </svg>
                    <div class="ring-value">
                        {{ $adherenceRate }}<small>%</small>
                    </div>
                </div>

                <div class="adherence-status" style="background: {{ $ringBg }}; color: {{ $ringColor }};">
                    <i class="fas fa-{{ $adherenceRate >= 80 ? 'check-circle' : ($adherenceRate >= 50 ? 'exclamation-triangle' : 'times-circle') }} me-1"></i>
                    {{ $ringText }}
                </div>

                <div class="mt-3" style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5;">
                    Dihitung dari <strong>{{ $patient->kepatuhan->count() }}</strong> total catatan kepatuhan.
                    <br>
                    <strong>{{ $patient->kepatuhan->where('status', 'hijau')->count() }}</strong> kali patuh minum obat.
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="stat-summary fade-up" style="animation-delay: 0.2s;">
                <div class="stat-item">
                    <div class="stat-num" style="color: var(--primary);">{{ $patient->kepatuhan->count() }}</div>
                    <div class="stat-lbl">Kepatuhan</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" style="color: var(--warning);">{{ $patient->diaryHarian->count() }}</div>
                    <div class="stat-lbl">Diary</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num" style="color: var(--success);">{{ $patient->refillObat->count() }}</div>
                    <div class="stat-lbl">Refill</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Tab Navigation ===== --}}
    <div class="fade-up" style="animation-delay: 0.25s;">
        <ul class="nav hi-tabs" id="detailTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="kepatuhan-tab" data-bs-toggle="tab" data-bs-target="#kepatuhanContent" type="button" role="tab">
                    <i class="fas fa-clipboard-check me-1"></i> Riwayat Kepatuhan
                    <span class="hi-badge hi-badge-muted ms-1">{{ $patient->kepatuhan->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="diary-tab" data-bs-toggle="tab" data-bs-target="#diaryContent" type="button" role="tab">
                    <i class="fas fa-book-medical me-1"></i> Diary Kesehatan
                    <span class="hi-badge hi-badge-muted ms-1">{{ $patient->diaryHarian->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="refill-tab" data-bs-toggle="tab" data-bs-target="#refillContent" type="button" role="tab">
                    <i class="fas fa-pills me-1"></i> Riwayat Refill ARV
                    <span class="hi-badge hi-badge-muted ms-1">{{ $patient->refillObat->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="detailTabContent">
            {{-- ==================== TAB 1: Riwayat Kepatuhan (FR-T02) ==================== --}}
            <div class="tab-pane fade show active" id="kepatuhanContent" role="tabpanel">
                <div class="hi-card">
                    <div class="hi-card-header">
                        <span><i class="fas fa-clipboard-check"></i> Riwayat Kepatuhan Minum Obat</span>
                        <span class="hi-badge hi-badge-info">{{ $patient->kepatuhan->count() }} Catatan</span>
                    </div>
                    <div class="hi-card-body" style="padding: 0;">
                        <table class="hi-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Status Kepatuhan</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->kepatuhan->sortByDesc('last_update') as $index => $record)
                                <tr>
                                    <td>
                                        <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem;">
                                            <i class="fas fa-calendar me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                            {{ \Carbon\Carbon::parse($record->last_update)->format('d M Y') }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: var(--text-secondary);">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($record->last_update)->format('H:i') }} WIB
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $kBadge = match($record->status) {
                                                'hijau' => ['class' => 'hi-badge-success', 'icon' => 'fa-check-circle', 'label' => 'Patuh (Diminum)'],
                                                'kuning' => ['class' => 'hi-badge-warning', 'icon' => 'fa-exclamation-triangle', 'label' => 'Waspada (Tunda)'],
                                                'merah' => ['class' => 'hi-badge-danger', 'icon' => 'fa-times-circle', 'label' => 'Beresiko (Terlewat)'],
                                                default => ['class' => 'hi-badge-muted', 'icon' => 'fa-question-circle', 'label' => '-'],
                                            };
                                        @endphp
                                        <span class="hi-badge {{ $kBadge['class'] }}">
                                            <i class="fas {{ $kBadge['icon'] }} me-1"></i> {{ $kBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-secondary);">
                                        @if($record->status === 'hijau')
                                            Obat diminum tepat waktu
                                        @elseif($record->status === 'kuning')
                                            Obat diminum terlambat, perlu perhatian
                                        @elseif($record->status === 'merah')
                                            <span style="color: var(--danger); font-weight: 600;">⚠ Obat tidak diminum, perlu follow-up</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4">
                                        <div class="hi-empty">
                                            <i class="fas fa-clipboard-list"></i>
                                            <p>Belum ada data riwayat kepatuhan untuk pasien ini.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB 2: Diary Kesehatan (FR-T04) ==================== --}}
            <div class="tab-pane fade" id="diaryContent" role="tabpanel">
                <div class="hi-card">
                    <div class="hi-card-header">
                        <span><i class="fas fa-book-medical"></i> Laporan Diary Kesehatan Harian</span>
                        <span class="hi-badge hi-badge-info">{{ $patient->diaryHarian->count() }} Catatan</span>
                    </div>
                    <div class="hi-card-body" style="padding: 0;">
                        <table class="hi-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Kondisi</th>
                                    <th>Gejala / Keluhan</th>
                                    <th>Catatan Tambahan</th>
                                    <th style="width: 90px;">Alert</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->diaryHarian->sortByDesc('tanggal') as $index => $diary)
                                @php
                                    // Deteksi kata-kata kritis dalam gejala untuk highlight visual
                                    $kataKritis = ['demam tinggi', 'sesak napas', 'diare berat', 'muntah', 'pusing hebat', 'ruam kulit', 'gangguan penglihatan', 'nyeri dada', 'penurunan berat badan', 'infeksi', 'batuk darah', 'kejang', 'pingsan'];
                                    $gejalaLower = strtolower($diary->gejala ?? '');
                                    $catatanLower = strtolower($diary->catatan ?? '');
                                    $isKritis = false;
                                    $matchedKata = [];
                                    foreach ($kataKritis as $kata) {
                                        if (str_contains($gejalaLower, $kata) || str_contains($catatanLower, $kata)) {
                                            $isKritis = true;
                                            $matchedKata[] = $kata;
                                        }
                                    }

                                    // Juga cek kondisi buruk
                                    $kondisiBuruk = in_array(strtolower($diary->kondisi ?? ''), ['buruk', 'sangat buruk', 'kritis']);
                                    $isKritis = $isKritis || $kondisiBuruk;
                                @endphp
                                <tr style="{{ $isKritis ? 'background: rgba(220,38,38,0.03); border-left: 3px solid var(--danger);' : '' }}">
                                    <td>
                                        <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem;">
                                            <i class="fas fa-calendar me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                            {{ \Carbon\Carbon::parse($diary->tanggal)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $kondisiMap = match(strtolower($diary->kondisi ?? '')) {
                                                'baik', 'sangat baik' => ['class' => 'hi-badge-success', 'icon' => 'fa-smile'],
                                                'cukup', 'biasa' => ['class' => 'hi-badge-warning', 'icon' => 'fa-meh'],
                                                'buruk', 'sangat buruk', 'kritis' => ['class' => 'hi-badge-danger', 'icon' => 'fa-frown'],
                                                default => ['class' => 'hi-badge-muted', 'icon' => 'fa-minus-circle'],
                                            };
                                        @endphp
                                        <span class="hi-badge {{ $kondisiMap['class'] }}">
                                            <i class="fas {{ $kondisiMap['icon'] }} me-1"></i> {{ ucfirst($diary->kondisi ?? '-') }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.82rem; max-width: 250px;">
                                        @if($diary->gejala)
                                            <div style="{{ $isKritis ? 'color: var(--danger); font-weight: 600;' : 'color: var(--text-primary);' }}">
                                                {{ $diary->gejala }}
                                            </div>
                                        @else
                                            <span style="color: var(--text-secondary); font-style: italic;">Tidak ada keluhan</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 0.82rem; color: var(--text-secondary); max-width: 200px;">
                                        {{ $diary->catatan ?? '-' }}
                                    </td>
                                    <td>
                                        @if($isKritis)
                                            <span class="alert-kritis" title="Gejala mengandung: {{ implode(', ', $matchedKata) }}{{ $kondisiBuruk ? ' (Kondisi buruk)' : '' }}">
                                                <i class="fas fa-exclamation-triangle"></i> Kritis
                                            </span>
                                        @else
                                            <span style="font-size: 0.72rem; color: #94a3b8;">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="hi-empty">
                                            <i class="fas fa-book-open"></i>
                                            <p>Belum ada catatan diary kesehatan harian untuk pasien ini.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ==================== TAB 3: Riwayat Refill ARV ==================== --}}
            <div class="tab-pane fade" id="refillContent" role="tabpanel">
                <div class="hi-card">
                    <div class="hi-card-header">
                        <span><i class="fas fa-pills"></i> Riwayat Pengajuan Refill ARV</span>
                        <span class="hi-badge hi-badge-info">{{ $patient->refillObat->count() }} Pengajuan</span>
                    </div>
                    <div class="hi-card-body" style="padding: 0;">
                        <table class="hi-table">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal Pengajuan / Refill</th>
                                    <th>Siklus Ke-</th>
                                    <th>Status</th>
                                    <th>Dibuat Pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->refillObat->sortByDesc('created_at') as $index => $refill)
                                <tr>
                                    <td>
                                        <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                            {{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem;">
                                            <i class="fas fa-calendar-check me-1" style="color: var(--success); font-size: 0.7rem;"></i>
                                            {{ \Carbon\Carbon::parse($refill->tanggal_refill)->format('d M Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="hi-badge hi-badge-info">
                                            <i class="fas fa-sync-alt me-1"></i> Siklus {{ $refill->siklus_ke }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $refillBadge = match($refill->status) {
                                                'menunggu' => ['class' => 'hi-badge-warning', 'icon' => 'fa-hourglass-half', 'label' => 'Menunggu'],
                                                'selesai' => ['class' => 'hi-badge-success', 'icon' => 'fa-check-double', 'label' => 'Selesai'],
                                                default => ['class' => 'hi-badge-muted', 'icon' => 'fa-question-circle', 'label' => ucfirst($refill->status)],
                                            };
                                        @endphp
                                        <span class="hi-badge {{ $refillBadge['class'] }}">
                                            <i class="fas {{ $refillBadge['icon'] }} me-1"></i> {{ $refillBadge['label'] }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.78rem; color: var(--text-secondary);">
                                        @if($refill->created_at)
                                            {{ $refill->created_at->format('d M Y, H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="hi-empty">
                                            <i class="fas fa-prescription-bottle-alt"></i>
                                            <p>Belum ada riwayat pengajuan refill ARV untuk pasien ini.</p>
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
</div>
@endsection
