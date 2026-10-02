<x-app-layout title="Master Data Nasabah" role="petugas">
    <x-page-header title="Master Data Nasabah / Pelanggan (FR-1.1)" description="Input dan kelola identitas KTP nasabah penggadaian.">
        <x-slot:actions>
            <a href="{{ route('petugas.customers.create') }}" class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark">
                + Tambah Nasabah KTP
            </a>
        </x-slot:actions>
    </x-page-header>

    <x-content-card>
        {{-- Search & Filter Bar --}}
        <form method="GET" action="{{ route('petugas.customers.index') }}" class="mb-6 flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor KTP / nama / HP nasabah..." class="w-full max-w-md rounded-lg border border-border bg-white px-3 py-2 text-sm outline-none focus:border-primary focus:ring-2 focus:ring-primary/10" />
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">Cari</button>
            @if(request('search'))
                <a href="{{ route('petugas.customers.index') }}" class="rounded-lg border border-border px-4 py-2 text-sm font-semibold hover:bg-appBg">Reset</a>
            @endif
        </form>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="border-b border-border bg-appBg text-xs font-semibold uppercase text-textSecondary">
                    <tr>
                        <th class="px-4 py-3">No KTP / Identitas</th>
                        <th class="px-4 py-3">Nama Nasabah</th>
                        <th class="px-4 py-3">No. HP</th>
                        <th class="px-4 py-3">Alamat</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border bg-white">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-appBg/50">
                            <td class="px-4 py-3 font-mono font-medium text-primary">{{ $customer->identity_number }}</td>
                            <td class="px-4 py-3 font-semibold text-textPrimary">{{ $customer->name }}</td>
                            <td class="px-4 py-3 text-textSecondary">{{ $customer->phone ?? '-' }}</td>
                            <td class="px-4 py-3 text-textSecondary truncate max-w-xs">{{ $customer->address ?? '-' }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route('petugas.customers.show', $customer->id) }}" class="text-xs font-semibold text-info hover:underline">Detail</a>
                                <a href="{{ route('petugas.customers.edit', $customer->id) }}" class="text-xs font-semibold text-primary hover:underline">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-textSecondary">Data nasabah tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $customers->links() }}
        </div>
    </x-content-card>
</x-app-layout>
