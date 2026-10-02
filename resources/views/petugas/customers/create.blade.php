<x-app-layout title="Tambah Nasabah KTP" role="petugas">
    <x-page-header title="Tambah Master Nasabah KTP Baru (FR-1.1)" description="Input identitas KTP dan kontak nasabah penggadaian." />

    <div class="w-full">
        <x-content-card class="w-full">
            <form method="POST" action="{{ route('petugas.customers.store') }}" class="space-y-5 w-full">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nomor KTP / Identitas <span class="text-danger">*</span></label>
                        <input type="text" name="identity_number" value="{{ old('identity_number') }}" required placeholder="Contoh: 1234567890123456" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 font-mono" />
                        <p class="mt-1 text-xs text-textSecondary">Nomor KTP bersifat unik dan wajib untuk identifikasi transaksi.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nama Lengkap Nasabah <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap sesuai KTP" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nomor HP / WhatsApp</label>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="081234567890" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Catatan Tambahan</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Catatan internal opsional" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-textPrimary mb-1">Alamat Domisili Sesuai KTP</label>
                    <textarea name="address" rows="3" placeholder="Alamat domisili lengkap nasabah" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">{{ old('address') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Data Nasabah
                    </button>
                    <a href="{{ route('petugas.customers.index') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
