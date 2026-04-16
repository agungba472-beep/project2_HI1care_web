@extends('layouts.v_template')

@section('title', 'Monitoring Kepatuhan Pasien - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-notes-medical header-icon"></i>
        <h1>Monitoring Kepatuhan Pasien</h1>
        <p>Pantau data pasien dan status kepatuhan pengobatan</p>
    </div>

    {{-- Patient Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-users"></i> Daftar Pasien Terdaftar</span>
            <button class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                <i class="fas fa-plus"></i> Tambah Pasien Manual
            </button>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th>No. Rekam Medis</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Lahir</th>
                        <th>No. Telepon</th>
                        <th>Fase Pengobatan</th>
                        <th>Status Akun</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td><span class="hi-code">{{ $patient->no_rekam_medis ?? '-' }}</span></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $avatarColors = ['#0891b2','#0e7490','#059669','#2563eb','#7c3aed','#d97706'];
                                    $pIni = strtoupper(substr($patient->nama_lengkap ?? 'P', 0, 1));
                                    $pCol = $avatarColors[ord($pIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $pCol }}">{{ $pIni }}</div>
                                <span style="font-weight: 600;">{{ $patient->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td style="font-size: 0.82rem; color: var(--text-secondary);">
                            {{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d/m/Y') }}
                        </td>
                        <td style="font-size: 0.82rem;">{{ $patient->no_telepon }}</td>
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
                                <button class="hi-btn hi-btn-warning hi-btn-sm toggle-status-btn" data-id="{{ $patient->id }}" title="Ubah Status">
                                    <i class="fas fa-power-off"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
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