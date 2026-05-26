<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kepatuhan Pasien ODHA</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; color: #000; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SISTEM INFORMASI HI!-CARE</h1>
        <p>Laporan Monitoring Kepatuhan Minum Obat & Refill Pasien (ODHA)</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>No. Reg HIV</th>
                <th>Nama Pasien</th>
                <th>Tanggal Lahir</th>
                <th>Status Kepatuhan</th>
                <th>Siklus Refill Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataLaporan as $index => $pasien)
                @php
                    $kepatuhan = strtoupper($pasien->status_kepatuhan ?? 'HIJAU');
                    $lastRefill = $pasien->refill->sortByDesc('siklus_ke')->first();
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $pasien->master->no_reg_hiv ?? '-' }}</td>
                    <td>{{ $pasien->master->nama ?? ($pasien->user->nama ?? '-') }}</td>
                    <td>{{ $pasien->master->tgl_lahir ?? '-' }}</td>
                    <td style="font-weight: bold;">{{ $kepatuhan }}</td>
                    <td>{{ $lastRefill ? 'Siklus ke-' . $lastRefill->siklus_ke : 'Belum Pernah' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pasien.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(!headers_sent())
        <script>
            window.onload = function() { window.print(); }
        </script>
    @endif
</body>
</html>