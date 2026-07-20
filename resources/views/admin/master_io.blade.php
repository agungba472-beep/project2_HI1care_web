@extends('layouts.v_template')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-viruses header-icon"></i>
        <h1>Master Infeksi Oportunistik</h1>
        <p>Kelola data Infeksi Oportunistik (IO) yang dapat diderita ODHA</p>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="hi-alert hi-alert-success fade-up">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="hi-alert hi-alert-error fade-up">
            <ul style="margin:0; padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-list"></i> Daftar Master IO</span>
            <button type="button" class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                <i class="fas fa-plus"></i> Tambah Data
            </button>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama IO</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ios as $index => $io)
                    <tr>
                        <td>
                            <span style="background: var(--surface); padding: 0.25rem 0.6rem; border-radius: 6px; font-weight: 600; font-size: 0.78rem; color: var(--text-secondary);">
                                {{ $index + 1 }}
                            </span>
                        </td>
                        <td><strong>{{ $io->nama_io }}</strong></td>
                        <td>{{ $io->deskripsi }}</td>
                        <td>
                            @if($io->status_aktif)
                                <span style="background: var(--primary-light); color: var(--primary); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Aktif</span>
                            @else
                                <span style="background: var(--error-light); color: var(--error); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <button class="hi-btn hi-btn-outline-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#editModal{{ $io->id }}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal{{ $io->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Master IO</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.master.io.update', $io->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-secondary">Nama IO</label>
                                            <input type="text" name="nama_io" class="hi-input" value="{{ $io->nama_io }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-secondary">Deskripsi (Opsional)</label>
                                            <textarea name="deskripsi" class="hi-input" rows="3">{{ $io->deskripsi }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold text-secondary">Status Aktif</label>
                                            <select name="status_aktif" class="hi-input" required>
                                                <option value="1" {{ $io->status_aktif ? 'selected' : '' }}>Aktif</option>
                                                <option value="0" {{ !$io->status_aktif ? 'selected' : '' }}>Nonaktif</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer" style="border-top: 1px solid var(--border-light); background: var(--surface);">
                                        <button type="button" class="hi-btn hi-btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            <i class="fas fa-inbox" style="font-size: 2rem; margin-bottom: 0.5rem; opacity: 0.5;"></i>
                            <div>Belum ada data master IO</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Master IO</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.master.io.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama IO</label>
                        <input type="text" name="nama_io" class="hi-input" required placeholder="Misal: TB Paru, Kandidiasis Oral">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="hi-input" rows="3" placeholder="Penjelasan singkat untuk Nakes"></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--border-light); background: var(--surface);">
                    <button type="button" class="hi-btn hi-btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
