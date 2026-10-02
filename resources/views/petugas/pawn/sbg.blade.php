<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Surat Bukti Gadai (SBG) — {{ $pawnTransaction->ticket_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { border: 1px solid #000 !important; box-shadow: none !important; max-width: 100% !important; margin: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-appBg text-textPrimary p-4 sm:p-6 antialiased">

    {{-- Top Action Bar (Visible on screen only) --}}
    <div class="no-print w-full max-w-2xl mx-auto mb-6 space-y-4">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-card border border-success/30 bg-success/15 px-4 py-3.5 text-sm text-success shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 bg-surface p-4 rounded-card border border-border shadow-sm">
            <div>
                <h2 class="text-sm font-bold text-textPrimary">Dokumen Surat Bukti Gadai (SBG)</h2>
                <p class="text-xs text-textSecondary">Simpan sebagai PDF atau cetak fisik untuk nasabah.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    <span>Unduh PDF / Cetak SBG</span>
                </button>
                <a href="{{ route('petugas.pawn.create') }}" class="rounded-lg border border-border px-3 py-2.5 text-xs font-semibold hover:bg-appBg transition">
                    + Transaksi Baru
                </a>
                <a href="{{ route('petugas.pawn.index') }}" class="rounded-lg border border-border px-3 py-2.5 text-xs font-semibold hover:bg-appBg transition">
                    Daftar Gadai
                </a>
            </div>
        </div>
    </div>

    {{-- Official SBG Document Box --}}
    <div class="print-container w-full max-w-2xl mx-auto bg-white border-2 border-primary/40 rounded-card p-6 sm:p-8 shadow-card">
        {{-- Header --}}
        <div class="text-center border-b-2 border-primary pb-4 mb-6">
            <h1 class="text-xl font-black text-primary tracking-wide uppercase">KONTER BUULOLO CELL99</h1>
            <p class="text-xs text-textSecondary font-medium">Layanan Jasa Gadai Barang Elektronik & Jual Beli HP Bekas Terpercaya</p>
            <div class="mt-3 inline-block bg-primary text-white text-xs font-bold px-4 py-1 rounded-full uppercase tracking-wider">
                SURAT BUKTI GADAI (SBG)
            </div>
        </div>

        {{-- Meta Data Grid --}}
        <table class="w-full text-xs sm:text-sm border-collapse mb-6">
            <tbody>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary w-1/3">Nomor Tiket SBG</td>
                    <td class="py-2.5 font-mono font-bold text-primary text-base">{{ $pawnTransaction->ticket_number }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Tanggal Transaksi</td>
                    <td class="py-2.5 font-medium">{{ $pawnTransaction->pawn_date->format('d F Y') }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Durasi Tenor Gadai</td>
                    <td class="py-2.5 font-semibold text-textPrimary">{{ $pawnTransaction->tenor_days }} Hari</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Tanggal Jatuh Tempo</td>
                    <td class="py-2.5 font-bold text-danger text-base">{{ $pawnTransaction->due_date->format('d F Y') }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Nama Nasabah / Pemilik</td>
                    <td class="py-2.5 font-semibold">{{ $pawnTransaction->customer->name ?? '-' }} (NIK: {{ $pawnTransaction->customer->identity_number ?? '-' }})</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Nomor Kontak (HP)</td>
                    <td class="py-2.5">{{ $pawnTransaction->customer->phone ?? '-' }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Barang Jaminan</td>
                    <td class="py-2.5 font-bold text-textPrimary">{{ $pawnTransaction->item->name ?? '-' }} ({{ $pawnTransaction->item->category ?? '-' }})</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Kondisi & Kelengkapan</td>
                    <td class="py-2.5 text-textSecondary">{{ $pawnTransaction->item->condition_notes ?? 'Sesuai pemeriksaan awal' }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Nilai Taksiran Barang</td>
                    <td class="py-2.5 font-semibold">Rp {{ number_format($pawnTransaction->estimated_value, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-border/80 bg-primary/5">
                    <td class="py-2.5 px-2 font-bold text-primary">Nilai Plafon Pinjaman</td>
                    <td class="py-2.5 px-2 font-bold text-primary text-base">Rp {{ number_format($pawnTransaction->loan_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b border-border/80">
                    <td class="py-2.5 font-bold text-textSecondary">Bunga Gadai ({{ $pawnTransaction->interest_rate }}%)</td>
                    <td class="py-2.5 font-semibold text-textPrimary">Rp {{ number_format($pawnTransaction->interest_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-b-2 border-primary bg-success/5">
                    <td class="py-3 px-2 font-bold text-success text-base">TOTAL TEBUS LUNAS</td>
                    <td class="py-3 px-2 font-bold text-success text-lg">Rp {{ number_format($pawnTransaction->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="py-2.5 font-bold text-textSecondary">Petugas Kasir</td>
                    <td class="py-2.5 text-textSecondary">{{ $pawnTransaction->user->name ?? 'Petugas' }}</td>
                </tr>
            </tbody>
        </table>

        {{-- Barcode Display Box --}}
        <div class="my-6 p-4 bg-appBg rounded-lg border border-border text-center">
            <div class="text-[10px] text-textSecondary uppercase font-bold tracking-wider mb-1">KODE BARCODE SBG (SCAN DI KASIR)</div>
            <div class="font-mono text-lg font-black tracking-widest text-primary">*{{ $pawnTransaction->barcode_code }}*</div>
        </div>

        {{-- Signature Section --}}
        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-8 pt-4">
            <div>
                <p class="font-medium text-textSecondary">Nasabah / Pemilik Jaminan</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[150px]">{{ $pawnTransaction->customer->name ?? '....................' }}</p>
            </div>
            <div>
                <p class="font-medium text-textSecondary">Petugas Konter Buulolo Cell99</p>
                <div class="h-16"></div>
                <p class="font-bold border-t border-border pt-1 inline-block min-w-[150px]">{{ $pawnTransaction->user->name ?? '....................' }}</p>
            </div>
        </div>

        {{-- Footer Notes --}}
        <div class="mt-8 pt-4 border-t border-border text-[10px] text-textSecondary leading-relaxed text-justify">
            <p class="font-bold text-textPrimary mb-1">Ketentuan Penggadaian:</p>
            <ol class="list-decimal list-inside space-y-0.5">
                <li>Surat Bukti Gadai ini adalah bukti sah kepemilikan jaminan yang wajib ditunjukkan saat pelunasan/perpanjangan.</li>
                <li>Barang yang tidak ditebus atau diperpanjang sampai batas tanggal jatuh tempo akan diproses sesuai aturan server status SIAP LELANG.</li>
            </ol>
        </div>
    </div>

</body>
</html>
