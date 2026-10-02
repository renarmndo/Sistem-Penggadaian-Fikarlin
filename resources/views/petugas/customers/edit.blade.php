<x-app-layout title="Edit Data Nasabah" role="petugas">
    <x-page-header title="Edit Data Nasabah KTP (FR-1.1)" description="Perbarui informasi nasabah {{ $customer->name }}" />

    <div class="w-full">
        <x-content-card class="w-full">
            <form method="POST" action="{{ route('petugas.customers.update', $customer->id) }}" class="space-y-5 w-full">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nomor KTP / Identitas <span class="text-danger">*</span></label>
                        <input type="text" name="identity_number" value="{{ old('identity_number', $customer->identity_number) }}" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 font-mono" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nama Lengkap Nasabah <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nomor HP / Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Catatan Tambahan</label>
                        <input type="text" name="notes" value="{{ old('notes', $customer->notes) }}" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-textPrimary mb-1">Alamat Domisili</label>
                    <textarea name="address" rows="3" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">{{ old('address', $customer->address) }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('petugas.customers.index') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
