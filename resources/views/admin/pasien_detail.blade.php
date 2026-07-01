@extends('layouts.v_template')

@section('title', 'Detail Pasien - WEAR')

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
    /* ===== Fix for DataTables Empty State & Numbering ===== */
    .hi-table tbody {
        counter-reset: rowNumber;
    }
    .hi-table tbody tr:not(.datatable-empty) {
        counter-increment: rowNumber;
    }
    .row-number::before {
        content: counter(rowNumber);
    }
    
    /* ===== Fix for Simple-DataTables Pagination UI ===== */
    .dataTable-pagination {
        margin-top: 15px;
    }
    .dataTable-pagination ul {
        display: flex;
        list-style: none;
        padding-left: 0;
        margin-bottom: 0;
    }
    .dataTable-pagination li {
        margin: 0 2px;
    }
    .dataTable-pagination a {
        padding: 6px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-decoration: none;
        color: var(--primary);
        background-color: #fff;
        font-weight: 500;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }
    .dataTable-pagination a:hover {
        background-color: #f1f5f9;
        color: var(--primary-dark);
    }
    .dataTable-pagination .active a {
        background-color: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 2px 4px rgba(14, 116, 144, 0.2);
    }
    .dataTable-pagination .disabled a {
        color: #94a3b8;
        background-color: #f8fafc;
        cursor: not-allowed;
    }

    /* ===== DataTables Overrides & Table Premium Aesthetics ===== */
    .dataTable-wrapper .dataTable-top {
        padding: 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .dataTable-wrapper .dataTable-bottom {
        padding: 1.5rem;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    .dataTable-wrapper .dataTable-input {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 0.5rem 1.25rem;
        font-size: 0.85rem;
        background: #f8fafc;
        transition: all 0.3s ease;
        width: 250px;
    }
    .dataTable-wrapper .dataTable-input:focus {
        outline: none;
        border-color: var(--primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(14, 116, 144, 0.1);
        width: 300px;
    }
    .dataTable-wrapper .dataTable-selector {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
        background: #ffffff;
        color: var(--text-primary);
        font-weight: 500;
        cursor: pointer;
    }
    .dataTable-info {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    .dataTable-pagination a {
        border-radius: 8px !important;
        margin: 0 3px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.5rem 0.85rem !important;
        color: var(--text-secondary) !important;
        transition: all 0.2s ease;
    }
    .dataTable-pagination a:hover {
        background: #f1f5f9 !important;
        color: var(--primary) !important;
    }
    .dataTable-pagination .active a {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
        box-shadow: 0 4px 10px rgba(14, 116, 144, 0.2) !important;
    }
    
    /* Premium Table Styles */
    .hi-table, .dataTable-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        background: #ffffff;
    }
    .hi-table th, .dataTable-table th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }
    .hi-table td, .dataTable-table td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
        color: var(--text-primary);
        transition: background-color 0.2s ease;
    }
    .hi-table tbody tr:hover td, .dataTable-table tbody tr:hover td {
        background-color: #f8fafc;
    }
    .hi-table tbody tr:last-child td, .dataTable-table tbody tr:last-child td {
        border-bottom: none;
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
            <div class="d-flex gap-2">
                <a href="{{ route('admin.laporan.cetakDetail', $patient->id) }}" target="_blank" class="hi-btn" style="background: var(--primary); color: #fff; border: 1.5px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                    <i class="fas fa-print"></i> Cetak Rekam Medis (PDF)
                </a>
                <a href="{{ route('admin.pasien.index') }}" class="hi-btn" style="background: rgba(255,255,255,0.15); color: #fff; border: 1.5px solid rgba(255,255,255,0.3); backdrop-filter: blur(4px);">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
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
                            $avatarColors = ['#012D1D','#0e7490','#059669','#012D1D','#7c3aed','#d97706'];
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
                        {{-- ===== Data Baru: Jenis Kelamin, No HP, Data Fisik ===== --}}
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: #ede9fe; color: #7c3aed;">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div>
                                    <div class="info-label">Jenis Kelamin</div>
                                    <div class="info-value">
                                        @if($patient->master->jenis_kelamin === 'L')
                                            <i class="fas fa-mars me-1" style="color: #012D1D;"></i> Laki-laki
                                        @elseif($patient->master->jenis_kelamin === 'P')
                                            <i class="fas fa-venus me-1" style="color: #ec4899;"></i> Perempuan
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: #dbeafe; color: #012D1D;">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                                <div>
                                    <div class="info-label">No. Handphone</div>
                                    <div class="info-value">{{ $patient->user->no_hp ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: #fef3c7; color: #d97706;">
                                    <i class="fas fa-weight"></i>
                                </div>
                                <div>
                                    <div class="info-label">Berat / Tinggi Badan</div>
                                    <div class="info-value">
                                        {{ $patient->master->berat_badan ? $patient->master->berat_badan . ' kg' : '-' }}
                                        &nbsp;/&nbsp;
                                        {{ $patient->master->tinggi_badan ? $patient->master->tinggi_badan . ' cm' : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="profile-info-item">
                                <div class="info-icon" style="background: #d1fae5; color: #059669;">
                                    <i class="fas fa-calculator"></i>
                                </div>
                                <div>
                                    <div class="info-label">BMI (Body Mass Index)</div>
                                    <div class="info-value">
                                        @if($patient->master->berat_badan && $patient->master->tinggi_badan)
                                            @php
                                                $bmi = $patient->master->berat_badan / pow($patient->master->tinggi_badan / 100, 2);
                                                $bmiFormatted = number_format($bmi, 1);
                                                $bmiLabel = $bmi < 18.5 ? 'Underweight' : ($bmi < 25 ? 'Normal' : ($bmi < 30 ? 'Overweight' : 'Obese'));
                                                $bmiColor = $bmi < 18.5 ? '#d97706' : ($bmi < 25 ? '#059669' : ($bmi < 30 ? '#d97706' : '#dc2626'));
                                            @endphp
                                            {{ $bmiFormatted }}
                                            <span class="hi-badge" style="background: {{ $bmiColor }}15; color: {{ $bmiColor }}; font-size: 0.68rem; margin-left: 0.5rem;">
                                                {{ $bmiLabel }}
                                            </span>
                                        @else
                                            -
                                        @endif
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
                    $ringColor = $statusWarna == 'hijau' ? '#059669' : ($statusWarna == 'kuning' ? '#d97706' : '#dc2626');
                    $ringBg = $statusWarna == 'hijau' ? '#d1fae5' : ($statusWarna == 'kuning' ? '#fef3c7' : '#fee2e2');
                    $ringText = $statusWarna == 'hijau' ? 'Patuh (Disiplin)' : ($statusWarna == 'kuning' ? 'Waspada' : 'Tidak Patuh');
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
                    <i class="fas fa-{{ $statusWarna == 'hijau' ? 'check-circle' : ($statusWarna == 'kuning' ? 'exclamation-triangle' : 'times-circle') }} me-1"></i>
                    {{ $ringText }}
                </div>

                <div class="mt-3" style="font-size: 0.75rem; color: var(--text-secondary); line-height: 1.5;">
                    Dihitung dari <strong>{{ \Carbon\Carbon::now()->daysInMonth }}</strong> hari di bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}.
                    <br>
                    <strong>{{ $diminumCount ?? 0 }}</strong> kali patuh minum obat bulan ini.
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

    {{-- ===== Filter Bulan & Tahun ===== --}}
    <div class="row fade-up mb-4" style="animation-delay: 0.22s;">
        <div class="col-12">
            <div class="bg-white p-3 rounded-4" style="border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                <form method="GET" action="{{ route('admin.pasien.show', $patient->id) }}" class="d-flex align-items-center flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #e0f2fe; color: #0284c7; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <span class="fw-bold" style="font-size: 0.85rem; color: var(--text-secondary);">Tampilkan Riwayat:</span>
                    </div>
                    
                    <select name="month" class="form-select form-select-sm" style="width: auto; min-width: 150px; border-radius: 8px; border-color: #cbd5e1; padding: 0.4rem 2rem 0.4rem 1rem;">
                        <option value="">Semua Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month', $filterMonth) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>

                    <select name="year" class="form-select form-select-sm" style="width: auto; min-width: 100px; border-radius: 8px; border-color: #cbd5e1; padding: 0.4rem 2rem 0.4rem 1rem;">
                        @foreach(range(now()->year - 2, now()->year) as $y)
                            <option value="{{ $y }}" {{ request('year', $filterYear) == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="hi-btn hi-btn-sm" style="background: var(--primary); color: white; border-radius: 8px; padding: 0.4rem 1.25rem;">
                        <i class="fas fa-filter me-1"></i> Terapkan Filter
                    </button>

                    @if(request()->has('month') && request('month') != '')
                        <a href="{{ route('admin.pasien.show', $patient->id) }}" class="hi-btn hi-btn-sm" style="background: #f1f5f9; color: var(--text-secondary); border-radius: 8px; padding: 0.4rem 1.25rem;">
                            <i class="fas fa-times me-1"></i> Reset
                        </a>
                    @endif
                </form>
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
                        <table class="hi-table" id="tableKepatuhan">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Status Kepatuhan</th>
                                    <th>Bukti Foto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->kepatuhan->sortByDesc('last_update') as $index => $record)
                                <tr>
                                    <td>
                                        <span class="row-number" style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
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
                                                'diminum', 'tepat waktu', 'hijau' => ['class' => 'hi-badge-success', 'icon' => 'fa-check-circle', 'label' => 'Patuh (Diminum)'],
                                                'tunda', 'kuning' => ['class' => 'hi-badge-warning', 'icon' => 'fa-exclamation-triangle', 'label' => 'Waspada (Tunda)'],
                                                'terlewat', 'merah' => ['class' => 'hi-badge-danger', 'icon' => 'fa-times-circle', 'label' => 'Beresiko (Terlewat)'],
                                                default => ['class' => 'hi-badge-muted', 'icon' => 'fa-question-circle', 'label' => ucfirst($record->status ?? '-')],
                                            };
                                        @endphp
                                        <span class="hi-badge {{ $kBadge['class'] }}">
                                            <i class="fas {{ $kBadge['icon'] }} me-1"></i> {{ $kBadge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($record->foto_bukti)
                                            <button type="button" class="hi-badge hi-badge-info" data-bs-toggle="modal" data-bs-target="#modalFotoKepatuhan{{ $record->id }}" style="border: none; background: var(--info); color: white;">
                                                <i class="fas fa-image me-1"></i> Lihat Bukti
                                            </button>
                                        @else
                                            <span style="font-size: 0.75rem; color: #94a3b8; font-style: italic;">Tidak ada foto</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
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
                        <table class="hi-table" id="tableDiary">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Kondisi Kesehatan Pasien</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($patient->diaryHarian->sortByDesc('tanggal') as $index => $diary)
                                @php
                                    // Hanya cek kondisi buruk berdasarkan dropdown
                                    $kondisiBuruk = in_array(strtolower($diary->kondisi ?? ''), ['buruk', 'sangat buruk', 'kritis']);
                                @endphp
                                <tr style="{{ $kondisiBuruk ? 'background: rgba(220,38,38,0.03); border-left: 3px solid var(--danger);' : '' }}">
                                    <td>
                                        <span class="row-number" style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
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
                                </tr>
                                @endforeach
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
                        <table class="hi-table" id="tableRefill">
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
                                @foreach($patient->refillObat->sortByDesc('created_at') as $index => $refill)
                                <tr>
                                    <td>
                                        <span class="row-number" style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}
@foreach($patient->kepatuhan as $record)
    @if($record->foto_bukti)
    <div class="modal fade" id="modalFotoKepatuhan{{ $record->id }}" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header" style="border-bottom: 1px solid #e2e8f0;">
            <h5 class="modal-title fw-bold"><i class="fas fa-image me-2 text-primary"></i>Bukti Foto Minum Obat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center" style="background: var(--surface-light); padding: 1.5rem;">
            <img src="{{ url('/file/' . $record->foto_bukti) }}" alt="Bukti Foto" class="img-fluid rounded shadow-sm" style="max-height: 75vh; object-fit: contain;">
          </div>
          <div class="modal-footer" style="border-top: none;">
            <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Tutup Layar</button>
          </div>
        </div>
      </div>
    </div>
    @endif
@endforeach

@endsection

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', event => {
        const tableKepatuhan = document.getElementById('tableKepatuhan');
        if (tableKepatuhan) {
            new simpleDatatables.DataTable(tableKepatuhan, {
                searchable: true,
                perPage: 10
            });
        }

        const tableDiary = document.getElementById('tableDiary');
        if (tableDiary) {
            new simpleDatatables.DataTable(tableDiary, {
                searchable: true,
                perPage: 10
            });
        }

        const tableRefill = document.getElementById('tableRefill');
        if (tableRefill) {
            new simpleDatatables.DataTable(tableRefill, {
                searchable: true,
                perPage: 10
            });
        }
    });
</script>
@endpush
