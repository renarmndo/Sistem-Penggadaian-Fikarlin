<x-app-layout title="Lokasi & Stok Gudang" role="admin">
    <x-page-header title="Manajemen Lokasi & Stok Gudang (FR-2.1)" description="Monitoring inventaris fisik barang di Rak Gudang, Rak Lelang, dan Etalase Toko secara terpusat." />

    {{-- Ringkasan Statistik KPI Gudang --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 w-full">
        <div class="rounded-card border border-primary/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-textSecondary">Stok Fisik Aktif</span>
                <span class="rounded-full bg-primary/10 p-2 text-primary">📦</span>
            </div>
            <p class="mt-2 text-2xl font-black text-primary">{{ $stats['total_aktif'] }} <span class="text-xs font-normal text-textSecondary">Unit</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Total unit yang tersimpan di toko</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-textSecondary">Rak Gudang (Gadai)</span>
                <span class="rounded-full bg-appBg p-2 text-textPrimary">🗄️</span>
            </div>
            <p class="mt-2 text-2xl font-black text-textPrimary">{{ $stats['rak_gudang'] }} <span class="text-xs font-normal text-textSecondary">Unit</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Jaminan aktif nasabah</p>
        </div>

        <div class="rounded-card border border-secondary/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-secondary">Rak Lelang (Macet)</span>
                <span class="rounded-full bg-secondary/10 p-2 text-secondary">🏷️</span>
            </div>
            <p class="mt-2 text-2xl font-black text-secondary">{{ $stats['rak_lelang'] }} <span class="text-xs font-normal text-secondary/80">Unit</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Siap dijual lelang kasir</p>
        </div>

        <div class="rounded-card border border-info/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase text-info">Etalase Toko</span>
                <span class="rounded-full bg-info/10 p-2 text-info">📱</span>
            </div>
            <p class="mt-2 text-2xl font-black text-info">{{ $stats['etalase'] }} <span class="text-xs font-normal text-info/80">Unit</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Barang elektronik bekas dipajang</p>
        </div>
    </div>

    {{-- Panel Filter Profesional --}}
    <x-content-card title="Filter & Pencarian Data Inventaris" class="w-full mb-6">
        <form method="GET" action="{{ route('admin.warehouse.index') }}" class="space-y-4 w-full">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 w-full">
                {{-- 1. Kata Kunci --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Kode barang / nama / NIK..." class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                </div>

                {{-- 2. Status Inventaris --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Status Inventaris</label>
                    <select name="status_scope" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                        <option value="aktif" {{ request('status_scope', 'aktif') === 'aktif' ? 'selected' : '' }}>📦 Stok Fisik Aktif (Default)</option>
                        <option value="tersimpan" {{ request('status_scope') === 'tersimpan' ? 'selected' : '' }}>Tersimpan (Gadai Baru)</option>
                        <option value="diperpanjang" {{ request('status_scope') === 'diperpanjang' ? 'selected' : '' }}>Diperpanjang (Gadai Aktif)</option>
                        <option value="siap_lelang" {{ request('status_scope') === 'siap_lelang' ? 'selected' : '' }}>Siap Lelang (Macet)</option>
                        <option value="stok_etalase" {{ request('status_scope') === 'stok_etalase' ? 'selected' : '' }}>Stok Etalase Toko</option>
                        <option value="keluar" {{ request('status_scope') === 'keluar' ? 'selected' : '' }}>✅ Riwayat Barang Keluar (Lunas & Terjual)</option>
                        <option value="semua" {{ request('status_scope') === 'semua' ? 'selected' : '' }}>📋 Semua Status (Termasuk Keluar)</option>
                    </select>
                </div>

                {{-- 3. Lokasi Fisik --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Lokasi Fisik Rak</label>
                    <select name="location" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                        <option value="">Semua Lokasi Fisik</option>
                        <option value="rak_gudang" {{ request('location') === 'rak_gudang' ? 'selected' : '' }}>Rak Gudang</option>
                        <option value="rak_lelang" {{ request('location') === 'rak_lelang' ? 'selected' : '' }}>Rak Lelang</option>
                        <option value="etalase" {{ request('location') === 'etalase' ? 'selected' : '' }}>Etalase Toko</option>
                    </select>
                </div>

                {{-- 4. Asal Barang --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Asal Transaksi</label>
                    <select name="source_type" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                        <option value="">Semua Asal Transaksi</option>
                        <option value="gadai" {{ request('source_type') === 'gadai' ? 'selected' : '' }}>Jasa Gadai</option>
                        <option value="beli_barang_bekas" {{ request('source_type') === 'beli_barang_bekas' ? 'selected' : '' }}>Beli Barang Bekas</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                <a href="{{ route('admin.warehouse.index') }}" class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-textSecondary hover:bg-appBg transition">
                    Reset Filter
                </a>
                <button type="submit" class="rounded-lg bg-primary px-6 py-2 text-xs font-bold text-white shadow-sm hover:bg-primaryDark transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </x-content-card>

    {{-- Tabel Data Inventaris --}}
    <x-content-card class="w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama & Kategori Barang</th>
                        <th class="px-4 py-3">Asal Barang</th>
                        <th class="px-4 py-3">Lokasi Fisik Rak</th>
                        <th class="px-4 py-3">Status Inventaris</th>
                        <th class="px-4 py-3 text-right">Aksi Pindah Rak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($items as $item)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-bold text-primary">{{ $item->item_code }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-textPrimary">{{ $item->name }}</p>
                                <p class="text-xs text-textSecondary">{{ $item->category }} {{ $item->brand ? '— ' . $item->brand : '' }}</p>
                            </td>
                            <td class="px-4 py-3 text-xs text-textSecondary uppercase font-semibold">
                                {{ str_replace('_', ' ', $item->source_type) }}
                            </td>
                            <td class="px-4 py-3">
                                @if (in_array($item->status, ['lunas', 'terjual']))
                                    <span class="inline-flex rounded-full bg-success/10 border border-success/20 px-2.5 py-0.5 text-xs font-bold text-success">
                                        KELUAR (Diserahkan)
                                    </span>
                                @else
                                    <span class="inline-flex rounded-md bg-appBg border border-border px-2.5 py-1 text-xs font-bold text-textPrimary uppercase">
                                        {{ str_replace('_', ' ', $item->location) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                    {{ $item->status === 'lunas' || $item->status === 'terjual' ? 'border-success/20 bg-success/10 text-success' : '' }}
                                    {{ $item->status === 'tersimpan' ? 'border-info/20 bg-info/10 text-info' : '' }}
                                    {{ $item->status === 'diperpanjang' ? 'border-warning/20 bg-warning/10 text-warning' : '' }}
                                    {{ $item->status === 'siap_lelang' ? 'border-secondary/20 bg-secondary/10 text-secondary' : '' }}
                                    {{ $item->status === 'stok_etalase' ? 'border-info/20 bg-info/10 text-info' : '' }}
                                ">
                                    {{ str_replace('_', ' ', strtoupper($item->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if (!in_array($item->status, ['lunas', 'terjual']))
                                    <form method="POST" action="{{ route('admin.warehouse.update-location', $item->id) }}" class="inline-flex items-center gap-1.5">
                                        @csrf
                                        @method('PATCH')
                                        <select name="location" class="rounded border border-border bg-white px-2 py-1 text-xs outline-none focus:border-primary">
                                            <option value="rak_gudang" {{ $item->location === 'rak_gudang' ? 'selected' : '' }}>Rak Gudang</option>
                                            <option value="rak_lelang" {{ $item->location === 'rak_lelang' ? 'selected' : '' }}>Rak Lelang</option>
                                            <option value="etalase" {{ $item->location === 'etalase' ? 'selected' : '' }}>Etalase</option>
                                        </select>
                                        <button type="submit" class="rounded bg-primary px-3 py-1 text-xs font-semibold text-white hover:bg-primaryDark transition">
                                            Pindah
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs font-medium text-textSecondary italic">Barang Sudah Keluar</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-textSecondary">
                                <p class="text-sm font-medium">Tidak ada data inventaris yang cocok dengan kriteria filter.</p>
                            </td>
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
