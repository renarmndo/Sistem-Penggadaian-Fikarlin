<x-app-layout title="Proses Transaksi SBG" role="petugas">
    <x-page-header title="Proses Pelunasan / Perpanjangan (FR-1.3)" description="Pilih jenis aksi pembayaran untuk SBG {{ $transaction->ticket_number }}" />

    <div class="w-full space-y-6" x-data="{
        type: 'pelunasan',
        extensionDays: {{ $transaction->tenor_days ?? 30 }},
        get newDueDatePreview() {
            let base = new Date('{{ $transaction->due_date->format('Y-m-d') }}');
            let today = new Date();
            if (today > base) {
                base = today;
            }
            base.setDate(base.getDate() + Number(this.extensionDays || 0));
            return base.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        }
    }">
        {{-- Details Card Full Width --}}
        <x-content-card title="Detail Dokumen SBG & Nasabah" class="w-full">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-sm">
                <div class="rounded-lg bg-appBg p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Nomor Tiket SBG</span>
                    <p class="font-mono font-bold text-primary text-base">{{ $transaction->ticket_number }}</p>
                </div>
                <div class="rounded-lg bg-appBg p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Nasabah</span>
                    <p class="font-semibold text-textPrimary">{{ $transaction->customer->name ?? '-' }} (NIK: {{ $transaction->customer->identity_number ?? '-' }})</p>
                </div>
                <div class="rounded-lg bg-appBg p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Barang Jaminan</span>
                    <p class="font-medium text-textPrimary">{{ $transaction->item->name ?? '-' }} ({{ $transaction->item->category ?? '-' }})</p>
                </div>
                <div class="rounded-lg bg-appBg p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Tanggal Jatuh Tempo</span>
                    <p class="font-bold text-danger">{{ $transaction->due_date->format('d F Y') }}</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-3 mt-4 text-sm">
                <div class="rounded-lg bg-surface p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Plafon Pinjaman Pokok:</span>
                    <p class="font-bold text-textPrimary text-base">Rp {{ number_format($transaction->loan_amount, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-surface p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Bunga Gadai ({{ $transaction->interest_rate }}%):</span>
                    <p class="font-bold text-textPrimary text-base">Rp {{ number_format($transaction->interest_amount, 0, ',', '.') }}</p>
                </div>
                <div class="rounded-lg bg-surface p-3 border border-border">
                    <span class="text-xs text-textSecondary uppercase font-medium">Denda Keterlambatan:</span>
                    <p class="font-bold text-danger text-base">Rp {{ number_format($penaltyInfo['penalty_amount'], 0, ',', '.') }}</p>
                </div>
            </div>

            @if ($penaltyInfo['days_late'] > 0)
                <div class="mt-4 rounded-lg bg-danger/10 border border-danger/20 p-3 text-xs text-danger flex justify-between items-center">
                    <div>
                        <span class="font-bold">Keterlambatan Server Auto-Calculate:</span> {{ $penaltyInfo['days_late'] }} hari melewati jatuh tempo.
                    </div>
                    <div class="font-bold text-sm">
                        Total Denda (0.5%/hari): Rp {{ number_format($penaltyInfo['penalty_amount'], 0, ',', '.') }}
                    </div>
                </div>
            @endif
        </x-content-card>

        {{-- Form Action Box Full Width --}}
        <x-content-card title="Pilih Opsi Pembayaran Kasir" class="w-full">
            <form method="POST" action="{{ route('petugas.payments.store') }}" class="space-y-6 w-full">
                @csrf
                <input type="hidden" name="pawn_transaction_id" value="{{ $transaction->id }}" />

                <div class="grid gap-6 md:grid-cols-2 w-full">
                    {{-- Option 1: Pelunasan --}}
                    <label class="relative flex flex-col justify-between cursor-pointer rounded-card border-2 p-5 transition" :class="type === 'pelunasan' ? 'border-success bg-success/5 shadow-md' : 'border-border bg-white hover:border-success/40'">
                        <input type="radio" name="payment_type" value="pelunasan" x-model="type" class="sr-only" />
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-success text-base">1. Tebus Lunas (LUNAS)</span>
                                <span class="rounded-full bg-success/20 px-2 py-0.5 text-xs font-bold text-success" x-show="type === 'pelunasan'">Dipilih</span>
                            </div>
                            <p class="mt-2 text-xs text-textSecondary leading-relaxed">Membayar Pokok Pinjaman + Bunga + Denda (jika ada). Status barang menjadi LUNAS dan diserahkan kembali kepada nasabah.</p>
                        </div>
                        <div class="mt-4 pt-4 border-t border-border flex justify-between items-center">
                            <span class="text-xs text-textSecondary font-semibold uppercase">Total Pembayaran:</span>
                            <span class="font-bold text-lg text-success">Rp {{ number_format($transaction->loan_amount + $transaction->interest_amount + $penaltyInfo['penalty_amount'], 0, ',', '.') }}</span>
                        </div>
                    </label>

                    {{-- Option 2: Perpanjangan with Customizable Tenor --}}
                    <label class="relative flex flex-col justify-between cursor-pointer rounded-card border-2 p-5 transition" :class="type === 'perpanjangan' ? 'border-warning bg-warning/5 shadow-md' : 'border-border bg-white hover:border-warning/40'">
                        <input type="radio" name="payment_type" value="perpanjangan" x-model="type" class="sr-only" />
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-warning text-base">2. Perpanjangan Tenor</span>
                                <span class="rounded-full bg-warning/20 px-2 py-0.5 text-xs font-bold text-warning" x-show="type === 'perpanjangan'">Dipilih</span>
                            </div>
                            <p class="mt-2 text-xs text-textSecondary leading-relaxed">Hanya membayar Bunga + Denda. Nasabah dapat menentukan durasi perpanjangan tenor.</p>
                            
                            {{-- Customizable Extension Tenor Inputs --}}
                            <div class="mt-3 p-3 bg-white rounded-lg border border-border" x-show="type === 'perpanjangan'">
                                <span class="block text-xs font-bold text-textPrimary mb-1.5">Pilih Durasi Perpanjangan:</span>
                                <div class="flex gap-1.5 mb-2">
                                    <button type="button" @click="extensionDays = 7" class="flex-1 rounded border px-2 py-1 text-xs font-semibold" :class="extensionDays == 7 ? 'bg-warning text-white border-warning' : 'bg-appBg text-textSecondary'">7 Hari</button>
                                    <button type="button" @click="extensionDays = 14" class="flex-1 rounded border px-2 py-1 text-xs font-semibold" :class="extensionDays == 14 ? 'bg-warning text-white border-warning' : 'bg-appBg text-textSecondary'">14 Hari</button>
                                    <button type="button" @click="extensionDays = 30" class="flex-1 rounded border px-2 py-1 text-xs font-semibold" :class="extensionDays == 30 ? 'bg-warning text-white border-warning' : 'bg-appBg text-textSecondary'">30 Hari</button>
                                    <button type="button" @click="extensionDays = 60" class="flex-1 rounded border px-2 py-1 text-xs font-semibold" :class="extensionDays == 60 ? 'bg-warning text-white border-warning' : 'bg-appBg text-textSecondary'">60 Hari</button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="number" name="extension_tenor_days" x-model.number="extensionDays" min="1" max="365" class="w-full rounded border border-border px-2 py-1 text-xs font-bold text-textPrimary" placeholder="Jumlah hari perpanjangan" />
                                    <span class="text-xs text-textSecondary font-semibold">Hari</span>
                                </div>
                                <p class="mt-1 text-[11px] text-textSecondary">Estimasi Jatuh Tempo Baru: <span class="font-bold text-primary" x-text="newDueDatePreview"></span></p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-border flex justify-between items-center">
                            <span class="text-xs text-textSecondary font-semibold uppercase">Total Pembayaran:</span>
                            <span class="font-bold text-lg text-warning">Rp {{ number_format($transaction->interest_amount + $penaltyInfo['penalty_amount'], 0, ',', '.') }}</span>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-textPrimary mb-1">Catatan Kasir</label>
                    <textarea name="notes" rows="2" placeholder="Catatan transaksi opsional" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-2 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Proses Pembayaran & Cetak Struk
                    </button>
                    <a href="{{ route('petugas.payments.search') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
