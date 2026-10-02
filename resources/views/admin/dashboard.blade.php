<x-app-layout title="Dashboard Admin Gudang" role="admin">
    <x-page-header title="Dashboard Admin Gudang" description="Monitoring fisik barang jaminan di Rak Gudang, Rak Lelang, dan Notifikasi Jatuh Tempo." />

    {{-- Summary Cards Grid --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-textSecondary uppercase">Rak Gudang (Fisik)</p>
                    <p class="mt-1 text-2xl font-bold text-primary">{{ number_format($totalGudang) }}</p>
                </div>
                <div class="rounded-xl bg-primary/10 p-3 text-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-textSecondary uppercase">Mendekati Tempo (H-3)</p>
                    <p class="mt-1 text-2xl font-bold text-warning">{{ number_format($nearDueDateCount) }}</p>
                </div>
                <div class="rounded-xl bg-warning/10 p-3 text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-textSecondary uppercase">Lewat Jatuh Tempo (Macet)</p>
                    <p class="mt-1 text-2xl font-bold text-danger">{{ number_format($overdueCount) }}</p>
                </div>
                <div class="rounded-xl bg-danger/10 p-3 text-danger">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" x2="12" y1="9" y2="13"/><line x1="12" x2="12.01" y1="17" y2="17"/></svg>
                </div>
            </div>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-textSecondary uppercase">Status Siap Lelang</p>
                    <p class="mt-1 text-2xl font-bold text-secondary">{{ number_format($siapLelangCount) }}</p>
                </div>
                <div class="rounded-xl bg-secondary/10 p-3 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m14 13-7.5 7.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0a2.12 2.12 0 0 1 0-3L11 10"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Needed Table --}}
    <x-content-card title="Notifikasi Tindakan Barang Macet / Siap Lelang (Rak Gudang -> Rak Lelang)">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Kode Barang</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Lokasi Fisik Saat Ini</th>
                        <th class="px-4 py-3">Status Sistem</th>
                        <th class="px-4 py-3 text-right">Tindakan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($itemsActionNeeded as $item)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-semibold text-primary">{{ $item->item_code }}</td>
                            <td class="px-4 py-3 font-medium text-textPrimary">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-xs font-semibold uppercase text-textSecondary">{{ $item->location }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full border px-2.5 py-0.5 text-xs font-semibold border-secondary/20 bg-secondary/10 text-secondary">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.auction.index') }}" class="rounded-lg bg-secondary px-3 py-1 text-xs font-semibold text-white hover:bg-secondary/90">
                                    Verifikasi Ke Rak Lelang
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-textSecondary">Tidak ada barang yang memerlukan verifikasi lelang saat ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-content-card>
</x-app-layout>
