@extends('layouts.v_template')

@section('title', 'Jadwal Praktik Nakes - HI!-CARE')

@section('content')
@include('layouts.partials.admin-styles')

<div class="admin-page">
    {{-- Page Header --}}
    <div class="page-header fade-up">
        <i class="fas fa-calendar-alt header-icon"></i>
        <h1>Jadwal Praktik Tenaga Kesehatan</h1>
        <p>Kelola jadwal konsultasi nakes sebagai referensi booking pasien di aplikasi mobile</p>
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

    {{-- Jadwal Table --}}
    <div class="hi-card fade-up">
        <div class="hi-card-header">
            <span><i class="fas fa-clock"></i> Daftar Jadwal Praktik</span>
            <button class="hi-btn hi-btn-primary hi-btn-sm" data-bs-toggle="modal" data-bs-target="#addJadwalModal">
                <i class="fas fa-plus"></i> Tambah Jadwal Praktik
            </button>
        </div>
        <div class="hi-card-body" style="padding: 0;">
            <table class="hi-table">
                <thead>
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Nama Tenaga Kesehatan</th>
                        <th>Bidang</th>
                        <th>Hari Praktik</th>
                        <th>Jam Praktik</th>
                        <th>Kuota</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jadwals as $index => $jadwal)
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
                                    $nNama = $jadwal->nakes->nama ?? 'N';
                                    $nIni = strtoupper(substr($nNama, 0, 1));
                                    $nCol = $avatarColors[ord($nIni) % count($avatarColors)];
                                @endphp
                                <div class="hi-avatar" style="background:{{ $nCol }}">{{ $nIni }}</div>
                                <span style="font-weight: 600;">{{ $nNama }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="hi-badge hi-badge-info">{{ $jadwal->nakes->profesi ?? 'Umum' }}</span>
                        </td>
                        <td>
                            @php
                                $hariColor = match($jadwal->hari) {
                                    'Senin' => '#2563eb',
                                    'Selasa' => '#7c3aed',
                                    'Rabu' => '#059669',
                                    'Kamis' => '#d97706',
                                    'Jumat' => '#0891b2',
                                    'Sabtu' => '#dc2626',
                                    'Minggu' => '#be185d',
                                    default => '#64748b',
                                };
                            @endphp
                            <span style="background: {{ $hariColor }}15; color: {{ $hariColor }}; padding: 0.3rem 0.75rem; border-radius: 50px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.3px;">
                                {{ $jadwal->hari }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 600; font-size: 0.85rem;">
                                <i class="fas fa-clock me-1" style="color: var(--primary); font-size: 0.7rem;"></i>
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} — {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </div>
                        </td>
                        <td>
                            <span style="background: var(--surface); padding: 0.3rem 0.65rem; border-radius: 8px; font-weight: 700; font-size: 0.85rem; color: var(--text-primary);">
                                {{ $jadwal->kuota_pasien }}
                            </span>
                            <span style="font-size: 0.68rem; color: var(--text-secondary);"> pasien</span>
                        </td>
                        <td>
                            <form action="{{ route('admin.jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hi-btn hi-btn-danger hi-btn-sm" title="Hapus Jadwal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="hi-empty">
                                <i class="fas fa-calendar-times"></i>
                                <p>Belum ada jadwal praktik. Klik "Tambah Jadwal Praktik" untuk membuat.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal: Tambah Jadwal Praktik --}}
<div class="modal fade hi-modal" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addJadwalModalLabel"><i class="fas fa-calendar-plus me-2"></i>Tambah Jadwal Praktik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jadwal.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tenaga Kesehatan</label>
                        <select class="form-select" name="nakes_id" required>
                            <option value="" disabled selected>— Pilih Nakes —</option>
                            @foreach($nakesList as $nk)
                                <option value="{{ $nk->id }}">{{ $nk->nama }} — {{ $nk->profesi ?? 'Umum' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hari Praktik</label>
                        <select class="form-select" name="hari" required>
                            <option value="" disabled selected>— Pilih Hari —</option>
                            <option value="Senin">Senin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Kamis">Kamis</option>
                            <option value="Jumat">Jumat</option>
                            <option value="Sabtu">Sabtu</option>
                            <option value="Minggu">Minggu</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Mulai</label>
                            <input type="time" class="form-control" name="jam_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam Selesai</label>
                            <input type="time" class="form-control" name="jam_selesai" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kuota Pasien per Sesi</label>
                        <input type="number" class="form-control" name="kuota_pasien" min="1" max="100" value="10" required>
                        <small class="text-muted">Jumlah maksimal pasien yang bisa booking per sesi.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="hi-btn hi-btn-outline" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="hi-btn hi-btn-primary"><i class="fas fa-save"></i> Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
