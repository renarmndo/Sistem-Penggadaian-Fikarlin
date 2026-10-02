<x-app-layout title="Dashboard Executive Owner" role="owner">
    <x-page-header title="Dashboard Eksekutif Owner (FR-3.1)" description="Ringkasan eksekutif piutang aktif, barang di gudang, estimasi pendapatan bunga, dan arus kas." />

    {{-- Executive Summary Cards --}}
    <div class="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Total Piutang Aktif</p>
            <p class="mt-1 text-2xl font-bold text-primary">Rp {{ number_format($totalPiutangAktif, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-textSecondary">Outstanding plafon pinjaman aktif</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Pendapatan Bunga Bulan Ini</p>
            <p class="mt-1 text-2xl font-bold text-success">Rp {{ number_format($bungaBulanIni, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-textSecondary">+ Denda: Rp {{ number_format($dendaBulanIni, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Barang di Rak Gudang</p>
            <p class="mt-1 text-2xl font-bold text-warning">{{ number_format($totalBarangGudang) }} Unit</p>
            <p class="mt-1 text-xs text-textSecondary">Gadai tersimpan & diperpanjang</p>
        </div>

        <div class="rounded-card border border-border bg-surface p-5 shadow-card">
            <p class="text-xs font-semibold text-textSecondary uppercase">Kas Keluar Beli Bekas</p>
            <p class="mt-1 text-2xl font-bold text-danger">Rp {{ number_format($kasKeluarBeliBekas, 0, ',', '.') }}</p>
            <p class="mt-1 text-xs text-textSecondary">Pengeluaran stok etalase bulan ini</p>
        </div>
    </div>

    {{-- Quick Access Section --}}
    <div class="grid gap-6 md:grid-cols-2">
        <x-content-card title="Menu Manajemen Utama Owner">
            <div class="space-y-3">
                <a href="{{ route('owner.reports.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border bg-appBg hover:bg-white transition">
                    <span class="font-semibold text-sm text-textPrimary">📊 Laporan Transaksi & Laba/Rugi Real-Time (FR-3.4)</span>
                    <span class="text-xs text-primary font-bold">Buka Laporan →</span>
                </a>
                <a href="{{ route('owner.settings.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border bg-appBg hover:bg-white transition">
                    <span class="font-semibold text-sm text-textPrimary">⚙️ Pengaturan Parameter Bunga, Denda, & Tenor (FR-3.2)</span>
                    <span class="text-xs text-primary font-bold">Atur Parameter →</span>
                </a>
                <a href="{{ route('owner.users.index') }}" class="flex items-center justify-between p-3 rounded-lg border border-border bg-appBg hover:bg-white transition">
                    <span class="font-semibold text-sm text-textPrimary">👥 Kelola Akun Pengguna Admin & Petugas (FR-3.3)</span>
                    <span class="text-xs text-primary font-bold">Kelola Akun →</span>
                </a>
            </div>
        </x-content-card>

        <x-content-card title="5 Transaksi Gadai Terakhir">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-border bg-appBg text-textSecondary uppercase font-semibold">
                        <tr>
                            <th class="py-2">No. Tiket</th>
                            <th class="py-2">Nasabah</th>
                            <th class="py-2 text-right">Plafon</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @foreach ($recentPawns as $p)
                            <tr>
                                <td class="py-2 font-mono font-bold text-primary">{{ $p->ticket_number }}</td>
                                <td class="py-2 font-medium">{{ $p->customer->name ?? '-' }}</td>
                                <td class="py-2 text-right font-semibold">Rp {{ number_format($p->loan_amount, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-content-card>
    </div>
</x-app-layout>
