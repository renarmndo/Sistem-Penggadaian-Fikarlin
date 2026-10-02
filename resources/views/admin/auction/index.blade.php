<x-app-layout title="Verifikasi Siap Lelang" role="admin">
    <x-page-header title="Verifikasi Barang Wanprestasi / Macet (FR-2.3)" description="Verifikasi pemindahan fisik barang dari Rak Gudang ke Rak Lelang." />

    <x-content-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Nasabah Owner</th>
                        <th class="px-4 py-3">Lokasi Fisik Saat Ini</th>
                        <th class="px-4 py-3">Status Item</th>
                        <th class="px-4 py-3 text-right">Verifikasi Pemindahan Fisik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-semibold text-primary">{{ $item->item_code }}</td>
                            <td class="px-4 py-3 font-medium text-textPrimary">{{ $item->name }} ({{ $item->category }})</td>
                            <td class="px-4 py-3 text-textSecondary">{{ $item->pawnTransaction->customer->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-xs font-bold uppercase text-textPrimary">{{ str_replace('_', ' ', $item->location) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold border-secondary/20 bg-secondary/10 text-secondary">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if ($item->location !== 'rak_lelang' || $item->status !== 'siap_lelang')
                                    <form method="POST" action="{{ route('admin.auction.mark-siap-lelang', $item->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="rounded-lg bg-secondary px-3 py-1.5 text-xs font-semibold text-white hover:bg-secondary/90 shadow-sm">
                                            Verifikasi & Pindah ke Rak Lelang
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-semibold text-success">✓ Sudah Di Rak Lelang</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-textSecondary">Tidak ada barang wanprestasi/macet yang perlu diverifikasi lelang.</td>
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
