<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Transaksi & Laba Rugi — Konter Buulolo Cell99</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; margin: 0; padding: 20px; color: #111827; }
        .report-box { max-width: 800px; margin: auto; border: 1px solid #1E3A5F; padding: 20px; border-radius: 6px; }
        .header { text-align: center; border-bottom: 2px solid #1E3A5F; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { margin: 0; font-size: 18px; color: #1E3A5F; }
        .header p { margin: 2px 0; font-size: 11px; color: #4B5563; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .table th, .table td { padding: 6px 8px; border: 1px solid #E5E7EB; text-align: left; }
        .table th { background: #F3F4F6; font-size: 10px; text-transform: uppercase; }
        .summary-box { background: #F9FAFB; border: 1px solid #D1D5DB; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .flex { display: flex; justify-content: space-between; margin: 4px 0; }
        .bold { font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .report-box { border: none; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="max-width: 800px; margin: 0 auto 10px auto; text-align: right;">
        <button onclick="window.print()" style="background: #1E3A5F; color: white; border: none; padding: 8px 16px; font-size: 12px; font-weight: bold; border-radius: 4px; cursor: pointer;">
            🖨️ Cetak / Save PDF Laporan
        </button>
    </div>

    <div class="report-box">
        <div class="header">
            <h1>KONTER BUULOLO CELL99</h1>
            <p>LAPORAN TRANSAKSI & LABA RUGI OPERASIONAL PENGGADAIAN</p>
            <p style="font-weight: bold; color: #1E3A5F; margin-top: 5px;">
                Periode: {{ $startDate->format('d/m/Y') }} s/d {{ $endDate->format('d/m/Y') }}
            </p>
        </div>

        {{-- Financial Summary --}}
        <div class="summary-box">
            <h3 style="margin: 0 0 8px 0; font-size: 12px; color: #1E3A5F;">RINGKASAN KEUANGAN PERIODE</h3>
            <div class="flex">
                <span>Total Pendapatan Bunga Gadai:</span>
                <span class="bold" style="color: #2E9E5B;">+ Rp {{ number_format($totalBungaMasuk, 0, ',', '.') }}</span>
            </div>
            <div class="flex">
                <span>Total Pendapatan Denda:</span>
                <span class="bold" style="color: #2E9E5B;">+ Rp {{ number_format($totalDendaMasuk, 0, ',', '.') }}</span>
            </div>
            <div class="flex">
                <span>Total Hasil Penjualan Lelang / Etalase:</span>
                <span class="bold" style="color: #1E3A5F;">+ Rp {{ number_format($totalPenjualanLelang, 0, ',', '.') }}</span>
            </div>
            <div class="flex">
                <span>Total Pengeluaran Beli Barang Bekas (Kas Keluar):</span>
                <span class="bold" style="color: #D64545;">- Rp {{ number_format($totalPengeluaranBeli, 0, ',', '.') }}</span>
            </div>
            <div style="border-top: 1px solid #CBD5E1; margin-top: 8px; padding-top: 6px;" class="flex bold">
                <span style="font-size: 13px;">ESTIMASI LABA KOTAR PERIODE:</span>
                <span style="font-size: 14px; color: {{ $labaKotor >= 0 ? '#2E9E5B' : '#D64545' }};">
                    Rp {{ number_format($labaKotor, 0, ',', '.') }}
                </span>
            </div>
        </div>

        {{-- Detail Gadai Baru --}}
        <h3 style="font-size: 12px; color: #1E3A5F; margin-bottom: 6px;">1. Detail Transaksi Gadai Baru</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>No Tiket</th>
                    <th>Nasabah</th>
                    <th>Barang</th>
                    <th style="text-align: right;">Plafon</th>
                    <th style="text-align: right;">Bunga</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pawnTransactions as $p)
                    <tr>
                        <td style="font-family: monospace;">{{ $p->ticket_number }}</td>
                        <td>{{ $p->customer->name ?? '-' }}</td>
                        <td>{{ $p->item->name ?? '-' }}</td>
                        <td style="text-align: right;">Rp {{ number_format($p->loan_amount, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($p->interest_amount, 0, ',', '.') }}</td>
                        <td>{{ $p->pawn_date->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada transaksi gadai baru.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Detail Pembayaran --}}
        <h3 style="font-size: 12px; color: #1E3A5F; margin-bottom: 6px;">2. Detail Pelunasan & Perpanjangan</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>No Pembayaran</th>
                    <th>Nasabah</th>
                    <th>Tipe</th>
                    <th style="text-align: right;">Bunga</th>
                    <th style="text-align: right;">Denda</th>
                    <th style="text-align: right;">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pawnPayments as $pm)
                    <tr>
                        <td style="font-family: monospace;">{{ $pm->payment_number }}</td>
                        <td>{{ $pm->pawnTransaction->customer->name ?? '-' }}</td>
                        <td style="text-transform: uppercase;">{{ $pm->payment_type }}</td>
                        <td style="text-align: right;">Rp {{ number_format($pm->interest_amount, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($pm->penalty_amount, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: bold;">Rp {{ number_format($pm->total_paid, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center;">Tidak ada transaksi pembayaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 40px; display: flex; justify-content: flex-end;">
            <div style="text-align: center; width: 200px;">
                <p>Owner Buulolo Cell99</p>
                <br><br><br>
                <p><strong>( Owner )</strong></p>
            </div>
        </div>
    </div>

</body>
</html>
