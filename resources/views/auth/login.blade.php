<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Sistem Informasi Operasional Penggadaian Buulolo Cell99</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-appBg flex items-center justify-center p-4 antialiased text-textPrimary">
    <div class="w-full max-w-md bg-surface rounded-card shadow-card border border-border overflow-hidden">
        {{-- Header --}}
        <div class="bg-primary p-6 text-white text-center">
            <div class="mx-auto h-12 w-12 rounded-xl bg-secondary/20 flex items-center justify-center text-secondary mb-3 border border-secondary/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M7 7h10"/><path d="M7 12h10"/><path d="M7 17h10"/></svg>
            </div>
            <h1 class="text-xl font-bold">Konter Buulolo Cell99</h1>
            <p class="text-xs text-white/70 mt-1">Sistem Informasi Manajemen Operasional Penggadaian</p>
        </div>

        {{-- Form Body --}}
        <div class="p-6 space-y-4">
            @if ($errors->any())
                <div class="p-3 bg-danger/10 border border-danger/20 rounded-lg text-xs text-danger">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-textSecondary uppercase tracking-wider mb-1.5">Email Kredensial</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10" placeholder="nama@buulolo.id" />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-textSecondary uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10" placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 text-textSecondary cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-border text-primary focus:ring-primary/20" />
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <button type="submit" class="w-full rounded-lg bg-primary py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark transition focus:outline-none focus:ring-2 focus:ring-primary/30">
                    Masuk ke Sistem
                </button>
            </form>

            {{-- Demo Hint --}}
            <div class="mt-6 pt-4 border-t border-border text-center text-xs text-textSecondary space-y-1">
                <p class="font-semibold text-textPrimary">Demo Akun Login:</p>
                <p>Owner: <span class="font-mono text-primary">owner@buulolo.id</span> (pass: password)</p>
                <p>Admin: <span class="font-mono text-primary">admin@buulolo.id</span> (pass: password)</p>
                <p>Petugas: <span class="font-mono text-primary">petugas@buulolo.id</span> (pass: password)</p>
            </div>
        </div>
    </div>
</body>
</html>
