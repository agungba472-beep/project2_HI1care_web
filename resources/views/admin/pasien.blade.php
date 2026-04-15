@extends('layouts.v_template')

@section('title', 'Manajemen Data Pasien - HI!-CARE')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Data Pasien</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Pasien</li>
    </ol>

    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Daftar Pasien Terdaftar
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                <i class="fas fa-plus"></i> Tambah Pasien Manual
            </button>
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No. Rekam Medis</th>
                        <th>Nama Lengkap</th>
                        <th>Tanggal Lahir</th>
                        <th>No. Telepon</th>
                        <th>Fase Pengobatan</th>
                        <th>Status Akun</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                    <tr>
                        <td>{{ $patient->no_rekam_medis ?? '-' }}</td>
                        <td>{{ $patient->nama_lengkap }}</td>
                        <td>{{ \Carbon\Carbon::parse($patient->tanggal_lahir)->format('d/m/Y') }}</td>
                        <td>{{ $patient->no_telepon }}</td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $patient->fase_pengobatan ?? 'Inisiasi' }}</span>
                        </td>
                        <td>
                            @if($patient->user && $patient->user->is_active)
                                <span class="badge bg-success status-badge" data-id="{{ $patient->id }}">Aktif</span>
                            @else
                                <span class="badge bg-danger status-badge" data-id="{{ $patient->id }}">Non-aktif</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.pasien.show', $patient->id) }}" class="btn btn-sm btn-info text-white" title="Lihat Detail Rekam">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                            <button class="btn btn-sm btn-warning toggle-status-btn" data-id="{{ $patient->id }}" title="Ubah Status Akses">
                                <i class="fas fa-power-off"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
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
        // Logika untuk mengubah status pasien (AJAX)
        const toggleButtons = document.querySelectorAll('.toggle-status-btn');
        
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.getAttribute('data-id');
                
                Swal.fire({
                    title: 'Konfirmasi Perubahan Status',
                    text: "Apakah Anda yakin ingin mengubah status akses pasien ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proses Fetch API untuk toggle status
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
                                .then(() => location.reload()); // Muat ulang tabel untuk update state UI
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