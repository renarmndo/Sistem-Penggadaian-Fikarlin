<x-app-layout title="Pengaturan Parameter" role="owner">
    <x-page-header title="Pengaturan Parameter Sistem (FR-3.2)" description="Konfigurasi persentase bunga default, rate denda keterlambatan, tenor baku, dan persentase plafon." />

    <div class="w-full">
        <x-content-card class="w-full">
            <form method="POST" action="{{ route('owner.settings.update') }}" class="space-y-6 w-full">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div class="p-4 rounded-lg bg-appBg border border-border">
                        <label class="block text-sm font-bold text-textPrimary mb-1">Persentase Bunga Gadai (%) <span class="text-danger">*</span></label>
                        <input type="number" name="default_interest_rate" value="{{ old('default_interest_rate', $interestRate) }}" required min="0" max="100" step="0.1" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-primary outline-none focus:border-primary" />
                        <p class="mt-2 text-xs text-textSecondary">Persentase bunga yang dikenakan pada setiap transaksi gadai baru (contoh: 10%).</p>
                    </div>

                    <div class="p-4 rounded-lg bg-appBg border border-border">
                        <label class="block text-sm font-bold text-textPrimary mb-1">Rate Denda Keterlambatan Per Hari (%) <span class="text-danger">*</span></label>
                        <input type="number" name="penalty_rate_per_day" value="{{ old('penalty_rate_per_day', $penaltyRate) }}" required min="0" max="100" step="0.01" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-danger outline-none focus:border-primary" />
                        <p class="mt-2 text-xs text-textSecondary">Persentase denda per hari dari nominal pinjaman apabila melewati tanggal jatuh tempo (contoh: 0.5%).</p>
                    </div>

                    <div class="p-4 rounded-lg bg-appBg border border-border">
                        <label class="block text-sm font-bold text-textPrimary mb-1">Tenor Baku Default (Hari) <span class="text-danger">*</span></label>
                        <input type="number" name="default_tenor_days" value="{{ old('default_tenor_days', $defaultTenor) }}" required min="1" step="1" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-textPrimary outline-none focus:border-primary" />
                        <p class="mt-2 text-xs text-textSecondary">Durasi masa aktif jatuh tempo default transaksi gadai baru dalam hitungan hari (contoh: 30 hari).</p>
                    </div>

                    <div class="p-4 rounded-lg bg-appBg border border-border">
                        <label class="block text-sm font-bold text-textPrimary mb-1">Batas Maksimal Plafon Pinjaman (%) <span class="text-danger">*</span></label>
                        <input type="number" name="plafon_percentage" value="{{ old('plafon_percentage', $plafonPercentage) }}" required min="1" max="100" step="1" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-success outline-none focus:border-primary" />
                        <p class="mt-2 text-xs text-textSecondary">Persentase batas maksimal pinjaman dari total nilai taksiran barang (contoh: 80%).</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Perubahan Parameter Sistem
                    </button>
                </div>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
