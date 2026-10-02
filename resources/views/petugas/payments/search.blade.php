<x-app-layout title="Pelunasan & Perpanjangan" role="petugas">
    <x-page-header title="Pelunasan & Perpanjangan Gadai (FR-1.3)" description="Scan Barcode SBG atau masukkan Nomor Tiket SBG untuk melakukan pelunasan atau perpanjangan." />

    <div class="w-full">
        <x-content-card title="Scan Barcode / Cari Nomor Tiket SBG" class="w-full">
            <form method="GET" action="{{ route('petugas.payments.search') }}" class="space-y-4 w-full">
                <div>
                    <label class="block text-sm font-medium text-textPrimary mb-1">Kode Barcode / Nomor Tiket SBG <span class="text-danger">*</span></label>
                    <div class="relative">
                        <input type="text" name="code" value="{{ request('code') }}" required autofocus placeholder="Scan Barcode / Ketik SBG-20260818-XXXX" class="w-full rounded-lg border border-border bg-white pl-12 pr-4 py-3.5 text-lg font-mono font-bold text-primary outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                        <div class="absolute left-4 top-4 text-textSecondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/></svg>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-textSecondary">Gunakan barcode scanner USB pada kasir atau ketik manual kode tiket dari fisik Surat Bukti Gadai (SBG).</p>
                </div>

                <button type="submit" class="w-full rounded-lg bg-primary py-3.5 text-base font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                    Cari & Memproses Transaksi SBG
                </button>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
