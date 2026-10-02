<x-app-layout title="Tambah Akun Pengguna" role="owner">
    <x-page-header title="Tambah Akun Pengguna Baru (FR-3.3)" description="Buat akun login untuk Owner, Admin Gudang, atau Petugas Kasir." />

    <div class="w-full">
        <x-content-card class="w-full">
            <form method="POST" action="{{ route('owner.users.store') }}" class="space-y-5 w-full">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Nama Lengkap Pengguna <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama Pengguna" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Email Login <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="nama@buulolo.id" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Kata Sandi <span class="text-danger">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-textPrimary mb-1">Role / Hak Akses <span class="text-danger">*</span></label>
                        <select name="role" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none focus:border-primary">
                            <option value="petugas">Petugas (Front-Office / Kasir)</option>
                            <option value="admin">Admin (Gudang / Verifikator Lelang)</option>
                            <option value="owner">Owner (Pemilik / Executive)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer text-sm text-textPrimary">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-border text-primary" />
                        <span class="font-medium">Status Akun Aktif (Dapat Login ke Sistem)</span>
                    </label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-border">
                    <button type="submit" class="rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition">
                        Simpan Akun Pengguna
                    </button>
                    <a href="{{ route('owner.users.index') }}" class="rounded-lg border border-border px-5 py-3 text-sm font-semibold hover:bg-appBg">
                        Batal
                    </a>
                </div>
            </form>
        </x-content-card>
    </div>
</x-app-layout>
