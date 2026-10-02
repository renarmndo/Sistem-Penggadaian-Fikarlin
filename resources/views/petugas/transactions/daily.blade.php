<x-app-layout title="Riwayat Kasir Harian" role="petugas">
    <x-page-header title="Buku Kas & Riwayat Transaksi Kasir" description="Rekapitulasi seluruh arus kas masuk, kas keluar, dan pencatatan transaksi kasir per tanggal operasional." />

    {{-- Ringkasan KPI Kasir Hari Ini --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 w-full">
        {{-- Card 1: Kas Masuk --}}
        <div class="rounded-card border border-success/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-textSecondary">Total Kas Masuk</span>
                <span class="rounded-full bg-success/10 p-2 text-success font-bold">⬇️</span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-black text-success">Rp {{ number_format($stats['cash_in'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-textSecondary mt-1">Pelunasan, perpanjangan, lelang</p>
        </div>

        {{-- Card 2: Kas Keluar --}}
        <div class="rounded-card border border-danger/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-textSecondary">Total Kas Keluar</span>
                <span class="rounded-full bg-danger/10 p-2 text-danger font-bold">⬆️</span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-black text-danger">Rp {{ number_format($stats['cash_out'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-textSecondary mt-1">Plafon gadai baru & beli bekas</p>
        </div>

        {{-- Card 3: Saldo Kas Bersih --}}
        <div class="rounded-card border border-primary/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-textSecondary">Arus Kas Bersih (Net)</span>
                <span class="rounded-full bg-primary/10 p-2 text-primary font-bold">⚖️</span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-black {{ $stats['net_flow'] >= 0 ? 'text-primary' : 'text-danger' }}">
                Rp {{ number_format($stats['net_flow'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] text-textSecondary mt-1">Selisih Kas Masuk - Kas Keluar</p>
        </div>

        {{-- Card 4: Total Lembar Transaksi --}}
        <div class="rounded-card border border-border bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-textSecondary">Jumlah Transaksi</span>
                <span class="rounded-full bg-appBg p-2 text-textPrimary font-bold">🧾</span>
            </div>
            <p class="mt-2 text-xl sm:text-2xl font-black text-textPrimary">{{ $stats['count'] }} <span class="text-xs font-normal text-textSecondary">Transaksi</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Tanggal {{ $targetDate->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Filter Bar & Action Header --}}
    <x-content-card class="w-full mb-6">
        <form method="GET" action="{{ route('petugas.transactions.daily') }}" class="flex flex-wrap items-end justify-between gap-4 w-full">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                {{-- Date Picker --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Pilih Tanggal Transaksi</label>
                    <input type="date" name="date" value="{{ $targetDate->format('Y-m-d') }}" class="rounded-lg border border-border bg-white px-3 py-2 text-sm font-semibold text-textPrimary outline-none focus:border-primary" />
                </div>

                {{-- Type Selector --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Jenis Transaksi</label>
                    <select name="type" class="rounded-lg border border-border bg-white px-3 py-2 text-sm font-semibold text-textPrimary outline-none focus:border-primary">
                        <option value="all" {{ $typeFilter === 'all' ? 'selected' : '' }}>Semua Jenis Transaksi</option>
                        <option value="gadai_baru" {{ $typeFilter === 'gadai_baru' ? 'selected' : '' }}>Gadai Baru (Kas Keluar)</option>
                        <option value="pelunasan" {{ $typeFilter === 'pelunasan' ? 'selected' : '' }}>Pelunasan Tebus (Kas Masuk)</option>
                        <option value="perpanjangan" {{ $typeFilter === 'perpanjangan' ? 'selected' : '' }}>Perpanjangan Tenor (Kas Masuk)</option>
                        <option value="beli_bekas" {{ $typeFilter === 'beli_bekas' ? 'selected' : '' }}>Beli Barang Bekas (Kas Keluar)</option>
                        <option value="penjualan_lelang" {{ $typeFilter === 'penjualan_lelang' ? 'selected' : '' }}>Penjualan Lelang/Etalase (Kas Masuk)</option>
                    </select>
                </div>

                <div class="pt-5">
                    <button type="submit" class="rounded-lg bg-primary px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-primaryDark transition">
                        Tampilkan
                    </button>
                </div>
            </div>

            {{-- Print Daily Sheet Button --}}
            <div class="w-full sm:w-auto flex justify-end">
                <a href="{{ route('petugas.transactions.print-daily', ['date' => $targetDate->format('Y-m-d'), 'type' => $typeFilter]) }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-secondary px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-secondary/90 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                    <span>Cetak Rekap Kasir (PDF)</span>
                </a>
            </div>
        </form>
    </x-content-card>

    {{-- Tabel Data Riwayat Transaksi Hari Ini --}}
    <x-content-card class="w-full">
        <div class="flex items-center justify-between mb-4 border-b border-border pb-3">
            <h3 class="text-sm font-bold text-textPrimary">
                Daftar Kronologis Transaksi Kasir — <span class="text-primary">{{ $targetDate->format('d F Y') }}</span>
            </h3>
            <span class="text-xs text-textSecondary font-semibold">Total: {{ $transactions->count() }} Data</span>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">No. Dokumen / Tiket</th>
                        <th class="px-4 py-3">Jenis Transaksi</th>
                        <th class="px-4 py-3">Nasabah / Pihak Terkait</th>
                        <th class="px-4 py-3">Rincian Barang / Keterangan</th>
                        <th class="px-4 py-3 text-right">Arus Kas</th>
                        <th class="px-4 py-3 text-right">Nominal (Rp)</th>
                        <th class="px-4 py-3 text-right">Cetak Ulang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 text-xs font-mono font-medium text-textSecondary">
                                {{ $tx['time'] ? \Carbon\Carbon::parse($tx['time'])->format('H:i:s') : '-' }}
                            </td>
                            <td class="px-4 py-3 font-mono font-bold text-primary text-xs">
                                {{ $tx['doc_number'] }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-bold
                                    {{ $tx['type_key'] === 'gadai_baru' ? 'bg-primary/10 text-primary border border-primary/20' : '' }}
                                    {{ $tx['type_key'] === 'pelunasan' ? 'bg-success/10 text-success border border-success/20' : '' }}
                                    {{ $tx['type_key'] === 'perpanjangan' ? 'bg-warning/10 text-warning border border-warning/20' : '' }}
                                    {{ $tx['type_key'] === 'beli_bekas' ? 'bg-danger/10 text-danger border border-danger/20' : '' }}
                                    {{ $tx['type_key'] === 'penjualan_lelang' ? 'bg-secondary/10 text-secondary border border-secondary/20' : '' }}
                                ">
                                    {{ $tx['type_label'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium text-textPrimary text-xs">
                                {{ $tx['party_name'] }}
                                @if ($tx['party_phone'] !== '-')
                                    <span class="block text-[11px] text-textSecondary font-normal">{{ $tx['party_phone'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-textSecondary">
                                <p class="font-medium text-textPrimary">{{ $tx['item_desc'] }}</p>
                                <p class="text-[11px]">{{ $tx['notes'] }}</p>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($tx['cash_flow'] === 'in')
                                    <span class="inline-flex items-center gap-1 rounded bg-success/10 text-success px-2 py-0.5 text-xs font-bold">
                                        + MASUK
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded bg-danger/10 text-danger px-2 py-0.5 text-xs font-bold">
                                        - KELUAR
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-sm tabular-nums {{ $tx['cash_flow'] === 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $tx['cash_flow'] === 'in' ? '+' : '-' }} Rp {{ number_format($tx['amount'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ $tx['print_url'] }}" target="_blank" class="inline-flex items-center gap-1 rounded border border-primary/30 bg-primary/5 px-2.5 py-1 text-xs font-bold text-primary hover:bg-primary hover:text-white transition">
                                    📥 {{ $tx['print_label'] }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-textSecondary">
                                <p class="text-sm font-semibold">Tidak ada transaksi yang tercatat pada tanggal {{ $targetDate->format('d/m/Y') }}.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-content-card>
</x-app-layout>
