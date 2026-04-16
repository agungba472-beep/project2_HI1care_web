@extends('layouts.v_template')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Manajemen Modul Edukasi</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Kelola informasi dan artikel untuk Pasien</li>
    </ol>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div><i class="fas fa-book-open me-1"></i> Daftar Modul Edukasi</div>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addEdukasiModal">
                <i class="fas fa-plus"></i> Tambah Modul
            </button>
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul Edukasi</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moduls as $index => $modul)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="fw-bold">{{ $modul->judul }}</td>
                        <td>{{ $modul->created_at->format('d M Y') }}</td>
                        <td>
                            <form action="{{ route('admin.edukasi.destroy', $modul->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus modul ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addEdukasiModal" tabindex="-1" aria-labelledby="addEdukasiModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addEdukasiModalLabel">Tambah Modul Edukasi Baru</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.edukasi.store') }}" method="POST">
          @csrf
          <div class="modal-body">
              <div class="mb-3">
                  <label for="judul" class="form-label">Judul Artikel / Edukasi</label>
                  <input type="text" class="form-control" id="judul" name="judul" required placeholder="Contoh: Pentingnya Minum ARV Tepat Waktu">
              </div>
              <div class="mb-3">
                  <label for="konten" class="form-label">Isi Konten</label>
                  <textarea class="form-control" id="konten" name="konten" rows="6" required placeholder="Tuliskan isi edukasi di sini..."></textarea>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Simpan Modul</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection