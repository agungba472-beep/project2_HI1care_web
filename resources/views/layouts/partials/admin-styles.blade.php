{{-- Shared Admin Page Styles - Include via @include('layouts.partials.admin-styles') --}}
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

    :root {
        --primary:       #0891b2;
        --primary-dark:  #0e7490;
        --primary-light: #a5f3fc;
        --secondary:     #1e3a5f;
        --accent:        #06b6d4;
        --success:       #059669;
        --success-light: #d1fae5;
        --warning:       #d97706;
        --warning-light: #fef3c7;
        --danger:        #dc2626;
        --danger-light:  #fee2e2;
        --info:          #2563eb;
        --info-light:    #dbeafe;
        --surface:       #f0fdfa;
        --text-primary:  #1e293b;
        --text-secondary:#64748b;
        --card-bg:       #ffffff;
        --border:        #e2e8f0;
    }

    .admin-page {
        font-family: 'Inter', sans-serif;
        background: var(--surface);
        min-height: 100vh;
        padding: 0 0.5rem;
    }

    /* ===== Page Header ===== */
    .page-header {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--primary-dark) 60%, var(--accent) 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: #fff;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(14, 116, 144, 0.2);
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -5%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(165,243,252,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .page-header h1 {
        font-weight: 800;
        font-size: 1.5rem;
        margin-bottom: 0.15rem;
    }
    .page-header p {
        opacity: 0.75;
        font-size: 0.85rem;
        margin: 0;
    }
    .page-header .header-icon {
        font-size: 2.5rem;
        opacity: 0.15;
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
    }

    /* ===== Modern Card ===== */
    .hi-card {
        border: 1px solid var(--border);
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(30, 58, 95, 0.05);
        overflow: hidden;
        background: var(--card-bg);
        transition: box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
    }
    .hi-card:hover {
        box-shadow: 0 6px 20px rgba(30, 58, 95, 0.08);
    }
    .hi-card .hi-card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border);
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafffe;
    }
    .hi-card .hi-card-header i {
        color: var(--primary);
        margin-right: 0.4rem;
    }
    .hi-card .hi-card-body {
        padding: 1.25rem 1.5rem;
    }

    /* ===== Modern Table ===== */
    .hi-table {
        width: 100%;
        font-size: 0.85rem;
        border-collapse: separate;
        border-spacing: 0;
    }
    .hi-table thead th {
        background: var(--surface);
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        padding: 0.8rem 1rem;
        border: none;
        border-bottom: 2px solid var(--border);
    }
    .hi-table tbody td {
        padding: 0.8rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        color: var(--text-primary);
    }
    .hi-table tbody tr {
        transition: background 0.2s ease;
    }
    .hi-table tbody tr:hover {
        background: #f0fdfa;
    }
    .hi-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* ===== Status Badges ===== */
    .hi-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        display: inline-block;
    }
    .hi-badge-success { background: var(--success-light); color: var(--success); }
    .hi-badge-warning { background: var(--warning-light); color: var(--warning); }
    .hi-badge-danger  { background: var(--danger-light); color: var(--danger); }
    .hi-badge-info    { background: var(--info-light); color: var(--info); }
    .hi-badge-muted   { background: #f1f5f9; color: var(--text-secondary); }

    /* ===== Buttons ===== */
    .hi-btn {
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.45rem 1rem;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }
    .hi-btn:hover { transform: translateY(-1px); }

    .hi-btn-primary   { background: var(--primary); color: #fff; }
    .hi-btn-primary:hover { background: var(--primary-dark); color: #fff; box-shadow: 0 4px 12px rgba(8,145,178,0.25); }

    .hi-btn-success   { background: var(--success); color: #fff; }
    .hi-btn-success:hover { background: #047857; color: #fff; box-shadow: 0 4px 12px rgba(5,150,105,0.25); }

    .hi-btn-danger    { background: var(--danger); color: #fff; }
    .hi-btn-danger:hover { background: #b91c1c; color: #fff; box-shadow: 0 4px 12px rgba(220,38,38,0.25); }

    .hi-btn-warning   { background: var(--warning); color: #fff; }
    .hi-btn-warning:hover { background: #b45309; color: #fff; box-shadow: 0 4px 12px rgba(217,119,6,0.25); }

    .hi-btn-info      { background: var(--info); color: #fff; }
    .hi-btn-info:hover { background: #1d4ed8; color: #fff; box-shadow: 0 4px 12px rgba(37,99,235,0.25); }

    .hi-btn-outline   { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); }
    .hi-btn-outline:hover { background: var(--primary); color: #fff; }

    .hi-btn-sm { font-size: 0.72rem; padding: 0.3rem 0.7rem; }

    /* ===== Modern Tabs ===== */
    .hi-tabs {
        border: none;
        gap: 0.25rem;
        margin-bottom: 1.5rem;
    }
    .hi-tabs .nav-link {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.82rem;
        padding: 0.6rem 1.2rem;
        transition: all 0.25s ease;
        margin-right: 0.35rem;
    }
    .hi-tabs .nav-link:hover {
        color: var(--primary);
        border-color: var(--primary);
        background: rgba(8, 145, 178, 0.04);
    }
    .hi-tabs .nav-link.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
    }

    /* ===== Modern Modal ===== */
    .hi-modal .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .hi-modal .modal-header {
        background: linear-gradient(135deg, var(--secondary), var(--primary-dark));
        color: #fff;
        border: none;
        padding: 1.25rem 1.5rem;
    }
    .hi-modal .modal-title {
        font-weight: 700;
        font-size: 1rem;
    }
    .hi-modal .modal-header .btn-close {
        filter: invert(1);
    }
    .hi-modal .modal-body {
        padding: 1.5rem;
    }
    .hi-modal .modal-body label {
        font-weight: 600;
        font-size: 0.82rem;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
    }
    .hi-modal .modal-body .form-control {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.6rem 0.85rem;
        font-size: 0.85rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .hi-modal .modal-body .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
    }
    .hi-modal .modal-footer {
        border-top: 1px solid var(--border);
        padding: 1rem 1.5rem;
    }

    /* ===== Form Styles ===== */
    .hi-form .form-label {
        font-weight: 600;
        font-size: 0.82rem;
        color: var(--text-primary);
        margin-bottom: 0.3rem;
    }
    .hi-form .form-control,
    .hi-form .form-select {
        border: 1.5px solid var(--border);
        border-radius: 10px;
        padding: 0.6rem 0.85rem;
        font-size: 0.85rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .hi-form .form-control:focus,
    .hi-form .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(8, 145, 178, 0.1);
    }

    /* ===== Alert ===== */
    .hi-alert {
        border: none;
        border-radius: 12px;
        padding: 0.85rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .hi-alert-success {
        background: var(--success-light);
        color: var(--success);
        border-left: 4px solid var(--success);
    }
    .hi-alert-danger {
        background: var(--danger-light);
        color: var(--danger);
        border-left: 4px solid var(--danger);
    }

    /* ===== Stat Mini Cards ===== */
    .hi-stat {
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        background: var(--card-bg);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .hi-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(14, 116, 144, 0.1);
    }
    .hi-stat .hi-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }
    .hi-stat .hi-stat-number {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
        line-height: 1;
    }
    .hi-stat .hi-stat-label {
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--text-secondary);
    }
    .hi-stat .hi-stat-accent {
        position: absolute;
        top: 0; left: 0;
        width: 4px; height: 100%;
    }

    /* ===== Avatar ===== */
    .hi-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.8rem;
        color: #fff;
    }

    /* ===== Empty State ===== */
    .hi-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: var(--text-secondary);
    }
    .hi-empty i { font-size: 2.5rem; color: #cbd5e1; margin-bottom: 0.75rem; display: block; }
    .hi-empty p { font-size: 0.85rem; margin: 0; }

    /* ===== Fade Animation ===== */
    .fade-up {
        opacity: 0;
        transform: translateY(15px);
        animation: fadeUp 0.5s ease forwards;
    }
    @keyframes fadeUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== Code Badge ===== */
    .hi-code {
        background: #f1f5f9;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--primary-dark);
    }
</style>
