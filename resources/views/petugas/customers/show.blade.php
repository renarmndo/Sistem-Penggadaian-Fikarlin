<x-app-layout title="Detail Nasabah" role="petugas">
    <x-page-header title="Profil & Riwayat Nasabah" description="Informasi identitas dan riwayat transaksi {{ $customer->name }}">
        <x-slot:actions>
            <a href="{{ route('petugas.customers.edit', $customer->id) }}" class="rounded-lg border border-border px-4 py-2 text-sm font-semibold hover:bg-appBg">
                Edit Nasabah
            </a>
            <a href="{{ route('petugas.customers.index') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">
                Kembali
            </a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 md:grid-cols-3">
        {{-- Profile Card --}}
        <div class="md:col-span-1">
            <x-content-card title="Data Nasabah">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-textSecondary uppercase font-medium">No. KTP / Identitas</dt>
                        <dd class="font-mono font-semibold text-primary text-base">{{ $customer->identity_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-textSecondary uppercase font-medium">Nama Lengkap</dt>
                        <dd class="font-semibold text-textPrimary">{{ $customer->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-textSecondary uppercase font-medium">No. HP / WhatsApp</dt>
                        <dd class="text-textPrimary">{{ $customer->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-textSecondary uppercase font-medium">Alamat</dt>
                        <dd class="text-textPrimary">{{ $customer->address ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-textSecondary uppercase font-medium">Terdaftar Sejak</dt>
                        <dd class="text-textSecondary">{{ $customer->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>
            </x-content-card>
        </div>

        {{-- History Pawn Transactions --}}
        <div class="md:col-span-2 space-y-6">
            <x-content-card title="Riwayat Transaksi Gadai">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                            <tr>
                                <th class="px-3 py-2">No Tiket</th>
                                <th class="px-3 py-2">Barang</th>
                                <th class="px-3 py-2">Status</th>
                                <th class="px-3 py-2 text-right">Plafon</th>
                                <th class="px-3 py-2">Tempo</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($customer->pawnTransactions as $pawn)
                                <tr class="hover:bg-appBg/50">
                                    <td class="px-3 py-2 font-mono text-primary font-medium">{{ $pawn->ticket_number }}</td>
                                    <td class="px-3 py-2 font-medium">{{ $pawn->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-semibold
                                            {{ $pawn->status === 'lunas' ? 'bg-success/10 text-success' : 'bg-info/10 text-info' }}">
                                            {{ ucfirst($pawn->status) }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($pawn->loan_amount, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-xs text-textSecondary">{{ $pawn->due_date->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3 py-4 text-center text-textSecondary">Belum ada riwayat transaksi gadai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-content-card>

            <x-content-card title="Riwayat Penjualan Barang Bekas">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                            <tr>
                                <th class="px-3 py-2">No Transaksi</th>
                                <th class="px-3 py-2">Barang Bekas</th>
                                <th class="px-3 py-2 text-right">Harga Beli</th>
                                <th class="px-3 py-2">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @forelse ($customer->purchaseTransactions as $purch)
                                <tr class="hover:bg-appBg/50">
                                    <td class="px-3 py-2 font-mono text-primary font-medium">{{ $purch->transaction_number }}</td>
                                    <td class="px-3 py-2 font-medium">{{ $purch->item->name ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right font-semibold">Rp {{ number_format($purch->purchase_price, 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-xs text-textSecondary">{{ $purch->purchase_date->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-3 py-4 text-center text-textSecondary">Belum ada riwayat penjualan barang bekas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-content-card>
        </div>
    </div>
</x-app-layout>
