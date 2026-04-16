@extends('layouts.v_template')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Laporan & Rekapitulasi</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ringkasan Data Pasien HI!-CARE</li>
    </ol>

    <div class="row">
        <div class="col-xl-6 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Pasien Terdaftar</div>
                        <h2 class="display-6 fw-bold mb-0">{{ $totalPasien }}</h2>
                    </div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="card bg-success text-white mb-4 shadow">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small">Total Refill Obat Disetujui</div>
                        <h2 class="display-6 fw-bold mb-0">{{ $totalRefillSelesai }}</h2>
                    </div>
                    <i class="fas fa-pills fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i> Data Detail Pasien
            </div>
            <button class="btn btn-sm btn-outline-success">
                <i class="fas fa-file-excel"></i> Export Excel
            </button>
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No. Registrasi HIV</th>
                        <th>Nama Pasien</th>
                        <th>Status Kepatuhan Terakhir</th>
                        <th>Tanggal Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dataLaporan as $laporan)
                    <tr>
                        <td class="text-center">{{ $laporan->master->no_reg_hiv ?? 'Belum ada' }}</td>
                        <td>{{ $laporan->user->nama ?? 'Data User Hilang' }}</td>
                        <td class="text-center">
                            <span class="badge {{ $laporan->status_kepatuhan == 'hijau' ? 'bg-success' : 'bg-danger' }}">
                                {{ strtoupper($laporan->status_kepatuhan ?? 'BELUM ADA DATA') }}
                            </span>
                        </td>
                        <td>{{ $laporan->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection