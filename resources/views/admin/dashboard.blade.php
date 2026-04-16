@extends('layouts.v_template')

@section('content')
{{-- Custom Dashboard Styles --}}
<style>
    /* ===== Google Font ===== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        /* ===== Medical Color Palette ===== */
        --primary:       #0891b2;  /* Teal */
        --primary-dark:  #0e7490;
        --primary-light: #a5f3fc;
        --secondary:     #1e3a5f;  /* Navy */
        --accent:        #06b6d4;  /* Cyan */
        --success:       #059669;  /* Emerald */
        --success-light: #d1fae5;
        --warning:       #d97706;  /* Amber */
        --warning-light: #fef3c7;
        --danger:        #dc2626;  /* Red */
        --danger-light:  #fee2e2;
        --info:          #2563eb;  /* Blue */
        --info-light:    #dbeafe;
        --surface:       #f0fdfa;  /* Light teal tint */
        --text-primary:  #1e293b;
        --text-secondary:#64748b;
        --card-bg:       #ffffff;
        --border:        #e2e8f0;
    }

    .dashboard-wrapper {
        font-family: 'Inter', sans-serif;
        padding: 0 0.5rem;
        background: var(--surface);
        min-height: 100vh;
    }

    /* ===== Welcome Banner ===== */
    .welcome-banner {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 60%, var(--accent) 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 40px rgba(14, 116, 144, 0.25);
    }
    .welcome-banner::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(165,243,252,0.12) 0%, transparent 70%);
        border-radius: 50%;
    }
    .welcome-banner::after {
        content: '';
        position: absolute;
        bottom: -60%;
        right: 15%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .welcome-banner h2 {
        font-weight: 800;
        font-size: 1.75rem;
        margin-bottom: 0.25rem;
    }
    .welcome-banner p {
        opacity: 0.85;
        font-size: 0.95rem;
        margin: 0;
    }
    .welcome-banner .welcome-date {
        font-size: 0.8rem;
        opacity: 0.7;
        margin-top: 0.5rem;
    }
    .welcome-banner .banner-icon {
        font-size: 3.5rem;
        opacity: 0.2;
        color: var(--primary-light);
    }

    /* ===== Stat Cards ===== */
    .stat-card {
        border: none;
        border-radius: 16px;
        padding: 1.5rem 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        cursor: default;
        min-height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        background: var(--card-bg);
        border: 1px solid var(--border);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 40px rgba(14, 116, 144, 0.12) !important;
        border-color: transparent;
    }
    .stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 1rem;
    }
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
        color: var(--text-primary);
    }
    .stat-card .stat-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card .stat-accent {
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        border-radius: 16px 0 0 16px;
    }

    /* Card Accent Colors */
    .stat-card.card-patients .stat-icon { background: rgba(8, 145, 178, 0.1); color: var(--primary); }
    .stat-card.card-patients .stat-accent { background: linear-gradient(180deg, var(--primary), var(--accent)); }

    .stat-card.card-pending .stat-icon { background: var(--warning-light); color: var(--warning); }
    .stat-card.card-pending .stat-accent { background: linear-gradient(180deg, #f59e0b, var(--warning)); }

    .stat-card.card-compliant .stat-icon { background: var(--success-light); color: var(--success); }
    .stat-card.card-compliant .stat-accent { background: linear-gradient(180deg, #34d399, var(--success)); }

    .stat-card.card-risk .stat-icon { background: var(--danger-light); color: var(--danger); }
    .stat-card.card-risk .stat-accent { background: linear-gradient(180deg, #f87171, var(--danger)); }

    .stat-card.card-refill .stat-icon { background: var(--info-light); color: var(--info); }
    .stat-card.card-refill .stat-accent { background: linear-gradient(180deg, #60a5fa, var(--info)); }

    .stat-card.card-broadcast .stat-icon { background: rgba(14, 116, 144, 0.08); color: var(--primary-dark); }
    .stat-card.card-broadcast .stat-accent { background: linear-gradient(180deg, var(--primary-dark), var(--secondary)); }

    /* ===== Modern Content Cards ===== */
    .modern-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(30, 58, 95, 0.06);
        overflow: hidden;
        transition: all 0.3s ease;
        background: var(--card-bg);
        border: 1px solid var(--border);
    }
    .modern-card:hover {
        box-shadow: 0 6px 24px rgba(30, 58, 95, 0.1);
    }
    .modern-card .card-header {
        background: transparent;
        border-bottom: 1px solid var(--border);
        padding: 1.15rem 1.5rem;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .modern-card .card-header i {
        color: var(--primary);
    }
    .modern-card .card-body {
        padding: 1.5rem;
    }

    /* ===== Chart ===== */
    .chart-container {
        position: relative;
        max-width: 280px;
        margin: 0 auto;
    }

    /* ===== Quick Action Buttons ===== */
    .quick-action-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.65rem;
    }
    .quick-action-btn {
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1rem 0.75rem;
        background: var(--card-bg);
        text-align: center;
        text-decoration: none;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.78rem;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.4rem;
    }
    .quick-action-btn:hover {
        border-color: var(--primary);
        background: rgba(8, 145, 178, 0.04);
        color: var(--primary);
        transform: translateY(-2px);
        text-decoration: none;
        box-shadow: 0 4px 16px rgba(8, 145, 178, 0.1);
    }
    .quick-action-btn i {
        font-size: 1.3rem;
        color: var(--primary);
    }

    /* ===== Modern Table ===== */
    .modern-table {
        width: 100%;
        font-size: 0.85rem;
    }
    .modern-table thead th {
        background: var(--surface);
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        padding: 0.85rem 1rem;
        border: none;
    }
    .modern-table tbody td {
        padding: 0.85rem 1rem;
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        color: var(--text-primary);
    }
    .modern-table tbody tr:hover {
        background: var(--surface);
    }
    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ===== Status Badges ===== */
    .badge-status {
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }
    .badge-hijau {
        background: var(--success-light);
        color: var(--success);
    }
    .badge-kuning {
        background: var(--warning-light);
        color: var(--warning);
    }
    .badge-merah {
        background: var(--danger-light);
        color: var(--danger);
    }
    .badge-pending {
        background: var(--info-light);
        color: var(--info);
    }

    /* ===== Compliance Progress Bar ===== */
    .compliance-bar-wrap {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .compliance-bar-wrap .bar-label {
        min-width: 105px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--text-primary);
    }
    .compliance-bar-wrap .bar-container {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .compliance-bar-wrap .bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 1.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .compliance-bar-wrap .bar-value {
        min-width: 40px;
        text-align: right;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    /* ===== Avatar ===== */
    .avatar-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        color: #fff;
    }

    /* ===== View All Button ===== */
    .btn-view-all {
        background: var(--primary);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.35rem 0.85rem;
        transition: all 0.2s ease;
    }
    .btn-view-all:hover {
        background: var(--primary-dark);
        color: #fff;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.25);
    }

    /* ===== Fade-in Animation ===== */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.6s ease forwards;
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }
    .fade-up:nth-child(1) { animation-delay: 0.05s; }
    .fade-up:nth-child(2) { animation-delay: 0.1s; }
    .fade-up:nth-child(3) { animation-delay: 0.15s; }
    .fade-up:nth-child(4) { animation-delay: 0.2s; }
    .fade-up:nth-child(5) { animation-delay: 0.25s; }
    .fade-up:nth-child(6) { animation-delay: 0.3s; }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .welcome-banner { padding: 1.5rem; }
        .welcome-banner h2 { font-size: 1.3rem; }
        .stat-card { min-height: 130px; padding: 1.25rem; }
        .stat-card .stat-number { font-size: 1.5rem; }
        .quick-action-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="dashboard-wrapper">
    {{-- ===== Welcome Banner ===== --}}
    <div class="welcome-banner fade-up">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h2>Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! 👋</h2>
                <p>Dashboard monitoring kesehatan pasien HI!-CARE</p>
                <div class="welcome-date">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>
            </div>
            <div class="d-none d-md-block banner-icon">
                <i class="fas fa-heartbeat"></i>
            </div>
        </div>
    </div>

    {{-- ===== Stat Cards Row ===== --}}
    <div class="row g-3 mb-4">
        {{-- Total Pasien --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-patients">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['total_pasien'] }}">{{ $stats['total_pasien'] }}</div>
                    <div class="stat-label">Total Pasien</div>
                </div>
            </div>
        </div>

        {{-- Pending Verifikasi --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-pending">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['pending_verifikasi'] }}">{{ $stats['pending_verifikasi'] }}</div>
                    <div class="stat-label">Pending</div>
                </div>
            </div>
        </div>

        {{-- Kepatuhan Hijau --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-compliant">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['kepatuhan_hijau'] }}">{{ $stats['kepatuhan_hijau'] }}</div>
                    <div class="stat-label">Patuh</div>
                </div>
            </div>
        </div>

        {{-- Kepatuhan Merah --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-risk">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['kepatuhan_merah'] }}">{{ $stats['kepatuhan_merah'] }}</div>
                    <div class="stat-label">Risiko Tinggi</div>
                </div>
            </div>
        </div>

        {{-- Total Refill --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-refill">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-pills"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['total_refill'] }}">{{ $stats['total_refill'] }}</div>
                    <div class="stat-label">Total Refill</div>
                </div>
            </div>
        </div>

        {{-- Total Broadcast --}}
        <div class="col-xl-2 col-lg-4 col-md-6 col-6 fade-up">
            <div class="stat-card card-broadcast">
                <div class="stat-accent"></div>
                <div class="stat-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <div>
                    <div class="stat-number" data-count="{{ $stats['total_broadcast'] }}">{{ $stats['total_broadcast'] }}</div>
                    <div class="stat-label">Broadcast</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Main Content Row ===== --}}
    <div class="row g-4 mb-4">
        {{-- Chart: Analitik Kepatuhan --}}
        <div class="col-lg-5 fade-up">
            <div class="modern-card h-100">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Analitik Kepatuhan
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="complianceChart"></canvas>
                    </div>

                    {{-- Compliance Progress Bars --}}
                    <div class="mt-4">
                        @php
                            $total = max($stats['kepatuhan_hijau'] + $stats['kepatuhan_kuning'] + $stats['kepatuhan_merah'], 1);
                            $pctHijau = round(($stats['kepatuhan_hijau'] / $total) * 100);
                            $pctKuning = round(($stats['kepatuhan_kuning'] / $total) * 100);
                            $pctMerah = round(($stats['kepatuhan_merah'] / $total) * 100);
                        @endphp

                        <div class="compliance-bar-wrap">
                            <span class="bar-label">🟢 Patuh</span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ $pctHijau }}%; background: var(--success);"></div>
                            </div>
                            <span class="bar-value">{{ $pctHijau }}%</span>
                        </div>
                        <div class="compliance-bar-wrap">
                            <span class="bar-label">🟡 Peringatan</span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ $pctKuning }}%; background: var(--warning);"></div>
                            </div>
                            <span class="bar-value">{{ $pctKuning }}%</span>
                        </div>
                        <div class="compliance-bar-wrap">
                            <span class="bar-label">🔴 Drop-out</span>
                            <div class="bar-container">
                                <div class="bar-fill" style="width: {{ $pctMerah }}%; background: var(--danger);"></div>
                            </div>
                            <span class="bar-value">{{ $pctMerah }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Patients Table --}}
        <div class="col-lg-7 fade-up">
            <div class="modern-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-users"></i> Pasien Terbaru</span>
                    <a href="{{ route('admin.pasien.index') }}" class="btn btn-view-all">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Pasien</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPasien as $pasien)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                            $initial = strtoupper(substr($pasien->user->name ?? 'P', 0, 1));
                                            $color = $avatarColors[ord($initial) % count($avatarColors)];
                                        @endphp
                                        <div class="avatar-sm" style="background: {{ $color }};">{{ $initial }}</div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-primary);">{{ $pasien->user->name ?? '-' }}</div>
                                            <div style="font-size: 0.72rem; color: var(--text-secondary);">ID: {{ $pasien->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($pasien->status_kepatuhan == 'hijau')
                                        <span class="badge-status badge-hijau">Patuh</span>
                                    @elseif($pasien->status_kepatuhan == 'kuning')
                                        <span class="badge-status badge-kuning">Peringatan</span>
                                    @elseif($pasien->status_kepatuhan == 'merah')
                                        <span class="badge-status badge-merah">Drop-out</span>
                                    @else
                                        <span class="badge-status badge-pending">{{ ucfirst($pasien->status_kepatuhan ?? '-') }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.8rem; color: var(--text-secondary);">
                                    {{ $pasien->created_at ? $pasien->created_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4" style="color: var(--text-secondary);">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block" style="color: #cbd5e1;"></i>
                                    Belum ada data pasien
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Bottom Row: Refill + Quick Actions ===== --}}
    <div class="row g-4 mb-4">
        {{-- Recent Refill --}}
        <div class="col-lg-8 fade-up">
            <div class="modern-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-pills"></i> Refill ARV Terbaru</span>
                    <a href="{{ route('admin.refill.index') }}" class="btn btn-view-all">
                        Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Pasien</th>
                                <th>Siklus Ke</th>
                                <th>Tanggal Refill</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentRefill as $refill)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $rInitial = strtoupper(substr($refill->pasien->user->name ?? 'R', 0, 1));
                                            $rColor = $avatarColors[ord($rInitial) % count($avatarColors)];
                                        @endphp
                                        <div class="avatar-sm" style="background: {{ $rColor }};">{{ $rInitial }}</div>
                                        <span style="font-weight: 600;">{{ $refill->pasien->user->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 600; color: var(--primary);">Siklus {{ $refill->siklus_ke ?? '-' }}</span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary);">
                                    {{ $refill->tanggal_refill ? \Carbon\Carbon::parse($refill->tanggal_refill)->format('d M Y') : '-' }}
                                </td>
                                <td>
                                    @if($refill->status == 'selesai' || $refill->status == 'completed')
                                        <span class="badge-status badge-hijau">Selesai</span>
                                    @elseif($refill->status == 'pending')
                                        <span class="badge-status badge-pending">Pending</span>
                                    @else
                                        <span class="badge-status badge-kuning">{{ ucfirst($refill->status ?? '-') }}</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4" style="color: var(--text-secondary);">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block" style="color: #cbd5e1;"></i>
                                    Belum ada data refill
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="col-lg-4 fade-up">
            <div class="modern-card h-100">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="quick-action-grid">
                        <a href="{{ route('admin.users.index') }}" class="quick-action-btn">
                            <i class="fas fa-user-check"></i>
                            Verifikasi User
                        </a>
                        <a href="{{ route('admin.pasien.index') }}" class="quick-action-btn">
                            <i class="fas fa-notes-medical"></i>
                            Monitoring
                        </a>
                        <a href="{{ route('admin.broadcast.index') }}" class="quick-action-btn">
                            <i class="fas fa-paper-plane"></i>
                            Broadcast
                        </a>
                        <a href="{{ route('admin.refill.index') }}" class="quick-action-btn">
                            <i class="fas fa-capsules"></i>
                            Refill ARV
                        </a>
                        <a href="{{ route('admin.edukasi.index') }}" class="quick-action-btn">
                            <i class="fas fa-book-medical"></i>
                            Modul Edukasi
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="quick-action-btn">
                            <i class="fas fa-file-medical-alt"></i>
                            Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Chart.js ===== --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ===== Doughnut Chart =====
    const ctx = document.getElementById('complianceChart').getContext('2d');

    const hijau = {{ $stats['kepatuhan_hijau'] }};
    const kuning = {{ $stats['kepatuhan_kuning'] }};
    const merah = {{ $stats['kepatuhan_merah'] }};

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Patuh (Hijau)', 'Peringatan (Kuning)', 'Drop-out (Merah)'],
            datasets: [{
                data: [hijau, kuning, merah],
                backgroundColor: [
                    '#059669',
                    '#d97706',
                    '#dc2626'
                ],
                borderColor: '#ffffff',
                borderWidth: 3,
                hoverOffset: 8,
                borderRadius: 4,
                spacing: 2,
            }]
        },
        options: {
            responsive: true,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10,
                        font: { family: 'Inter', size: 12, weight: '500' },
                        color: '#64748b'
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { family: 'Inter', weight: '600' },
                    bodyFont: { family: 'Inter' },
                }
            },
            animation: {
                animateRotate: true,
                duration: 1200,
            }
        }
    });

    // ===== Number Counter Animation =====
    document.querySelectorAll('.stat-number').forEach(el => {
        const target = parseInt(el.textContent);
        if (isNaN(target) || target === 0) return;
        el.textContent = '0';
        let current = 0;
        const step = Math.max(1, Math.ceil(target / 35));
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = current;
        }, 30);
    });
});
</script>
@endsection