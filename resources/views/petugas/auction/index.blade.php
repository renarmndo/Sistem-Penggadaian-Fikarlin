<x-app-layout title="Penjualan Lelang / Etalase" role="petugas">
    <x-page-header title="Modul Penjualan Barang Lelang & Etalase (FR-2.4)" description="Proses penjualan barang yang berstatus SIAP LELANG atau STOK ETALASE di toko front-office." />

    <x-content-card>
        <form method="GET" action="{{ route('petugas.auction.index') }}" class="mb-6 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Barang / Nama / Kategori..." class="w-full max-w-md rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">Cari Barang</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Asal Barang</th>
                        <th class="px-4 py-3">Lokasi Fisik</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Harga Taksiran/Jual</th>
                        <th class="px-4 py-3 text-right">Aksi Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-appBg/50" x-data="{ openModal: false }">
                            <td class="px-4 py-3 font-mono font-semibold text-primary">{{ $item->item_code }}</td>
                            <td class="px-4 py-3 font-semibold text-textPrimary">{{ $item->name }} ({{ $item->category }})</td>
                            <td class="px-4 py-3 text-xs text-textSecondary uppercase font-medium">{{ str_replace('_', ' ', $item->source_type) }}</td>
                            <td class="px-4 py-3 text-xs font-semibold text-textPrimary">{{ strtoupper($item->location) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                    {{ $item->status === 'siap_lelang' ? 'border-secondary/20 bg-secondary/10 text-secondary' : 'border-info/20 bg-info/10 text-info' }}
                                ">
                                    {{ str_replace('_', ' ', strtoupper($item->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold tabular-nums text-textPrimary">
                                Rp {{ number_format($item->selling_price ?? $item->estimated_value, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button @click="openModal = true" class="rounded-lg bg-success px-3 py-1.5 text-xs font-semibold text-white hover:bg-success/90">
                                    Jual Sekarang
                                </button>

                                {{-- Modal Input Penjualan --}}
                                <div x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 text-left" style="display: none;">
                                    <div @click.away="openModal = false" class="w-full max-w-md rounded-card bg-white p-6 shadow-xl">
                                        <h3 class="text-base font-bold text-textPrimary mb-1">Penjualan {{ $item->name }}</h3>
                                        <p class="text-xs text-textSecondary mb-4">Kode Barang: <span class="font-mono font-bold text-primary">{{ $item->item_code }}</span></p>

                                        <form method="POST" action="{{ route('petugas.auction.sell', $item->id) }}" class="space-y-4">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-semibold text-textSecondary uppercase mb-1">Nama Pembeli <span class="text-danger">*</span></label>
                                                <input type="text" name="buyer_name" required placeholder="Nama lengkap pembeli" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary" />
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-textSecondary uppercase mb-1">Harga Jual Disepakati (Rp) <span class="text-danger">*</span></label>
                                                <input type="number" name="selling_price" value="{{ $item->selling_price ?? $item->estimated_value }}" required min="1000" step="1000" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm font-bold text-success outline-none focus:border-primary" />
                                            </div>

                                            <div>
                                                <label class="block text-xs font-semibold text-textSecondary uppercase mb-1">Catatan Penjualan</label>
                                                <textarea name="notes" rows="2" placeholder="Catatan transaksi lelang" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary"></textarea>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="openModal = false" class="rounded-lg border border-border px-4 py-2 text-xs font-semibold hover:bg-appBg">Batal</button>
                                                <button type="submit" class="rounded-lg bg-success px-4 py-2 text-xs font-semibold text-white hover:bg-success/90">Konfirmasi TERJUAL</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-textSecondary">Tidak ada barang yang siap lelang atau di etalase.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </x-content-card>
</x-app-layout>
