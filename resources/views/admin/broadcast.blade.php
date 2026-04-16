@extends('layouts.v_template')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Pesan Broadcast</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Kirim Informasi dan Modul Edukasi ke Semua Pasien</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-paper-plane me-1"></i> Buat Pesan Baru
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.broadcast.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="judul" class="form-label fw-bold">Judul Pesan</label>
                            <input type="text" class="form-control" id="judul" name="judul" placeholder="Contoh: Jadwal Pengambilan Obat Libur Nasional" required>
                        </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label fw-bold">Isi Pesan / Edukasi</label>
                            <textarea class="form-control" id="pesan" name="pesan" rows="5" placeholder="Tuliskan pesan lengkap di sini..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-send"></i> Kirim Broadcast Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i> Riwayat Pesan Terkirim
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal & Waktu</th>
                                <th>Judul Pesan</th>
                                <th>Cuplikan Isi Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($broadcasts as $b)
                            <tr>
                                <td>{{ $b->created_at->format('d M Y - H:i') }}</td>
                                <td class="fw-bold">{{ $b->judul }}</td>
                                <td>{{ Str::limit($b->pesan, 60, '...') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection