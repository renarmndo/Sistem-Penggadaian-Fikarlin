@props([
    'role' => 'petugas',
])

@php
    $menus = match($role) {
        'owner' => [
            ['route' => 'owner.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['route' => 'owner.reports.index', 'label' => 'Laporan & Laba/Rugi', 'icon' => 'chart'],
            ['route' => 'owner.settings.index', 'label' => 'Pengaturan Parameter', 'icon' => 'settings'],
            ['route' => 'owner.users.index', 'label' => 'Manajemen Pengguna', 'icon' => 'list'],
        ],
        'admin' => [
            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['route' => 'admin.warehouse.index', 'label' => 'Lokasi & Stok Gudang', 'icon' => 'package'],
            ['route' => 'admin.duedate.index', 'label' => 'Kontrak Jatuh Tempo', 'icon' => 'clock'],
            ['route' => 'admin.auction.index', 'label' => 'Siap Jual / Lelang', 'icon' => 'gavel'],
        ],
        default => [
            ['route' => 'petugas.dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
            ['route' => 'petugas.customers.index', 'label' => 'Data Nasabah (KTP)', 'icon' => 'list'],
            ['route' => 'petugas.pawn.create', 'label' => 'Gadai Baru', 'icon' => 'dollar'],
            ['route' => 'petugas.payments.search', 'label' => 'Pelunasan & Perpanjangan', 'icon' => 'refresh'],
            ['route' => 'petugas.purchases.create', 'label' => 'Beli Barang Bekas', 'icon' => 'phone'],
            ['route' => 'petugas.auction.index', 'label' => 'Penjualan Lelang', 'icon' => 'gavel'],
            ['route' => 'petugas.transactions.daily', 'label' => 'Riwayat Kasir Harian', 'icon' => 'history'],
        ],
    };

    $roleLabel = match($role) {
        'owner' => 'Owner',
        'admin' => 'Admin Gudang',
        default => 'Petugas Front-Office',
    };
@endphp

{{-- Sidebar Container (Hides completely off-canvas when closed, no icons visible) --}}
<aside
    class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col bg-primary text-white shadow-2xl transition-transform duration-300 ease-in-out"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
>
    {{-- Brand Header --}}
    <div class="flex h-16 items-center justify-between border-b border-white/10 px-5 bg-primaryDark/50">
        <div class="min-w-0 flex-1">
            <h2 class="truncate text-base font-bold tracking-tight text-white">Buulolo Cell99</h2>
            <p class="truncate text-[11px] text-white/70 font-medium">SIM Operasional Penggadaian</p>
        </div>
        <div class="rounded-md bg-secondary/20 px-2 py-1 text-[11px] font-bold text-secondary border border-secondary/30">
            BC99
        </div>
    </div>

    {{-- Navigation Links --}}
    <nav class="flex-1 space-y-1.5 overflow-y-auto px-3.5 py-4">
        @foreach ($menus as $menu)
            @php
                $isActive = request()->routeIs($menu['route'] . '*') || request()->routeIs($menu['route']);
            @endphp
            <a
                href="{{ route($menu['route']) }}"
                class="flex items-center gap-3 rounded-lg px-3.5 py-2.5 text-sm font-medium transition-all duration-150
                    {{ $isActive
                        ? 'bg-secondary text-white shadow-sm font-semibold translate-x-1'
                        : 'text-white/80 hover:bg-white/10 hover:text-white'
                    }}"
            >
                @if ($menu['icon'] === 'home')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                @elseif ($menu['icon'] === 'list')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" x2="21" y1="6" y2="6"/><line x1="8" x2="21" y1="12" y2="12"/><line x1="8" x2="21" y1="18" y2="18"/><line x1="3" x2="3.01" y1="6" y2="6"/><line x1="3" x2="3.01" y1="12" y2="12"/><line x1="3" x2="3.01" y1="18" y2="18"/></svg>
                @elseif ($menu['icon'] === 'package')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                @elseif ($menu['icon'] === 'chart')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>
                @elseif ($menu['icon'] === 'settings')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                @elseif ($menu['icon'] === 'clock')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                @elseif ($menu['icon'] === 'gavel')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m14 13-7.5 7.5c-.83.83-2.17.83-3 0 0 0 0 0 0 0a2.12 2.12 0 0 1 0-3L11 10"/><path d="m16 16 6-6"/><path d="m8 8 6-6"/><path d="m9 7 8 8"/><path d="m21 11-8-8"/></svg>
                @elseif ($menu['icon'] === 'dollar')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M16 8h-6a2 2 0 1 0 0 4h4a2 2 0 1 1 0 4H8"/><path d="M12 18V6"/></svg>
                @elseif ($menu['icon'] === 'check')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
                @elseif ($menu['icon'] === 'refresh')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                @elseif ($menu['icon'] === 'phone')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>
                @elseif ($menu['icon'] === 'history')
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
                @endif

                <span class="truncate">{{ $menu['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- User Info Footer --}}
    <div class="border-t border-white/10 p-4 bg-primaryDark/30">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/15 text-sm font-bold text-white border border-white/20">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold text-white">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="truncate text-xs text-white/60 font-medium">{{ $roleLabel }}</p>
            </div>
        </div>
    </div>
</aside>

{{-- Mobile Overlay Backdrop (only for mobile screens) --}}
<div
    x-show="sidebarOpen"
    x-on:click="sidebarOpen = false"
    class="fixed inset-0 z-30 bg-black/50 lg:hidden"
    x-transition:enter="transition-opacity ease-linear duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition-opacity ease-linear duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none;"
></div>
