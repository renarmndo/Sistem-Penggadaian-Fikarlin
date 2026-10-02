<x-layouts.app title="Dashboard" role="petugas">
    <x-page-header
        title="Dashboard Petugas"
        description="Ringkasan transaksi hari ini."
    />

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-content-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-textSecondary">Gadai Baru</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-textPrimary">0</p>
                </div>
                <div class="rounded-xl bg-info/10 p-3 text-info">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                </div>
            </div>
        </x-content-card>

        <x-content-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-textSecondary">Pelunasan</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-textPrimary">0</p>
                </div>
                <div class="rounded-xl bg-success/10 p-3 text-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                </div>
            </div>
        </x-content-card>

        <x-content-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-textSecondary">Perpanjangan</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-textPrimary">0</p>
                </div>
                <div class="rounded-xl bg-warning/10 p-3 text-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                </div>
            </div>
        </x-content-card>

        <x-content-card>
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-medium text-textSecondary">Beli Barang Bekas</p>
                    <p class="mt-2 text-2xl font-bold tabular-nums text-textPrimary">0</p>
                </div>
                <div class="rounded-xl bg-secondary/10 p-3 text-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                </div>
            </div>
        </x-content-card>
    </div>

    <div class="mt-6">
        <x-content-card title="Riwayat Transaksi Hari Ini">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-border text-sm">
                    <thead class="bg-appBg text-left text-xs font-semibold uppercase tracking-wide text-textSecondary">
                        <tr>
                            <th class="px-4 py-3">Waktu</th>
                            <th class="px-4 py-3">Jenis</th>
                            <th class="px-4 py-3">Nasabah</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border bg-white">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center">
                                <p class="text-sm font-semibold text-textPrimary">Data belum tersedia</p>
                                <p class="mt-1 text-sm text-textSecondary">Belum ada transaksi hari ini.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-content-card>
    </div>
</x-layouts.app>
