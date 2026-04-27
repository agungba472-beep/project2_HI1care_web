@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-users-cog header-icon"></i>
        <h1>Manajemen Pengguna</h1>
        <p>Kelola data master pasien, tenaga kesehatan, dan akun pengguna</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="hi-alert hi-alert-success fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="hi-alert hi-alert-danger fade-up">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Tabs --}}
    <ul class="nav hi-tabs fade-up" id="userTab" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="master-tab" data-bs-toggle="tab" data-bs-target="#master" type="button">
                <i class="fas fa-database me-1"></i> Data Master Pasien
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="nakes-tab" data-bs-toggle="tab" data-bs-target="#nakes" type="button">
                <i class="fas fa-user-nurse me-1"></i> Tenaga Kesehatan
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button">
                <i class="fas fa-users me-1"></i> Semua Akun
            </button>
        </li>
    </ul>

    <div class="tab-content" id="userTabContent">
        {{-- Tab: Data Master Pasien --}}
        <div class="tab-pane fade show active" id="master" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-database"></i> Daftar No. Reg HIV Sah (Whitelist)</span>
                    <button class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMaster">
                        <i class="fas fa-plus"></i> Tambah Master
                    </button>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th>No. Reg HIV</th>
                                <th>Nama Pasien</th>
                                <th>Status Registrasi</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pasienMaster as $m)
                            <tr>
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
                                <td colspan="4">
                                    <div class="hi-empty">
                                        <i class="fas fa-database"></i>
                                        <p>Belum ada data master pasien</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab: Tenaga Kesehatan --}}
        <div class="tab-pane fade" id="nakes" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-user-nurse"></i> Daftar Tenaga Kesehatan</span>
                    <button class="hi-btn hi-btn-success hi-btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahNakes">
                        <i class="fas fa-user-plus"></i> Registrasi Nakes
                    </button>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Bidang / Spesialisasi</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($nakes as $n)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                            $ini = strtoupper(substr($n->user->nama ?? 'N', 0, 1));
                                            $col = $avatarColors[ord($ini) % count($avatarColors)];
                                        @endphp
                                        <div class="hi-avatar" style="background:{{ $col }}">{{ $ini }}</div>
                                        <span style="font-weight: 600;">{{ $n->user->nama }}</span>
                                    </div>
                                </td>
                                <td style="color: var(--text-secondary);">{{ $n->user->username }}</td>
                                <td><span class="hi-badge hi-badge-info">{{ $n->bidang ?? $n->profesi ?? 'Umum' }}</span></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button class="hi-btn hi-btn-warning hi-btn-sm" title="Edit"><i class="fas fa-edit"></i></button>
                                        <form action="{{ route('admin.users.destroy', $n->user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun nakes ini?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">
                                    <div class="hi-empty">
                                        <i class="fas fa-user-nurse"></i>
                                        <p>Belum ada data tenaga kesehatan</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Tab: Semua Akun --}}
        <div class="tab-pane fade" id="all" role="tabpanel">
            <div class="hi-card fade-up">
                <div class="hi-card-header">
                    <span><i class="fas fa-users"></i> Semua Akun Terdaftar</span>
                </div>
                <div class="hi-card-body" style="padding: 0;">
                    <table class="hi-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th style="width: 100px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $uIni = strtoupper(substr($user->nama ?? 'U', 0, 1));
                                            $uCol = $avatarColors[ord($uIni) % count($avatarColors)];
                                        @endphp
                                        <div class="hi-avatar" style="background:{{ $uCol }}">{{ $uIni }}</div>
                                        <span style="font-weight: 600;">{{ $user->nama }}</span>
                                    </div>
                                </td>
                                <td style="color: var(--text-secondary);">{{ $user->username }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="hi-badge hi-badge-danger">Admin</span>
                                    @elseif($user->role == 'nakes')
                                        <span class="hi-badge hi-badge-info">Nakes</span>
                                    @else
                                        <span class="hi-badge hi-badge-success">Pasien</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->status_akun == 'aktif')
                                        <span class="hi-badge hi-badge-success">Aktif</span>
                                    @elseif($user->status_akun == 'pending')
                                        <span class="hi-badge hi-badge-warning">Pending</span>
                                    @else
                                        <span class="hi-badge hi-badge-danger">{{ ucfirst($user->status_akun) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role !== 'admin')
                                    <div class="d-flex gap-1 flex-wrap">
                                        @if($user->status_akun === 'pending')
                                            <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="hi-btn hi-btn-success hi-btn-sm" title="Setujui" onclick="return confirm('Setujui akun {{ $user->nama }}?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="hi-btn hi-btn-warning hi-btn-sm" title="Tolak" onclick="return confirm('Tolak akun {{ $user->nama }}?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->nama }}?');" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="hi-empty">
                                        <i class="fas fa-users"></i>
                                        <p>Belum ada akun terdaftar</p>
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

{{-- Modal: Tambah Nakes --}}
<div class="modal fade hi-modal" id="modalTambahNakes" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.users.storeNakes') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Registrasi Akun Nakes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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

{{-- Modal: Tambah Master --}}
<div class="modal fade hi-modal" id="modalTambahMaster" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('admin.master.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-database me-2"></i>Input No. Reg HIV Sah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
@endsection