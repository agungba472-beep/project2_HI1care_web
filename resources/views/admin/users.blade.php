@extends('layouts.v_template')

@section('title', 'Manajemen Pengguna - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-users-cog header-icon"></i>
        <h1>Manajemen Pengguna</h1>
        <p>Verifikasi pendaftar baru, kelola data master pasien, tenaga kesehatan, dan pasien aktif</p>
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

    {{-- ===== Tab Navigation ===== --}}
    <ul class="nav hi-tabs fade-up" id="userTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab">
                <i class="fas fa-user-clock me-1"></i> Menunggu Verifikasi
                @if($pendingUsers->count() > 0)
                    <span class="hi-badge hi-badge-danger ms-1">{{ $pendingUsers->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button" role="tab">
                <i class="fas fa-database me-1"></i> Data Master Pasien
                <span class="hi-badge hi-badge-muted ms-1">{{ $pasienMaster->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="nakes-tab" data-bs-toggle="tab" data-bs-target="#nakes" type="button" role="tab">
                <i class="fas fa-user-nurse me-1"></i> Tenaga Kesehatan
                <span class="hi-badge hi-badge-muted ms-1">{{ $nakes->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pasien-tab" data-bs-toggle="tab" data-bs-target="#pasienAktif" type="button" role="tab">
                <i class="fas fa-user-check me-1"></i> Pasien Aktif
                <span class="hi-badge hi-badge-muted ms-1">{{ $activePatients->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="userTabContent">

        {{-- ==================== TAB 1: MENUNGGU VERIFIKASI (FR-A01) ==================== --}}
        <div class="tab-pane fade show active" id="pending" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-user-clock"></i> Daftar Pendaftar Baru — Menunggu Persetujuan Admin</span>
                    <span class="hi-badge {{ $pendingUsers->count() > 0 ? 'hi-badge-danger' : 'hi-badge-success' }}">
                        {{ $pendingUsers->count() }} antrian
                    </span>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th>Tanggal Daftar</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th style="width: 200px;">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingUsers as $index => $user)
                            <tr>
                                <td>
                                    <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; font-size: 0.85rem;">
                                        <i class="fas fa-calendar me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                        {{ $user->created_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--text-secondary);">
                                        <i class="fas fa-clock me-1"></i>{{ $user->created_at->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                            $uIni = strtoupper(substr($user->nama ?? 'U', 0, 1));
                                            $uCol = $avatarColors[ord($uIni) % count($avatarColors)];
                                        @endphp
                                        <div class="hi-avatar" style="background:{{ $uCol }}">{{ $uIni }}</div>
                                        <span style="font-weight: 600;">{{ $user->nama }}</span>
                                    </div>
                                </td>
                                <td><span class="hi-code">{{ $user->username }}</span></td>
                                <td>
                                    @if($user->role == 'pasien')
                                        <span class="hi-badge hi-badge-info"><i class="fas fa-user me-1"></i>Pasien</span>
                                    @elseif($user->role == 'nakes')
                                        <span class="hi-badge hi-badge-warning"><i class="fas fa-user-nurse me-1"></i>Nakes</span>
                                    @else
                                        <span class="hi-badge hi-badge-muted">{{ ucfirst($user->role) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="hi-btn hi-btn-success hi-btn-sm" onclick="return confirm('Setujui pendaftaran {{ $user->nama }}?')">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" onclick="return confirm('Tolak pendaftaran {{ $user->nama }}? Akun akan ditandai ditolak.')">
                                                <i class="fas fa-times"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="hi-empty">
                                        <i class="fas fa-check-double"></i>
                                        <p>Tidak ada pendaftar baru yang menunggu verifikasi. Semua sudah diproses! 🎉</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 2: DATA MASTER PASIEN ==================== --}}
        <div class="tab-pane fade" id="master" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-database"></i> Daftar No. Reg HIV Sah (Whitelist)</span>
                    <button class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMaster">
                        <i class="fas fa-plus"></i> Tambah Master Pasien
                    </button>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th>No. Reg HIV</th>
                                <th>Nama Pasien</th>
                                <th>Status Registrasi</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pasienMaster as $index => $m)
                            <tr>
                                <td>
                                    <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td><span class="hi-code">{{ $m->no_reg_hiv }}</span></td>
                                <td style="font-weight: 600;">{{ $m->nama }}</td>
                                <td>
                                    @if($m->is_registered)
                                        <span class="hi-badge hi-badge-success"><i class="fas fa-check-circle me-1"></i>Sudah Punya Akun</span>
                                    @else
                                        <span class="hi-badge hi-badge-muted"><i class="fas fa-clock me-1"></i>Belum Daftar di HP</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('admin.master.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data master ini?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="hi-empty">
                                        <i class="fas fa-database"></i>
                                        <p>Belum ada data master pasien. Tambahkan melalui tombol di atas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 3: TENAGA KESEHATAN ==================== --}}
        <div class="tab-pane fade" id="nakes" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-user-nurse"></i> Daftar Tenaga Kesehatan</span>
                    <button class="hi-btn hi-btn-success hi-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahNakes">
                        <i class="fas fa-user-plus"></i> Tambah Nakes Baru
                    </button>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th>Nama Nakes</th>
                                <th>Profesi / Spesialis</th>
                                <th>No. SIP</th>
                                <th>No. HP</th>
                                <th>Status Akun</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nakes as $index => $n)
                            <tr>
                                <td>
                                    <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                            $nIni = strtoupper(substr($n->nama ?? 'N', 0, 1));
                                            $nCol = $avatarColors[ord($nIni) % count($avatarColors)];
                                        @endphp
                                        <div class="hi-avatar" style="background:{{ $nCol }}">{{ $nIni }}</div>
                                        <div>
                                            <span style="font-weight: 600;">{{ $n->nama }}</span>
                                            <div style="font-size: 0.72rem; color: var(--text-secondary);">
                                                {{ $n->user->username ?? '-' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="hi-badge hi-badge-info">{{ $n->profesi ?? 'Umum' }}</span></td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary);">
                                    {{ $n->no_sip ?? '-' }}
                                </td>
                                <td style="font-size: 0.82rem;">
                                    @if($n->no_hp)
                                        <i class="fas fa-phone me-1" style="color: var(--success); font-size: 0.7rem;"></i>{{ $n->no_hp }}
                                    @else
                                        <span style="color: var(--text-secondary);">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->user && $n->user->status_akun == 'aktif')
                                        <span class="hi-badge hi-badge-success"><i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i>Aktif</span>
                                    @else
                                        <span class="hi-badge hi-badge-danger"><i class="fas fa-circle me-1" style="font-size: 0.4rem;"></i>Non-aktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if($n->user)
                                    <form action="{{ route('admin.users.destroy', $n->user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun nakes {{ $n->nama }}?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">
                                    <div class="hi-empty">
                                        <i class="fas fa-user-nurse"></i>
                                        <p>Belum ada data tenaga kesehatan. Daftarkan melalui tombol di atas.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ==================== TAB 4: PASIEN AKTIF ==================== --}}
        <div class="tab-pane fade" id="pasienAktif" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-user-check"></i> Daftar Pasien dengan Akun Aktif</span>
                    <span class="hi-badge hi-badge-success">{{ $activePatients->count() }} pasien</span>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th style="width: 45px;">No</th>
                                <th>No. Reg HIV</th>
                                <th>Nama Pasien</th>
                                <th>Username</th>
                                <th>Status Kepatuhan</th>
                                <th>Tgl Bergabung</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activePatients as $index => $p)
                            <tr>
                                <td>
                                    <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td><span class="hi-code">{{ $p->master->no_reg_hiv ?? '-' }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $pNama = $p->master->nama ?? ($p->user->nama ?? 'P');
                                            $pIni = strtoupper(substr($pNama, 0, 1));
                                            $pCol = $avatarColors[ord($pIni) % count($avatarColors)];
                                        @endphp
                                        <div class="hi-avatar" style="background:{{ $pCol }}">{{ $pIni }}</div>
                                        <span style="font-weight: 600;">{{ $pNama }}</span>
                                    </div>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary);">
                                    {{ $p->user->username ?? '-' }}
                                </td>
                                <td>
                                    @php
                                        $sk = $p->status_kepatuhan ?? 'hijau';
                                        $skMap = match($sk) {
                                            'hijau' => ['class' => 'hi-badge-success', 'label' => 'Patuh'],
                                            'kuning' => ['class' => 'hi-badge-warning', 'label' => 'Waspada'],
                                            'merah' => ['class' => 'hi-badge-danger', 'label' => 'Beresiko'],
                                            default => ['class' => 'hi-badge-muted', 'label' => '-'],
                                        };
                                    @endphp
                                    <span class="hi-badge {{ $skMap['class'] }}">{{ ucfirst($sk) }} — {{ $skMap['label'] }}</span>
                                </td>
                                <td style="font-size: 0.82rem; color: var(--text-secondary);">
                                    @if($p->user && $p->user->created_at)
                                        <i class="fas fa-calendar me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                        {{ $p->user->created_at->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="hi-empty">
                                        <i class="fas fa-users"></i>
                                        <p>Belum ada pasien dengan akun aktif.</p>
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

{{-- ==================== MODAL: TAMBAH DATA MASTER PASIEN ==================== --}}
<div class="modal fade hi-modal" id="modalTambahMaster" tabindex="-1" aria-labelledby="modalTambahMasterLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahMasterLabel"><i class="fas fa-database me-2"></i>Input No. Reg HIV Sah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size: 0.82rem; color: var(--text-secondary); margin-bottom: 1rem;">
                        <i class="fas fa-info-circle me-1"></i> Input nomor ini agar pasien bisa mendaftar mandiri melalui aplikasi mobile.
                    </p>
                    <div class="mb-3">
                        <label class="form-label">No. Reg Nasional HIV</label>
                        <input type="text" name="no_reg_hiv" class="form-control" placeholder="Contoh: 12.34.56.78" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Pasien (Sesuai KTP)</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama pasien" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL: REGISTRASI NAKES BARU ==================== --}}
<div class="modal fade hi-modal" id="modalTambahNakes" tabindex="-1" aria-labelledby="modalTambahNakesLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.storeNakes') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahNakesLabel"><i class="fas fa-user-plus me-2"></i>Registrasi Akun Nakes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username (untuk Login)</label>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Awal</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bidang / Spesialisasi</label>
                        <input type="text" name="spesialisasi" class="form-control" placeholder="Contoh: Dokter Umum, Konselor">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. SIP</label>
                        <input type="text" name="no_sip" class="form-control" placeholder="Nomor Surat Izin Praktik">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 08123456789">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-success"><i class="fas fa-check"></i> Daftarkan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection