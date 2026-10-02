<x-app-layout title="Input Beli Barang Bekas" role="petugas">
    <x-page-header title="Transaksi Beli Barang Bekas (FR-1.4)" description="Simpan transaksi pembelian barang bekas dari nasabah. Otomatis mencatat arus kas keluar dan menambah stok etalase toko." />

    <div class="w-full">
        <form method="POST" action="{{ route('petugas.purchases.store') }}" class="space-y-6 w-full">
            @csrf

            <div class="grid gap-6 lg:grid-cols-2 w-full">
                {{-- Penjual Card --}}
                <x-content-card title="1. Identitas Penjual (Nasabah KTP)" class="w-full">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Pilih Penjual Terdaftar <span class="text-danger">*</span></label>
                            <select name="customer_id" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary">
                                <option value="">-- Pilih Penjual (Nomor KTP - Nama) --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->identity_number }} — {{ $c->name }} ({{ $c->phone }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Catatan Tambahan</label>
                            <textarea name="notes" rows="3" placeholder="Catatan pembelian barang bekas" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </x-content-card>

                {{-- Spesifikasi Barang --}}
                <x-content-card title="2. Spesifikasi Barang Bekas" class="w-full">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-textPrimary mb-1">Nama Barang Bekas <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" value="{{ old('item_name') }}" required placeholder="Contoh: Samsung Galaxy S22 Ultra 256GB" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Kategori Barang <span class="text-danger">*</span></label>
                            <select name="category" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary">
                                <option value="HP/Smartphone">HP / Smartphone</option>
                                <option value="Laptop">Laptop / Notebook</option>
                                <option value="TV/Electronics">TV / Elektronik</option>
                                <option value="Kamera">Kamera / Videografi</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Merk / Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Samsung, Apple, Asus" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Tipe / Model</label>
                            <input type="text" name="model_type" value="{{ old('model_type') }}" placeholder="Spesifikasi / Seri" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Kondisi Barang Bekas</label>
                            <input type="text" name="condition_notes" value="{{ old('condition_notes') }}" placeholder="Mulus 95%, Batteray Health 88%" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>
                    </div>
                </x-content-card>
            </div>

            {{-- Harga Pembelian & Rencana Jual --}}
            <x-content-card title="3. Harga Pembelian (Arus Kas Keluar) & Etalase" class="w-full">
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Harga Beli Disepakati (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="purchase_price" value="{{ old('purchase_price') }}" required min="1000" step="1000" placeholder="0" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-danger outline-none focus:border-primary" />
                        <p class="mt-1 text-xs text-textSecondary">Dicatat sebagai arus kas keluar kasir.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Rencana Harga Jual Etalase Toko (Rp)</label>
                        <input type="number" name="selling_price" value="{{ old('selling_price') }}" min="1000" step="1000" placeholder="0" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-success outline-none focus:border-primary" />
                        <p class="mt-1 text-xs text-textSecondary">Harga banderol untuk penjualan di etalase toko.</p>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3 pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Transaksi & Tambahkan ke Stok Etalase
                    </button>
                    <a href="{{ route('petugas.purchases.index') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </x-content-card>
        </form>
    </div>
</x-app-layout>
