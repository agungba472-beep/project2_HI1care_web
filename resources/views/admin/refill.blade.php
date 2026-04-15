@extends('layouts.v_template')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Monitoring Refill Obat</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Pasien H-3 Jadwal Refill</li>
    </ol>

    <div class="card mb-4 shadow-sm">
        <div class="card-header">
            <i class="fas fa-pills me-1"></i> Data Refill Pasien
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>No. Reg HIV</th>
                        <th>Nama Pasien</th>
                        <th>Status Kepatuhan</th>
                        <th>Tgl Refill Berikutnya</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingRefills as $p)
                    <tr>
                        <td>{{ $p->master->no_reg_hiv }}</td>
                        <td>{{ $p->user->nama }}</td>
                        <td>
                            <span class="badge {{ $p->status_kepatuhan == 'hijau' ? 'bg-success' : 'bg-danger' }}">
                                {{ strtoupper($p->status_kepatuhan) }}
                            </span>
                        </td>
                        <td class="text-danger fw-bold">{{ $p->refill->last()->tgl_refill_berikutnya ?? '-' }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm">Kirim Pengingat</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection