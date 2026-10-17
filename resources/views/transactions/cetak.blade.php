<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        
        /* Style Header Kop Surat */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px double black; }
        .header-table td { border: none; padding: 5px; vertical-align: middle; }
        
        /* Style Judul Laporan */
        .judul-laporan { text-align: center; margin-bottom: 15px; }

        /* Style Tabel Data */
        .data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .data-table th, .data-table td { border: 1px solid #000; padding: 6px; text-align: left; }
        .data-table th { background-color: #f0f0f0; text-align: center; }
        
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 30px; text-align: right; page-break-inside: avoid; }
        
        .summary-row td { border-top: 2px solid #000; font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
           <td width="15%" class="text-center">
                @php
                    $logoPath = public_path(config('laporan.logo'));
                @endphp

                @if (file_exists($logoPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" style="height: 80px; width: auto;">
                @endif
            </td>
            
            <td width="85%" class="text-center">
                <h2 style="margin:0; font-size: 18px; text-transform: uppercase;">{{ config('laporan.instansi.pemerintah') }}</h2>
                <h1 style="margin:5px 0; font-size: 20px; text-transform: uppercase;">{{ config('laporan.instansi.kantor') }}</h1>
                <p style="margin:0; font-size: 11px; font-style: italic;">Alamat: {{ config('laporan.instansi.alamat') }}</p>
            </td>
        </tr>
    </table>

    <div class="judul-laporan">
        <h3 style="margin:0; text-transform: uppercase; text-decoration: underline;">{{ $judulLaporan }}</h3>
        <p style="margin:5px 0;">Laporan Realisasi Keuangan</p>
        <small>Dicetak pada: {{ date('d F Y H:i') }}</small>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Pos Anggaran</th>
                <th width="25%">Uraian / Keterangan</th>
                <th width="15%">Penerima</th>
                <th width="10%">Sumber Dana</th>
                <th width="15%">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($transactions as $t)
            <tr>
                <td class="text-center">{{ $no++ }}</td>
                <td>{{ $t->tanggal->format('d/m/Y') }}</td>
                <td>{{ $t->category->nama ?? '-' }}</td>
                <td>{{ $t->keterangan }}</td>
                <td>{{ $t->penerima }}</td>
                <td class="text-center">{{ $t->budgetStage->nama_tahap ?? '-' }}</td>
                <td class="text-end">{{ number_format($t->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        
        <tfoot>
            <tr class="summary-row">
                <td colspan="6" class="text-end">TOTAL REALISASI PENGELUARAN</td>
                <td class="text-end">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>

            <tr style="font-weight: bold; background-color: #e8f5e9;">
                <td colspan="6" class="text-end">TOTAL PAGU ANGGARAN (DANA MASUK)</td>
                <td class="text-end">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>

            <tr style="font-weight: bold; background-color: #e3f2fd; font-size: 13px;">
                <td colspan="6" class="text-end">SISA KAS (SALDO)</td>
                <td class="text-end">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Bendahara Pengeluaran,</p>
        <br><br><br>
        <p><strong style="text-decoration: underline;">{{ config('laporan.bendahara.nama') }}</strong><br>NIP. {{ config('laporan.bendahara.nip') }}</p>
    </div>

</body>
</html>