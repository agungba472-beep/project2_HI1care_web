<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rekam Medis - {{ $patient->master->nama ?? ($patient->user->nama ?? 'Pasien') }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #059669;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #012D1D;
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #666;
            font-size: 14px;
        }
        .section-title {
            color: #059669;
            font-size: 18px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 15px;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #012D1D;
            font-weight: bold;
        }
        .biodata-table td {
            border: none;
            padding: 5px;
        }
        .biodata-table td:first-child {
            width: 150px;
            font-weight: bold;
            color: #555;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            color: #fff;
            font-weight: bold;
            font-size: 11px;
        }
        .bg-hijau { background-color: #10b981; }
        .bg-kuning { background-color: #f59e0b; }
        .bg-merah { background-color: #ef4444; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>REKAM MEDIS PASIEN HIV</h1>
        <p>Aplikasi WEAR (Web & Mobile Application for HIV Care)</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}</p>
    </div>

    <div class="section-title">A. Biodata Pasien</div>
    <table class="biodata-table">
        <tr>
            <td>No. Register HIV</td>
            <td>: {{ $patient->master->no_reg_hiv ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Lengkap</td>
            <td>: {{ $patient->master->nama ?? ($patient->user->nama ?? '-') }}</td>
        </tr>
        <tr>
            <td>NIK</td>
            <td>: {{ $patient->master->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td>Jenis Kelamin</td>
            <td>: {{ $patient->master->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Lahir</td>
            <td>: {{ $patient->master->tgl_lahir ? \Carbon\Carbon::parse($patient->master->tgl_lahir)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $patient->master->alamat ?? '-' }}</td>
        </tr>
        <tr>
            <td>Fase Pengobatan</td>
            <td>: {{ $patient->fase_pengobatan ?? 'Inisiasi' }}</td>
        </tr>
    </table>

    <div class="section-title">B. Ringkasan Kepatuhan (Bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</div>
    <table class="biodata-table">
        <tr>
            <td>Persentase Pencapaian Terapi</td>
            <td>: <strong>{{ $adherenceRate }}%</strong> ({{ $diminumCount }} kali patuh dari {{ \Carbon\Carbon::now()->daysInMonth }} hari)</td>
        </tr>
        <tr>
            <td>Status Kedisiplinan</td>
            <td>: 
                @php $status = $statusWarna ?? 'hijau'; @endphp
                <span class="badge bg-{{ $status }}">
                    {{ strtoupper($status) }}
                </span>
            </td>
        </tr>
    </table>

    <div class="section-title">C. Riwayat Kepatuhan Minum Obat</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal & Waktu</th>
                <th style="width: 25%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patient->kepatuhan->sortByDesc('last_update') as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($record->last_update)->format('d M Y, H:i') }} WIB</td>
                <td>
                    @php
                        $kBadge = match($record->status) {
                            'diminum', 'tepat waktu', 'hijau' => 'Patuh (Diminum)',
                            'tunda', 'kuning' => 'Waspada (Tunda)',
                            'terlewat', 'merah' => 'Beresiko (Terlewat)',
                            default => ucfirst($record->status ?? '-'),
                        };
                    @endphp
                    {{ $kBadge }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada riwayat kepatuhan</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">D. Diary Kesehatan Harian</div>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 70%;">Kondisi Kesehatan Pasien</th>
            </tr>
        </thead>
        <tbody>
            @forelse($patient->diaryHarian->sortByDesc('tanggal') as $index => $diary)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($diary->tanggal)->format('d M Y') }}</td>
                <td>{{ ucfirst($diary->kondisi ?? '-') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="text-center">Belum ada catatan diary</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini digenerate secara otomatis oleh Sistem WEAR.<br>
        Bukan untuk keperluan resep.
    </div>

</body>
</html>
