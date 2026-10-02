<x-app-layout title="Gadai Baru" role="petugas">
    <x-page-header title="Form Transaksi Gadai Baru (FR-1.2)" description="Input data nasabah, spesifikasi barang jaminan, kalkulasi otomatis nilai plafon, dan tentukan tenor masa gadai." />

    <div class="w-full" x-data="{
        estimatedValue: 0,
        loanAmount: 0,
        interestRate: 10,
        plafonPercentage: 80,
        tenorDays: 30,
        get maxPlafon() {
            return Math.round((this.estimatedValue * this.plafonPercentage) / 100);
        },
        get interestAmount() {
            return Math.round((this.loanAmount * this.interestRate) / 100);
        },
        get totalAmount() {
            return Number(this.loanAmount) + Number(this.interestAmount);
        },
        get dueDateFormatted() {
            let d = new Date();
            d.setDate(d.getDate() + Number(this.tenorDays || 0));
            return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
        },
        updatePlafon() {
            this.loanAmount = this.maxPlafon;
        },
        setTenor(days) {
            this.tenorDays = days;
        }
    }">
        <form method="POST" action="{{ route('petugas.pawn.store') }}" class="space-y-6 w-full">
            @csrf

            {{-- Grid Section: Customer & Item --}}
            <div class="grid gap-6 lg:grid-cols-2 w-full">
                {{-- Card 1: Data Nasabah --}}
                <x-content-card title="1. Identitas Nasabah / Pelanggan (KTP)" class="w-full">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Pilih Nasabah Terdaftar <span class="text-danger">*</span></label>
                            <select name="customer_id" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10">
                                <option value="">-- Pilih Nasabah (Nomor KTP - Nama) --</option>
                                @foreach ($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->identity_number }} — {{ $c->name }} ({{ $c->phone }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-textSecondary">Pastikan identitas KTP nasabah telah diverifikasi.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Catatan Tambahan Transaksi</label>
                            <textarea name="notes" rows="3" placeholder="Catatan internal transaksi (opsional)" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </x-content-card>

                {{-- Card 2: Spesifikasi Barang Jaminan --}}
                <x-content-card title="2. Spesifikasi Barang Jaminan (Elektronik)" class="w-full">
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-textPrimary mb-1">Nama Barang / Perangkat <span class="text-danger">*</span></label>
                            <input type="text" name="item_name" value="{{ old('item_name') }}" required placeholder="Contoh: iPhone 13 Pro Max 256GB" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
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
                            <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Apple, Samsung, Asus" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Tipe / Seri</label>
                            <input type="text" name="model_type" value="{{ old('model_type') }}" placeholder="Model / Seri" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-textPrimary mb-1">Kondisi & Kelengkapan</label>
                            <input type="text" name="condition_notes" value="{{ old('condition_notes') }}" placeholder="Mulus, Fullset, Dusbook" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                        </div>
                    </div>
                </x-content-card>
            </div>

            {{-- Card 3: Taksiran, Plafon, & Fleksibilitas Tenor --}}
            <x-content-card title="3. Nilai Taksiran, Plafon Pinjaman & Penentuan Tenor Masa Gadai" class="w-full">
                <div class="grid gap-6 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nilai Taksiran Barang (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="estimated_value" x-model.number="estimatedValue" @input="updatePlafon()" required min="1000" step="1000" placeholder="0" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-primary outline-none focus:border-primary" />
                        <p class="mt-1 text-xs text-textSecondary">Batas Plafon Maksimal (80%): <span class="font-bold text-primary" x-text="'Rp ' + maxPlafon.toLocaleString('id-ID')"></span></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Plafon Pinjaman Disepakati (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="loan_amount" x-model.number="loanAmount" required min="1000" step="1000" placeholder="0" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-base font-bold text-success outline-none focus:border-primary" />
                    </div>

                    {{-- Customizable Tenor Selector --}}
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Tentukan Durasi Tenor (Hari) <span class="text-danger">*</span></label>
                        <div class="flex gap-1.5 mb-2">
                            <button type="button" @click="setTenor(7)" class="flex-1 rounded border px-2 py-1 text-xs font-semibold transition" :class="tenorDays == 7 ? 'bg-primary text-white border-primary' : 'bg-appBg text-textSecondary hover:bg-border'">7 Hari</button>
                            <button type="button" @click="setTenor(14)" class="flex-1 rounded border px-2 py-1 text-xs font-semibold transition" :class="tenorDays == 14 ? 'bg-primary text-white border-primary' : 'bg-appBg text-textSecondary hover:bg-border'">14 Hari</button>
                            <button type="button" @click="setTenor(30)" class="flex-1 rounded border px-2 py-1 text-xs font-semibold transition" :class="tenorDays == 30 ? 'bg-primary text-white border-primary' : 'bg-appBg text-textSecondary hover:bg-border'">30 Hari</button>
                            <button type="button" @click="setTenor(60)" class="flex-1 rounded border px-2 py-1 text-xs font-semibold transition" :class="tenorDays == 60 ? 'bg-primary text-white border-primary' : 'bg-appBg text-textSecondary hover:bg-border'">60 Hari</button>
                        </div>
                        <div class="relative">
                            <input type="number" name="tenor_days" x-model.number="tenorDays" required min="1" max="365" placeholder="Ketik jumlah hari tenor" class="w-full rounded-lg border border-border bg-white px-3 py-2 text-sm font-bold text-textPrimary outline-none focus:border-primary" />
                            <span class="absolute right-3 top-2.5 text-xs text-textSecondary font-semibold">Hari</span>
                        </div>
                    </div>
                </div>

                {{-- Live Summary Calculation Card --}}
                <div class="mt-6 rounded-card border border-primary/20 bg-primary/5 p-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4 text-sm w-full">
                    <div>
                        <span class="text-xs text-textSecondary uppercase font-medium">Bunga Gadai Sistem:</span>
                        <p class="font-bold text-textPrimary text-base" x-text="interestRate + '% (' + (interestAmount ? 'Rp ' + interestAmount.toLocaleString('id-ID') : 'Rp 0') + ')'"></p>
                    </div>
                    <div>
                        <span class="text-xs text-textSecondary uppercase font-medium">Durasi Tenor Gadai:</span>
                        <p class="font-bold text-textPrimary text-base" x-text="(tenorDays || 0) + ' Hari'"></p>
                    </div>
                    <div>
                        <span class="text-xs text-textSecondary uppercase font-medium">Tanggal Jatuh Tempo:</span>
                        <p class="font-bold text-danger text-base" x-text="dueDateFormatted"></p>
                    </div>
                    <div>
                        <span class="text-xs text-textSecondary uppercase font-medium">Total Tagihan Tebus:</span>
                        <p class="font-bold text-success text-lg" x-text="'Rp ' + totalAmount.toLocaleString('id-ID')"></p>
                    </div>
                </div>

                <div class="mt-6 flex items-center gap-3 pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Transaksi & Generate Barcode SBG
                    </button>
                    <a href="{{ route('petugas.pawn.index') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </x-content-card>
        </form>
    </div>
</x-app-layout>
