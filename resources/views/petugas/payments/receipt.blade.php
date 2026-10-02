<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk Pembayaran — {{ $pawnPayment->payment_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .receipt-box { border: none !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="min-h-screen bg-appBg text-textPrimary p-4 sm:p-6 antialiased">

    {{-- Top Action Bar (Screen Only) --}}
    <div class="no-print w-full max-w-md mx-auto mb-6 space-y-4">
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-card border border-success/30 bg-success/15 px-4 py-3.5 text-sm text-success shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                <div class="flex-1 font-semibold">{{ session('success') }}</div>
            </div>
        @endif

        <div class="flex flex-wrap items-center justify-between gap-3 bg-surface p-4 rounded-card border border-border shadow-sm">
            <div>
                <h2 class="text-sm font-bold text-textPrimary">Struk Bukti Pembayaran</h2>
                <p class="text-xs text-textSecondary">Simpan sebagai PDF atau cetak struk kasir.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white shadow-sm hover:bg-primaryDark transition cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    <span>Unduh PDF / Cetak</span>
                </button>
                <a href="{{ route('petugas.payments.search') }}" class="rounded-lg border border-border px-3 py-2 text-xs font-semibold hover:bg-appBg transition">
                    Scan Lainnya
                </a>
            </div>
        </div>
    </div>

    {{-- Thermal/Receipt Document Box --}}
    <div class="receipt-box w-full max-w-md mx-auto bg-white border border-border rounded-card p-6 shadow-card font-mono text-xs">
        <div class="text-center border-b border-dashed border-border pb-3 mb-4">
            <h2 class="text-base font-black text-textPrimary">BUULOLO CELL99</h2>
            <p class="text-[11px] text-textSecondary font-sans">STRUK BUKTI PEMBAYARAN GADAI</p>
            <p class="text-[10px] text-primary font-bold mt-1">No. {{ $pawnPayment->payment_number }}</p>
        </div>

        <div class="space-y-1.5 mb-4">
            <div class="flex justify-between">
                <span class="text-textSecondary">Waktu Transaksi:</span>
                <span>{{ $pawnPayment->payment_date->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-textSecondary">Kasir:</span>
                <span>{{ $pawnPayment->user->name ?? 'Kasir' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-textSecondary">No. Tiket SBG:</span>
                <span class="font-bold text-primary">{{ $pawnPayment->pawnTransaction->ticket_number ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-textSecondary">Nama Nasabah:</span>
                <span class="font-semibold">{{ $pawnPayment->pawnTransaction->customer->name ?? '-' }}</span>
            </div>
        </div>

        <div class="border-t border-dashed border-border my-3"></div>

        <div class="space-y-1.5 mb-4">
            <div class="flex justify-between font-bold">
                <span class="uppercase">TIPE PEMBAYARAN:</span>
                <span class="uppercase {{ $pawnPayment->payment_type === 'pelunasan' ? 'text-success' : 'text-warning' }}">{{ $pawnPayment->payment_type }}</span>
            </div>

            @if ($pawnPayment->payment_type === 'pelunasan')
                <div class="flex justify-between">
                    <span class="text-textSecondary">Pokok Pinjaman:</span>
                    <span>Rp {{ number_format($pawnPayment->principal_amount, 0, ',', '.') }}</span>
                </div>
            @endif

            <div class="flex justify-between">
                <span class="text-textSecondary">Bunga Gadai:</span>
                <span>Rp {{ number_format($pawnPayment->interest_amount, 0, ',', '.') }}</span>
            </div>

            @if ($pawnPayment->penalty_amount > 0)
                <div class="flex justify-between text-danger">
                    <span>Denda Keterlambatan:</span>
                    <span>Rp {{ number_format($pawnPayment->penalty_amount, 0, ',', '.') }}</span>
                </div>
            @endif
        </div>

        <div class="border-t-2 border-border my-3"></div>

        <div class="flex justify-between items-center text-sm font-bold my-3">
            <span>TOTAL DIBAYAR:</span>
            <span class="text-base text-success">Rp {{ number_format($pawnPayment->total_paid, 0, ',', '.') }}</span>
        </div>

        <div class="border-t border-dashed border-border my-3"></div>

        @if ($pawnPayment->payment_type === 'perpanjangan')
            <div class="bg-warning/10 p-2.5 rounded text-center my-3 border border-warning/20">
                <span class="text-[11px] font-bold text-warning block">JATUH TEMPO BARU:</span>
                <span class="text-sm font-black text-danger">{{ $pawnPayment->new_due_date->format('d F Y') }}</span>
            </div>
        @else
            <div class="bg-success/10 p-2.5 rounded text-center my-3 border border-success/20">
                <span class="text-xs font-bold text-success block">STATUS: LUNAS</span>
                <span class="text-[10px] text-textSecondary">Barang Jaminan Telah Diserahkan Kepada Nasabah</span>
            </div>
        @endif

        <div class="text-center text-[10px] text-textSecondary mt-6 pt-3 border-t border-dashed border-border font-sans">
            <p class="font-bold text-textPrimary">Terima Kasih Atas Kunjungan Anda</p>
            <p>Simpan struk ini sebagai bukti pembayaran yang sah.</p>
        </div>
    </div>

</body>
</html>
