<x-app-layout title="Daftar Transaksi Gadai" role="petugas">
    <x-page-header title="Daftar Transaksi Gadai" description="Kelola dan pantau seluruh Surat Bukti Gadai (SBG).">
        <x-slot:actions>
            <a href="{{ route('petugas.pawn.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">
                + Gadai Baru
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-content-card>
        <form method="GET" action="{{ route('petugas.pawn.index') }}" class="mb-6 flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Tiket / Barcode / Nasabah..." class="w-full max-w-xs rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
            <select name="status" class="rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                <option value="">Semua Status</option>
                <option value="tersimpan" {{ request('status') === 'tersimpan' ? 'selected' : '' }}>Tersimpan</option>
                <option value="diperpanjang" {{ request('status') === 'diperpanjang' ? 'selected' : '' }}>Diperpanjang</option>
                <option value="lunas" {{ request('status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                <option value="siap_lelang" {{ request('status') === 'siap_lelang' ? 'selected' : '' }}>Siap Lelang</option>
                <option value="terjual" {{ request('status') === 'terjual' ? 'selected' : '' }}>Terjual</option>
            </select>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">No. Tiket</th>
                        <th class="px-4 py-3">Barcode</th>
                        <th class="px-4 py-3">Nasabah</th>
                        <th class="px-4 py-3">Barang Jaminan</th>
                        <th class="px-4 py-3 text-right">Plafon Pinjaman</th>
                        <th class="px-4 py-3">Jatuh Tempo</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($transactions as $tx)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-semibold text-primary">{{ $tx->ticket_number }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-textSecondary">{{ $tx->barcode_code }}</td>
                            <td class="px-4 py-3 font-medium text-textPrimary">{{ $tx->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-textSecondary">{{ $tx->item->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-semibold tabular-nums text-textPrimary">Rp {{ number_format($tx->loan_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-textSecondary">{{ $tx->due_date->format('d/m/Y') }}</td>
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
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('petugas.pawn.sbg', $tx->id) }}" target="_blank" class="inline-flex items-center gap-1 rounded border border-primary/30 bg-primary/5 px-2.5 py-1 text-xs font-bold text-primary hover:bg-primary hover:text-white transition">
                                    📥 Unduh / Cetak SBG
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-textSecondary">Tidak ada transaksi gadai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transactions->links() }}
        </div>
    </x-content-card>
</x-app-layout>
