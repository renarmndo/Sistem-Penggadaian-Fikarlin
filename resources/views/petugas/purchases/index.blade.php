<x-app-layout title="Beli Barang Bekas" role="petugas">
    <x-page-header title="Daftar Pembelian Barang Bekas (FR-1.4)" description="Pencatatan arus kas keluar & pembaruan stok etalase otomatis.">
        <x-slot:actions>
            <a href="{{ route('petugas.purchases.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">
                + Beli Barang Bekas
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-content-card>
        <form method="GET" action="{{ route('petugas.purchases.index') }}" class="mb-6 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Transaksi / Penjual..." class="w-full max-w-md rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">Cari</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">No. Transaksi</th>
                        <th class="px-4 py-3">Penjual (KTP)</th>
                        <th class="px-4 py-3">Barang Bekas</th>
                        <th class="px-4 py-3 text-right">Harga Beli (Kas Keluar)</th>
                        <th class="px-4 py-3">Tanggal Beli</th>
                        <th class="px-4 py-3 text-right">Nota</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($purchases as $p)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-semibold text-primary">{{ $p->transaction_number }}</td>
                            <td class="px-4 py-3 font-medium text-textPrimary">{{ $p->customer->name ?? '-' }} (NIK: {{ $p->customer->identity_number ?? '-' }})</td>
                            <td class="px-4 py-3 text-textSecondary">{{ $p->item->name ?? '-' }} ({{ $p->item->category ?? '-' }})</td>
                            <td class="px-4 py-3 text-right font-bold text-danger">Rp {{ number_format($p->purchase_price, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-xs text-textSecondary">{{ $p->purchase_date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('petugas.purchases.nota', $p->id) }}" target="_blank" class="inline-flex items-center gap-1 rounded border border-primary/30 bg-primary/5 px-2.5 py-1 text-xs font-bold text-primary hover:bg-primary hover:text-white transition">
                                    📥 Unduh / Cetak Nota
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-textSecondary">Belum ada transaksi beli barang bekas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </x-content-card>
</x-app-layout>
