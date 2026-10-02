<x-app-layout title="Monitoring Jatuh Tempo" role="admin">
    <x-page-header title="Monitoring Kontrak Jatuh Tempo (FR-2.2)" description="Sistem monitoring jatuh tempo berbasis tanggal server untuk mendeteksi status aman, mendekati jatuh tempo, maupun wanprestasi (macet)." />

    {{-- Ringkasan Metrik KPI Jatuh Tempo --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6 w-full">
        {{-- Card 1: Total Kontrak Aktif --}}
        <div class="rounded-card border border-primary/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-textSecondary">Kontrak Aktif</span>
                <span class="rounded-full bg-primary/10 p-2 text-primary">📋</span>
            </div>
            <p class="mt-2 text-2xl font-black text-primary">{{ $stats['total_active'] }} <span class="text-xs font-normal text-textSecondary">Kontrak</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Total pinjaman: Rp {{ number_format($stats['total_loan'], 0, ',', '.') }}</p>
        </div>

        {{-- Card 2: Jatuh Tempo Hari Ini --}}
        <div class="rounded-card border border-danger/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-danger">Jatuh Tempo Hari Ini</span>
                <span class="rounded-full bg-danger/10 p-2 text-danger">🔔</span>
            </div>
            <p class="mt-2 text-2xl font-black text-danger">{{ $stats['due_today'] }} <span class="text-xs font-normal text-danger/80">Kontrak</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Tanggal {{ $today->format('d/m/Y') }}</p>
        </div>

        {{-- Card 3: Mendekati Tempo (H-3) --}}
        <div class="rounded-card border border-warning/20 bg-surface p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-warning">Mendekati Tempo (H-3)</span>
                <span class="rounded-full bg-warning/10 p-2 text-warning">⏰</span>
            </div>
            <p class="mt-2 text-2xl font-black text-warning">{{ $stats['near_3'] }} <span class="text-xs font-normal text-warning/80">Kontrak</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Perlu konfirmasi ke nasabah</p>
        </div>

        {{-- Card 4: Lewat Jatuh Tempo (Macet) --}}
        <div class="rounded-card border border-danger/30 bg-danger/5 p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase text-danger">Lewat Tempo (Macet)</span>
                <span class="rounded-full bg-danger/20 p-2 text-danger">🚨</span>
            </div>
            <p class="mt-2 text-2xl font-black text-danger">{{ $stats['overdue'] }} <span class="text-xs font-normal text-danger/80">Kontrak</span></p>
            <p class="text-[11px] text-textSecondary mt-1">Dikenakan denda harian</p>
        </div>
    </div>

    {{-- Panel Filter Profesional --}}
    <x-content-card title="Filter & Pencarian Kontrak Jatuh Tempo" class="w-full mb-6">
        <form method="GET" action="{{ route('admin.duedate.index') }}" class="space-y-4 w-full">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 w-full">
                {{-- 1. Kata Kunci --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Cari Kata Kunci</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="No. Tiket / NIK / Nama / No. HP..." class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                </div>

                {{-- 2. Kategori Jatuh Tempo --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Kategori Jatuh Tempo</label>
                    <select name="due_scope" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                        <option value="all" {{ request('due_scope', 'all') === 'all' ? 'selected' : '' }}>Semua Kontrak Aktif</option>
                        <option value="today" {{ request('due_scope') === 'today' ? 'selected' : '' }}>🔔 Jatuh Tempo Hari Ini (H-0)</option>
                        <option value="near_3" {{ request('due_scope') === 'near_3' ? 'selected' : '' }}>⏰ Mendekati Tempo (H-3)</option>
                        <option value="near_7" {{ request('due_scope') === 'near_7' ? 'selected' : '' }}>📅 Jatuh Tempo Minggu Ini (H-7)</option>
                        <option value="overdue" {{ request('due_scope') === 'overdue' ? 'selected' : '' }}>🚨 Lewat Jatuh Tempo (Macet)</option>
                        <option value="safe" {{ request('due_scope') === 'safe' ? 'selected' : '' }}>✅ Status Aman (> 7 Hari)</option>
                    </select>
                </div>

                {{-- 3. Status Transaksi --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Status Kontrak</label>
                    <select name="status" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">
                        <option value="all" {{ request('status', 'all') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="tersimpan" {{ request('status') === 'tersimpan' ? 'selected' : '' }}>Tersimpan (Gadai Baru)</option>
                        <option value="diperpanjang" {{ request('status') === 'diperpanjang' ? 'selected' : '' }}>Diperpanjang (Gadai Aktif)</option>
                    </select>
                </div>

                {{-- 4. Rentang Tanggal Tempo --}}
                <div>
                    <label class="block text-xs font-bold text-textPrimary uppercase mb-1">Batas Tanggal Tempo</label>
                    <input type="date" name="due_date_to" value="{{ request('due_date_to') }}" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-border">
                <a href="{{ route('admin.duedate.index') }}" class="rounded-lg border border-border px-4 py-2 text-xs font-semibold text-textSecondary hover:bg-appBg transition">
                    Reset Filter
                </a>
                <button type="submit" class="rounded-lg bg-primary px-6 py-2 text-xs font-bold text-white shadow-sm hover:bg-primaryDark transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </x-content-card>

    {{-- Tabel Data Kontrak --}}
    <x-content-card class="w-full">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">No. Tiket SBG</th>
                        <th class="px-4 py-3">Nasabah & Kontak</th>
                        <th class="px-4 py-3">Barang Jaminan</th>
                        <th class="px-4 py-3 text-right">Plafon Pinjaman</th>
                        <th class="px-4 py-3">Tanggal Tempo</th>
                        <th class="px-4 py-3">Status / Selisih Waktu</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($transactions as $tx)
                        @php
                            $dueDate = \Carbon\Carbon::parse($tx->due_date);
                            $diffDays = (int) $today->diffInDays($dueDate, false);
                        @endphp
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-bold text-primary text-xs">
                                {{ $tx->ticket_number }}
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-textPrimary text-xs">{{ $tx->customer->name ?? '-' }}</p>
                                <p class="text-[11px] text-textSecondary">{{ $tx->customer->phone ?? '-' }}</p>
                            </td>
                            <td class="px-4 py-3">
                                <p class="font-medium text-textPrimary text-xs">{{ $tx->item->name ?? '-' }}</p>
                                <p class="text-[11px] text-textSecondary">{{ $tx->item->category ?? '-' }} (Lokasi: <span class="uppercase font-semibold text-textPrimary">{{ str_replace('_', ' ', $tx->item->location ?? 'rak_gudang') }}</span>)</p>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-sm text-textPrimary">
                                Rp {{ number_format($tx->loan_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 font-bold {{ $diffDays < 0 ? 'text-danger' : ($diffDays <= 3 ? 'text-warning' : 'text-textPrimary') }}">
                                {{ $dueDate->format('d F Y') }}
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold">
                                @if ($diffDays < 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-danger/10 text-danger border border-danger/20 px-2.5 py-0.5 text-xs font-bold">
                                        🚨 Macet {{ abs($diffDays) }} Hari
                                    </span>
                                @elseif ($diffDays === 0)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-danger/15 text-danger border border-danger/30 px-2.5 py-0.5 text-xs font-black animate-pulse">
                                        🔔 Hari Ini (H-0)
                                    </span>
                                @elseif ($diffDays <= 3)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-warning/10 text-warning border border-warning/20 px-2.5 py-0.5 text-xs font-bold">
                                        ⏰ Sisa {{ $diffDays }} Hari (H-{{ $diffDays }})
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-success/10 text-success border border-success/20 px-2.5 py-0.5 text-xs font-medium">
                                        ✅ Sisa {{ $diffDays }} Hari
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('petugas.pawn.sbg', $tx->id) }}" target="_blank" class="inline-flex items-center gap-1 rounded border border-border px-2.5 py-1 text-xs font-semibold text-textSecondary hover:bg-appBg hover:text-textPrimary transition">
                                    Lihat SBG
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-12 text-center text-textSecondary">
                                <p class="text-sm font-medium">Tidak ada data kontrak yang cocok dengan kriteria filter.</p>
                            </td>
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
