<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kuitansi Penjualan Barang Lelang/Etalase — {{ $item->item_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { border: 1px solid #000 !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-appBg text-textPrimary p-4 sm:p-6 antialiased">

    {{-- Top Action Bar (Screen Only) --}}
    <div class="no-print w-full max-w-xl mx-auto mb-6 space-y-4">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-card border border-success/30 bg-success/15 px-4 py-3.5 text-sm text-success shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 bg-surface p-4 rounded-card border border-border shadow-sm">
            <div>
                <h2 class="text-sm font-bold text-textPrimary">Kuitansi Bukti Penjualan</h2>
                <p class="text-xs text-textSecondary">Simpan sebagai PDF atau cetak kuitansi penjualan.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    <span>Unduh PDF / Cetak</span>
                </button>
                <a href="{{ route('petugas.auction.index') }}" class="rounded-lg border border-border px-3 py-2.5 text-xs font-semibold hover:bg-appBg transition">
                    Daftar Etalase / Lelang
                </a>
            </div>
        </div>
    </div>

    {{-- Official Sales Receipt Box --}}
    <div class="print-container w-full max-w-xl mx-auto bg-white border-2 border-primary/40 rounded-card p-6 sm:p-8 shadow-card text-sm">
        <div class="text-center border-b-2 border-primary pb-4 mb-6">
            <h1 class="text-lg font-black text-primary uppercase">KONTER BUULOLO CELL99</h1>
            <p class="text-xs text-textSecondary font-medium">Kuitansi Resmi Penjualan Barang Lelang / Etalase Toko</p>
            <div class="mt-2 font-mono text-xs font-bold text-primary">Kode Barang: {{ $item->item_code }}</div>
        </div>

        <table class="w-full text-xs sm:text-sm border-collapse mb-6">
            <tbody>
                <tr class="border-b border-border">
                    <td class="py-2.5 font-bold text-textSecondary w-1/3">Tanggal Penjualan</td>
                    <td class="py-2.5 font-semibold">{{ \Carbon\Carbon::now()->format('d F Y') }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-2.5 font-bold text-textSecondary">Nama Pembeli</td>
                    <td class="py-2.5 font-bold text-primary">{{ $buyerName ?? 'Pembeli' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-2.5 font-bold text-textSecondary">Nama Barang</td>
                    <td class="py-2.5 font-bold text-textPrimary">{{ $item->name }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-2.5 font-bold text-textSecondary">Kategori / Merk</td>
                    <td class="py-2.5 text-textSecondary">{{ $item->category ?? '-' }} / {{ $item->brand ?? '-' }}</td>
                </tr>
                <tr class="border-b border-border">
                    <td class="py-2.5 font-bold text-textSecondary">Kondisi Barang</td>
                    <td class="py-2.5 text-textSecondary">{{ $item->condition_notes ?? 'Sesuai pemeriksaan' }}</td>
                </tr>
                <tr class="border-b-2 border-primary bg-success/5">
                    <td class="py-3 px-2 font-bold text-success text-base">HARGA PENJUALAN</td>
                    <td class="py-3 px-2 font-bold text-success text-lg">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 font-bold text-textSecondary">Status Barang</td>
                    <td class="py-2.5 font-bold text-success uppercase">TERJUAL</td>
                </tr>
            </tbody>
        </table>

        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-8 pt-4">
            <div>
                <p class="font-medium text-textSecondary">Pembeli Barang</p>
                <div class="h-14"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[140px]">{{ $buyerName ?? '....................' }}</p>
            </div>
            <div>
                <p class="font-medium text-textSecondary">Petugas Kasir</p>
                <div class="h-14"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[140px]">{{ auth()->user()->name ?? 'Petugas' }}</p>
            </div>
        </div>
    </div>

</body>
</html>
