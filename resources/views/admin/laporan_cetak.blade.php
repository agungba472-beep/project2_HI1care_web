<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kepatuhan Pasien ODHA</title>
    
    @if(isset($isWord) && $isWord)
        <style>
            body { font-family: 'Times New Roman', Times, serif; color: #000; line-height: 1.5; }
            .kop-surat { text-align: center; margin-bottom: 20px; }
            .kop-surat h1 { margin: 0; font-size: 20pt; font-weight: bold; }
            .kop-surat h3 { margin: 0; font-size: 14pt; font-weight: bold; }
            .kop-surat p { margin: 0; font-size: 10pt; }
            .garis-kop { border-bottom: 3px solid #000; margin-bottom: 20px; }
            .judul-laporan { text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #000; padding: 6px; font-size: 10pt; }
            th { background-color: #D3D3D3; text-align: center; font-weight: bold; }
            .text-center { text-align: center; }
            .signature-box { float: right; width: 250px; text-align: center; margin-top: 30px; font-size: 11pt; }
        </style>
    @else
        <style>
            body { font-family: 'Times New Roman', Times, serif; color: #333; margin: 20px; background: #fff; }
            .kop-surat { text-align: center; border-bottom: 4px double #000; padding-bottom: 10px; margin-bottom: 25px; }
            .kop-surat h1 { margin: 0; font-size: 24px; color: #012D1D; text-transform: uppercase; }
            .kop-surat h3 { margin: 5px 0; font-size: 16px; color: #444; }
            .kop-surat p { margin: 2px 0; font-size: 12px; color: #666; }
            .judul-laporan { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; }
            .info-tanggal { text-align: right; font-size: 11px; margin-bottom: 10px; color: #555; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 12px; }
            th, td { border: 1px solid #444; padding: 8px; text-align: left; }
            th { background-color: #012D1D; color: white; text-align: center; text-transform: uppercase; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            .text-center { text-align: center; }
            .badge-kepatuhan { font-weight: bold; }
            .hijau { color: #059669; }
            .kuning { color: #d97706; }
            .merah { color: #dc2626; }
            .signature-box { float: right; width: 250px; text-align: center; margin-top: 40px; font-size: 13px; }
            .signature-space { height: 70px; }
            .clearfix::after { content: ""; clear: both; display: table; }
        </style>
    @endif
</head>
<body>

    <div class="kop-surat">
        <h1>KLINIK VCT WEAR</h1>
        <h3>PELAYANAN & PENDAMPINGAN PASIEN ODHA</h3>
        <p>Jl. Kesehatan No. 123, Kota Subang, Jawa Barat, 41211</p>
        <p>Email: admin@hicare.com | Telp: (0260) 123456</p>
    </div>
    @if(isset($isWord) && $isWord) <div class="garis-kop"></div> @endif

    <div class="judul-laporan">LAPORAN MONITORING KEPATUHAN MINUM OBAT & REFILL</div>
    
    @if(!isset($isWord) || !$isWord)
        <div class="info-tanggal">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
    @else
        <p style="text-align: right; font-size: 10pt;">Tanggal Unduh: {{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</p>
    @endif

    <table @if(isset($isWord) && $isWord) border="1" cellspacing="0" cellpadding="5" @endif>
        <thead>
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="15%">No. Reg HIV</th>
                <th width="25%">Nama Pasien</th>
                <th width="15%">Tanggal Lahir</th>
                <th width="20%">Status Kepatuhan</th>
                <th width="20%">Siklus Refill Terakhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataLaporan as $index => $pasien)
                @php
                    $kepatuhan = strtolower($pasien->status_kepatuhan ?? 'hijau');
                    $lastRefill = $pasien->refill->sortByDesc('siklus_ke')->first();
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $pasien->master->no_reg_hiv ?? '-' }}</td>
                    <td>{{ $pasien->master->nama ?? ($pasien->user->nama ?? '-') }}</td>
                    <td class="text-center">{{ $pasien->master->tgl_lahir ?? '-' }}</td>
                    <td class="text-center @if(!isset($isWord) || !$isWord) badge-kepatuhan {{ $kepatuhan }} @endif" style="font-weight: bold;">
                        {{ strtoupper($kepatuhan) }}
                    </td>
                    <td class="text-center">{{ $lastRefill ? 'Siklus ke-' . $lastRefill->siklus_ke : 'Belum Pernah' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pasien.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="clearfix">
        <div class="signature-box">
            <p>Subang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
            <p>Penanggung Jawab Klinik,</p>
            @if(isset($isWord) && $isWord) <br><br><br> @else <div class="signature-space"></div> @endif
            <p><strong><u>Dr. {{ auth()->user()->name ?? 'Nakes WEAR' }}</u></strong><br>NIP. 19801010 200501 1 001</p>
        </div>
    </div>

    @if(!headers_sent() && (!isset($isWord) || !$isWord))
        <script> window.onload = function() { window.print(); } </script>
    @endif
</body>
</html>