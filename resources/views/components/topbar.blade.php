@props([
    'title' => '',
    'subtitle' => '',
])

<header class="sticky top-0 z-30 flex h-16 w-full items-center justify-between border-b border-border bg-surface px-4 shadow-sm lg:px-6">
    {{-- Left: Toggle Hamburger / X + Title --}}
    <div class="flex items-center gap-3">
        {{-- Sidebar Toggle Button with Hamburger and X --}}
        <button
            x-on:click="sidebarOpen = !sidebarOpen"
            class="flex items-center justify-center rounded-lg border border-border p-2 text-textSecondary hover:bg-appBg hover:text-textPrimary transition focus:outline-none focus:ring-2 focus:ring-primary/20"
            :title="sidebarOpen ? 'Tutup Sidebar' : 'Buka Sidebar'"
            :aria-label="sidebarOpen ? 'Tutup sidebar' : 'Buka sidebar'"
        >
            {{-- X Icon (Shown when sidebar is CLOSED) --}}
            <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
            </svg>

            {{-- Hamburger Icon (Shown when sidebar is OPEN) --}}
            <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
            </svg>
        </button>

        <div>
            @if ($subtitle)
                <p class="text-xs text-textSecondary">{{ $subtitle }}</p>
            @endif
            <h1 class="text-base font-semibold text-textPrimary">{{ $title }}</h1>
        </div>
    </div>

    {{-- Right: Profile Dropdown --}}
    <div class="flex items-center gap-3">
        {{-- Profile Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button
                x-on:click="open = !open"
                x-on:click.outside="open = false"
                class="flex items-center gap-2 rounded-lg border border-border px-3 py-1.5 transition hover:bg-appBg"
            >
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white shadow-sm">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="hidden text-left md:block">
                    <p class="text-xs font-semibold text-textPrimary leading-tight">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-[10px] text-textSecondary uppercase font-medium">{{ ucfirst(Auth::user()->role ?? 'petugas') }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="hidden h-4 w-4 text-textSecondary md:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            {{-- Dropdown Menu --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 rounded-card border border-border bg-surface py-1 shadow-card z-50"
                style="display: none;"
            >
                <div class="border-b border-border px-4 py-2">
                    <p class="text-sm font-semibold text-textPrimary">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-textSecondary font-mono">{{ Auth::user()->email ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-danger hover:bg-danger/10 transition font-medium"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                        Keluar (Logout)
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
