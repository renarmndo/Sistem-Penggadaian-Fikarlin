<x-app-layout title="Manajemen Pengguna" role="owner">
    <x-page-header title="Manajemen Akun Pengguna (FR-3.3)" description="Kelola hak akses pengguna sistem untuk Owner, Admin Gudang, dan Petugas Kasir.">
        <x-slot:actions>
            <a href="{{ route('owner.users.create') }}" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">
                + Tambah Akun Pengguna
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-content-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email Kredensial</th>
                        <th class="px-4 py-3">Role Hak Akses</th>
                        <th class="px-4 py-3">Status Akun</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($users as $u)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-semibold text-textPrimary">{{ $u->name }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-textSecondary">{{ $u->email }}</td>
                            <td class="px-4 py-3 font-semibold uppercase text-primary text-xs">{{ $u->role }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $u->is_active ? 'bg-success/10 text-success border border-success/20' : 'bg-danger/10 text-danger border border-danger/20' }}">
                                    {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('owner.users.edit', $u->id) }}" class="text-xs font-semibold text-primary hover:underline">Edit</a>
                                @if ($u->id !== auth()->id())
                                    <form method="POST" action="{{ route('owner.users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-danger hover:underline">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-textSecondary">Belum ada akun pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </x-content-card>
</x-app-layout>
