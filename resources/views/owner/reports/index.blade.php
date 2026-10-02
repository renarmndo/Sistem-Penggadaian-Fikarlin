<x-app-layout title="Laporan Transaksi & Laba Rugi" role="owner">
    <x-page-header title="Laporan Transaksi & Laba / Rugi Real-Time (FR-3.4)" description="Rekapitulasi transaksi penggadaian, pendapatan bunga/denda, pembelian barang bekas, penjualan lelang, dan laba rugi bersih.">
        <x-slot:actions>
            <a href="{{ route('owner.reports.print', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" target="_blank" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark">
                🖨️ Cetak Laporan Real-Time
            </a>
        </x-slot:actions>
    </x-page-header>

    {{-- Filter Tanggal Periode --}}
    <x-content-card class="mb-6">
        <form method="GET" action="{{ route('owner.reports.index') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-textSecondary uppercase mb-1">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary" />
            </div>
            <div>
                <label class="block text-xs font-semibold text-textSecondary uppercase mb-1">Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary" />
            </div>
            <button type="submit" class="rounded-lg bg-primary px-5 py-2 text-sm font-semibold text-white hover:bg-primaryDark">
                Filter Periode
            </button>
        </form>
    </x-content-card>

    {{-- Financial Summary Grid --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Pendapatan Bunga</p>
            <p class="mt-1 text-xl font-bold text-success">Rp {{ number_format($totalBungaMasuk, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Pendapatan Denda</p>
            <p class="mt-1 text-xl font-bold text-success">Rp {{ number_format($totalDendaMasuk, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Penjualan Lelang / Etalase</p>
            <p class="mt-1 text-xl font-bold text-primary">Rp {{ number_format($totalPenjualanLelang, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Estimasi Laba Kotor Periode</p>
            <p class="mt-1 text-2xl font-bold {{ $labaKotor >= 0 ? 'text-success' : 'text-danger' }}">
                Rp {{ number_format($labaKotor, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Table 1: Transaksi Gadai Baru --}}
        <x-content-card title="1. Rekapitulasi Gadai Baru Periode Terpilih (Total Pencairan: Rp {{ number_format($totalPinjamanDicairkan, 0, ',', '.') }})">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border bg-appBg text-textSecondary uppercase font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">No. Tiket</th>
                            <th class="py-2.5 px-3">Nasabah</th>
                            <th class="py-2.5 px-3">Barang Jaminan</th>
                            <th class="py-2.5 px-3 text-right">Plafon</th>
                            <th class="py-2.5 px-3 text-right">Bunga</th>
                            <th class="py-2.5 px-3">Tanggal Gadai</th>
                            <th class="py-2.5 px-3">Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse ($pawnTransactions as $p)
                            <tr>
                                <td class="py-2 px-3 font-mono font-bold text-primary">{{ $p->ticket_number }}</td>
                                <td class="py-2 px-3 font-medium">{{ $p->customer->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-textSecondary">{{ $p->item->name ?? '-' }}</td>
                                <td class="py-2 px-3 text-right font-semibold">Rp {{ number_format($p->loan_amount, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-success">Rp {{ number_format($p->interest_amount, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-textSecondary">{{ $p->pawn_date->format('d/m/Y') }}</td>
                                <td class="py-2 px-3 text-textSecondary">{{ $p->user->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 px-3 text-center text-textSecondary">Tidak ada transaksi gadai baru pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-content-card>

        {{-- Table 2: Pembayaran Bunga & Pelunasan --}}
        <x-content-card title="2. Rekapitulasi Pelunasan & Perpanjangan (Arus Kas Masuk)">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border bg-appBg text-textSecondary uppercase font-semibold">
                        <tr>
                            <th class="py-2.5 px-3">No Pembayaran</th>
                            <th class="py-2.5 px-3">Nasabah</th>
                            <th class="py-2.5 px-3">Tipe</th>
                            <th class="py-2.5 px-3 text-right">Bunga</th>
                            <th class="py-2.5 px-3 text-right">Denda</th>
                            <th class="py-2.5 px-3 text-right">Total Diterima</th>
                            <th class="py-2.5 px-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        @forelse ($pawnPayments as $pm)
                            <tr>
                                <td class="py-2 px-3 font-mono font-bold text-primary">{{ $pm->payment_number }}</td>
                                <td class="py-2 px-3 font-medium">{{ $pm->pawnTransaction->customer->name ?? '-' }}</td>
                                <td class="py-2 px-3 font-bold uppercase text-xs">{{ $pm->payment_type }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-success">Rp {{ number_format($pm->interest_amount, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-semibold text-danger">Rp {{ number_format($pm->penalty_amount, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-bold text-textPrimary">Rp {{ number_format($pm->total_paid, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-textSecondary">{{ $pm->payment_date->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-4 px-3 text-center text-textSecondary">Tidak ada pembayaran pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-content-card>
    </div>
</x-app-layout>
