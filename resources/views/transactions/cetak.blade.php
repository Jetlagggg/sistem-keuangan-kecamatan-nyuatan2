<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Keuangan</title>
    <style>
        @page { size: A4 landscape; margin: 15mm 14mm; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 10.5px; color: #111; line-height: 1.45; }

        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; border-bottom: 3px double #111; }
        .header-table td { border: none; padding: 4px 6px; vertical-align: middle; }

        .judul-laporan { text-align: center; margin-bottom: 14px; }
        .judul-laporan h3 { margin: 0; font-size: 13px; text-transform: uppercase; text-decoration: underline; letter-spacing: .02em; }
        .judul-laporan p { margin: 4px 0 2px; font-size: 10.5px; }
        .judul-laporan small { font-family: "DejaVu Sans Mono", monospace; font-size: 9px; color: #444; }

        .data-table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        .data-table thead { display: table-header-group; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 5px 6px; text-align: left; vertical-align: top; }
        .data-table th { background-color: #f0f0ef; text-align: center; font-size: 9.5px; text-transform: uppercase; letter-spacing: .03em; }
        .data-table tr { page-break-inside: avoid; }

        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .mono { font-family: "DejaVu Sans Mono", monospace; font-size: 9.5px; }
        .footer { margin-top: 26px; text-align: right; page-break-inside: avoid; }
        .footer p { margin: 0; }

        .summary-row td { border-top: 3px double #111; font-weight: bold; background-color: #f7f7f6; font-size: 10px; }
        .summary-row.final td { border-top: 1px solid #333; font-size: 11px; background-color: #eee; }
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
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" style="height: 70px; width: auto;">
                @endif
            </td>

            <td width="85%" class="text-center">
                <h2 style="margin:0; font-size: 15px; text-transform: uppercase; letter-spacing: .03em;">{{ config('laporan.instansi.pemerintah') }}</h2>
                <h1 style="margin:4px 0; font-size: 17px; text-transform: uppercase; letter-spacing: .04em;">{{ config('laporan.instansi.kantor') }}</h1>
                <p style="margin:0; font-size: 9.5px; font-style: italic;">Alamat: {{ config('laporan.instansi.alamat') }}</p>
            </td>
        </tr>
    </table>

    <div class="judul-laporan">
        <h3>{{ $judulLaporan }}</h3>
        <p>Laporan Realisasi Keuangan</p>
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
                <td class="text-center mono">{{ $no++ }}</td>
                <td class="mono">{{ $t->tanggal->format('d/m/Y') }}</td>
                <td>{{ $t->category->nama ?? '-' }}</td>
                <td>{{ $t->keterangan }}</td>
                <td>{{ $t->penerima }}</td>
                <td class="text-center">{{ $t->budgetStage->nama_tahap ?? '-' }}</td>
                <td class="text-end mono">{{ number_format($t->nominal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr class="summary-row">
                <td colspan="6" class="text-end">TOTAL REALISASI PENGELUARAN</td>
                <td class="text-end mono">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</td>
            </tr>

            <tr class="summary-row">
                <td colspan="6" class="text-end">TOTAL PAGU ANGGARAN (DANA MASUK)</td>
                <td class="text-end mono">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</td>
            </tr>

            <tr class="summary-row final">
                <td colspan="6" class="text-end">SISA KAS (SALDO)</td>
                <td class="text-end mono">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
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
