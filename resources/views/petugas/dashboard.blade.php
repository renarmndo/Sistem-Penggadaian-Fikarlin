<x-app-layout title="Dashboard Petugas" role="petugas">
    <x-page-header title="Dashboard Front-Office" description="Operasional transaksi harian kasir dan pelayanan pelanggan." />

    {{-- Shortcut Quick Action Cards --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('petugas.customers.index') }}" class="group rounded-card border border-border bg-surface p-5 shadow-card transition hover:border-primary/30 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-info/10 p-3 text-info transition group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-textPrimary">Data Nasabah (KTP)</h2>
                    <p class="text-xs text-textSecondary">Kelola master nasabah</p>
                </div>
            </div>
        </a>

        <a href="{{ route('petugas.pawn.create') }}" class="group rounded-card border border-border bg-surface p-5 shadow-card transition hover:border-primary/30 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-primary/10 p-3 text-primary transition group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-textPrimary">Transaksi Gadai Baru</h2>
                    <p class="text-xs text-textSecondary">Cetak SBG ber-barcode</p>
                </div>
            </div>
        </a>

        <a href="{{ route('petugas.payments.search') }}" class="group rounded-card border border-border bg-surface p-5 shadow-card transition hover:border-primary/30 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-success/10 p-3 text-success transition group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-textPrimary">Pelunasan & Perpanjangan</h2>
                    <p class="text-xs text-textSecondary">Scan barcode SBG</p>
                </div>
            </div>
        </a>

        <a href="{{ route('petugas.purchases.create') }}" class="group rounded-card border border-border bg-surface p-5 shadow-card transition hover:border-primary/30 hover:shadow-md">
            <div class="flex items-center gap-4">
                <div class="rounded-xl bg-warning/10 p-3 text-warning transition group-hover:scale-105">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-textPrimary">Beli Barang Bekas</h2>
                    <p class="text-xs text-textSecondary">Kas keluar & stok etalase</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Recent Transactions Table --}}
    <x-content-card title="Riwayat Transaksi Terbaru">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">No. Tiket / Barcode</th>
                        <th class="px-4 py-3">Nasabah</th>
                        <th class="px-4 py-3">Barang</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Plafon Pinjaman</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($recentTransactions as $tx)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-medium text-primary">{{ $tx->ticket_number }}</td>
                            <td class="px-4 py-3 font-medium text-textPrimary">{{ $tx->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-textSecondary">{{ $tx->item->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                    {{ $tx->status === 'lunas' ? 'border-success/20 bg-success/10 text-success' : '' }}
                                    {{ $tx->status === 'tersimpan' ? 'border-info/20 bg-info/10 text-info' : '' }}
                                    {{ $tx->status === 'diperpanjang' ? 'border-warning/20 bg-warning/10 text-warning' : '' }}
                                    {{ $tx->status === 'siap_lelang' ? 'border-secondary/20 bg-secondary/10 text-secondary' : '' }}
                                ">
                                    {{ ucfirst($tx->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold tabular-nums text-textPrimary">
                                Rp {{ number_format($tx->loan_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('petugas.pawn.sbg', $tx->id) }}" class="text-xs font-semibold text-primary hover:underline">Cetak SBG</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-textSecondary">Belum ada transaksi hari ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-content-card>
</x-app-layout>
