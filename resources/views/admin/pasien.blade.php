@extends('layouts.v_template')

@section('title', 'Monitoring Kepatuhan Pasien - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-notes-medical header-icon"></i>
        <h1>Monitoring Kepatuhan Pasien</h1>
        <p>Pantau data pasien, status kepatuhan pengobatan, dan ekspor laporan</p>
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

    {{-- Filter & Ekspor Card --}}
    <div class="hi-card fade-up" style="margin-bottom: 1.5rem;">
        <div class="hi-card-header">
            <span><i class="fas fa-filter"></i> Filter & Ekspor Data</span>
        </div>
        <div class="hi-card-body">
            <form action="{{ route('admin.pasien.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><i class="fas fa-search me-1"></i>Cari Nama Pasien</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Ketik nama pasien...">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><i class="fas fa-heartbeat me-1"></i>Status Kepatuhan</label>
                        <select class="form-select" name="status">
                            <option value="">Semua Status</option>
                            <option value="hijau" {{ request('status') == 'hijau' ? 'selected' : '' }}>🟢 Hijau (Patuh)</option>
                            <option value="kuning" {{ request('status') == 'kuning' ? 'selected' : '' }}>🟡 Kuning (Waspada)</option>
                            <option value="merah" {{ request('status') == 'merah' ? 'selected' : '' }}>🔴 Merah (Beresiko)</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="hi-btn hi-btn-primary hi-btn-sm">
                                <i class="fas fa-search"></i> Cari
                            </button>
                            <a href="{{ route('admin.pasien.index') }}" class="hi-btn hi-btn-outline hi-btn-sm">
                                <i class="fas fa-redo"></i> Reset
                            </a>
                            <a href="{{ route('admin.laporan.export', request()->query()) }}" class="hi-btn hi-btn-sm" style="background: #059669; color: #fff; border: none;">
                                <i class="fas fa-file-excel"></i> Ekspor Excel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Patient Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-users"></i> Daftar Pasien Terdaftar</span>
            <button class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th>No. Reg HIV</th>
                        <th>Nama Pasien</th>
                        <th>Status Kepatuhan</th>
                        <th>Tanggal Lahir</th>
                        <th>Alamat</th>
                        <th>Fase Pengobatan</th>
                        <th>Status Akun</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td><span class="hi-code">{{ $patient->master->no_reg_hiv ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                    $pName = $patient->master->nama ?? ($patient->user->nama ?? 'P');
                                    $pIni = strtoupper(substr($pName, 0, 1));
                                    $pCol = $avatarColors[ord($pIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $pCol }}">{{ $pIni }}</div>
                                <span style="font-weight: 600;">{{ $pName }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                                $statusKepatuhan = $patient->status_kepatuhan ?? 'hijau';
                                $badgeClass = match($statusKepatuhan) {
                                    'hijau' => 'hi-badge-success',
                                    'kuning' => 'hi-badge-warning',
                                    'merah' => 'hi-badge-danger',
                                    default => 'hi-badge-info',
                                };
                                $statusLabel = match($statusKepatuhan) {
                                    'hijau' => 'Patuh',
                                    'kuning' => 'Waspada',
                                    'merah' => 'Beresiko',
                                    default => '-',
                                };
                            @endphp
                            <span class="hi-badge {{ $badgeClass }}">
                                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> {{ ucfirst($statusKepatuhan) }} — {{ $statusLabel }}
                            </span>
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-secondary);">
                            {{ $patient->master->tgl_lahir ? \Carbon\Carbon::parse($patient->master->tgl_lahir)->format('d/m/Y') : '-' }}
                        </td>
                        <td style="font-size: 0.82rem;">{{ $patient->master->alamat ?? '-' }}</td>
                        <td>
                            <span class="hi-badge hi-badge-info">{{ $patient->fase_pengobatan ?? 'Inisiasi' }}</span>
                        </td>
                        <td>
                            @if($patient->user && $patient->user->is_active)
                                <span class="hi-badge hi-badge-success" data-id="{{ $patient->id }}">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Aktif
                                </span>
                            @else
                                <span class="hi-badge hi-badge-danger" data-id="{{ $patient->id }}">
                                    <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> Non-aktif
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.pasien.show', $patient->id) }}" class="hi-btn hi-btn-info hi-btn-sm" title="Detail">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="hi-empty">
                                <i class="fas fa-users"></i>
                                <p>Belum ada data pasien terdaftar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Tambah Pasien --}}
<div class="modal fade hi-modal" id="addPatientModal" tabindex="-1" aria-labelledby="addPatientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPatientModalLabel"><i class="fas fa-user-plus me-2"></i>Tambah Data Pasien Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pasien.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required placeholder="Nama sesuai KTP">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Rekam Medis (Opsional)</label>
                            <input type="text" class="form-control" name="no_rekam_medis" placeholder="Misal: RM-12345">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username (Untuk Login App)</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required minlength="6">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="tanggal_lahir" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" class="form-control" name="no_telepon" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-status-btn');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    text: "Apakah Anda yakin ingin mengubah status akses pasien ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0891b2',
                    cancelButtonColor: '#dc2626',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/pasien/${patientId}/toggle-status`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if(data.success) {
                                Swal.fire('Berhasil!', data.message, 'success')
                                .then(() => location.reload());
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'Terjadi kesalahan pada server.', 'error');
                        });
                    }
                });
            });
        });
    });
</script>
@endpush