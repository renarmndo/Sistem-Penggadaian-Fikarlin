<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekap Kasir Harian — {{ $targetDate->format('d/m/Y') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { border: none !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-appBg text-textPrimary p-4 sm:p-6 antialiased text-xs">

    {{-- Top Action Bar (Screen Only) --}}
    <div class="no-print w-full max-w-4xl mx-auto mb-6 flex items-center justify-between bg-surface p-4 rounded-card border border-border shadow-sm">
        <div>
            <h2 class="text-sm font-bold text-textPrimary">Lembar Rekap Kasir Harian</h2>
            <p class="text-xs text-textSecondary">Tanggal: {{ $targetDate->format('d F Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-primaryDark transition cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                <span>Unduh PDF / Cetak Laporan</span>
            </button>
            <a href="{{ route('petugas.transactions.daily') }}" class="rounded-lg border border-border px-3 py-2 text-xs font-semibold hover:bg-appBg transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Printable Container --}}
    <div class="print-container w-full max-w-4xl mx-auto bg-white border border-border rounded-card p-6 sm:p-8 shadow-card">
        {{-- Header --}}
        <div class="text-center border-b-2 border-primary pb-4 mb-6">
            <h1 class="text-lg font-black text-primary uppercase">KONTER BUULOLO CELL99</h1>
            <p class="text-xs text-textSecondary font-medium">Laporan Rekapitulasi Kasir & Arus Kas Harian</p>
            <p class="text-xs font-bold text-textPrimary mt-1">Tanggal Operasional: {{ $targetDate->format('d F Y') }}</p>
        </div>

        {{-- KPI Summary Boxes --}}
        <div class="grid grid-cols-4 gap-3 mb-6 text-center">
            <div class="p-3 rounded border border-success/30 bg-success/5">
                <span class="text-[10px] text-textSecondary uppercase font-bold">Total Kas Masuk</span>
                <p class="text-sm font-black text-success mt-1">Rp {{ number_format($stats['cash_in'], 0, ',', '.') }}</p>
            </div>
            <div class="p-3 rounded border border-danger/30 bg-danger/5">
                <span class="text-[10px] text-textSecondary uppercase font-bold">Total Kas Keluar</span>
                <p class="text-sm font-black text-danger mt-1">Rp {{ number_format($stats['cash_out'], 0, ',', '.') }}</p>
            </div>
            <div class="p-3 rounded border border-primary/30 bg-primary/5">
                <span class="text-[10px] text-textSecondary uppercase font-bold">Saldo Kas Bersih</span>
                <p class="text-sm font-black text-primary mt-1">Rp {{ number_format($stats['net_flow'], 0, ',', '.') }}</p>
            </div>
            <div class="p-3 rounded border border-border bg-appBg">
                <span class="text-[10px] text-textSecondary uppercase font-bold">Total Transaksi</span>
                <p class="text-sm font-black text-textPrimary mt-1">{{ $stats['count'] }} Lembar</p>
            </div>
        </div>

        {{-- Table --}}
        <table class="w-full text-left text-xs border-collapse border border-border mb-6">
            <thead>
                <tr class="bg-appBg border-b border-border font-bold text-textSecondary">
                    <th class="p-2 border-r border-border w-12 text-center">No</th>
                    <th class="p-2 border-r border-border">Waktu</th>
                    <th class="p-2 border-r border-border">No. Dokumen</th>
                    <th class="p-2 border-r border-border">Jenis Transaksi</th>
                    <th class="p-2 border-r border-border">Nasabah / Pihak Terkait</th>
                    <th class="p-2 border-r border-border">Keterangan Barang</th>
                    <th class="p-2 border-r border-border text-right">Kas Masuk (Rp)</th>
                    <th class="p-2 text-right">Kas Keluar (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse ($transactions as $idx => $tx)
                    <tr>
                        <td class="p-2 border-r border-border text-center">{{ $idx + 1 }}</td>
                        <td class="p-2 border-r border-border font-mono">{{ $tx['time'] ? \Carbon\Carbon::parse($tx['time'])->format('H:i') : '-' }}</td>
                        <td class="p-2 border-r border-border font-mono font-bold text-primary">{{ $tx['doc_number'] }}</td>
                        <td class="p-2 border-r border-border font-medium">{{ $tx['type_label'] }}</td>
                        <td class="p-2 border-r border-border">{{ $tx['party_name'] }}</td>
                        <td class="p-2 border-r border-border">{{ $tx['item_desc'] }}</td>
                        <td class="p-2 border-r border-border text-right font-bold text-success">
                            {{ $tx['cash_flow'] === 'in' ? number_format($tx['amount'], 0, ',', '.') : '-' }}
                        </td>
                        <td class="p-2 text-right font-bold text-danger">
                            {{ $tx['cash_flow'] === 'out' ? number_format($tx['amount'], 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="p-4 text-center text-textSecondary">Tidak ada transaksi tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-appBg border-t-2 border-border font-bold">
                    <td colspan="6" class="p-2 border-r border-border text-right uppercase">TOTAL ARUS KAS:</td>
                    <td class="p-2 border-r border-border text-right text-success">Rp {{ number_format($stats['cash_in'], 0, ',', '.') }}</td>
                    <td class="p-2 text-right text-danger">Rp {{ number_format($stats['cash_out'], 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Signatures --}}
        <div class="grid grid-cols-2 gap-8 text-center mt-10 pt-4 text-xs">
            <div>
                <p class="font-medium text-textSecondary">Petugas Kasir Bertugas</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[150px]">{{ auth()->user()->name ?? 'Petugas Kasir' }}</p>
            </div>
            <div>
                <p class="font-medium text-textSecondary">Mengetahui (Owner / Admin)</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[150px]">( ........................................ )</p>
            </div>
        </div>
    </div>

</body>
</html>
