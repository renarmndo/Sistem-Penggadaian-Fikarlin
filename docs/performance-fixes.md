# Perbaikan Masalah Lazy Loading & Performance

## Masalah yang Dideteksi

Dari log server menunjukkan:
- Redirect loop antara `/` dan `/login` dengan response time ~500ms
- Kemungkinan N+1 query problem (lazy loading)
- Session management yang belum optimal

## Perbaikan yang Dilakukan

### 1. **Perbaikan Redirect Loop** (`routes/web.php`)

**Sebelum:**
```php
Route::get('/', function () {
    return redirect()->route('login');
});
```

**Sesudah:**
```php
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return match ($user->role) {
            \App\Models\User::ROLE_OWNER => redirect()->route('owner.dashboard'),
            \App\Models\User::ROLE_ADMIN => redirect()->route('admin.dashboard'),
            \App\Models\User::ROLE_PETUGAS => redirect()->route('petugas.dashboard'),
            default => redirect()->route('login'),
        };
    }
    return redirect()->route('login');
});
```

**Manfaat:** 
- Menghindari redirect loop
- Langsung mengarahkan user yang sudah login ke dashboard mereka
- Mengurangi 1 HTTP request

### 2. **Prevent Lazy Loading** (`app/Providers/AppServiceProvider.php`)

```php
public function boot(): void
{
    // Prevent lazy loading in development to catch N+1 issues
    if ($this->app->environment('local')) {
        \Illuminate\Database\Eloquent\Model::preventLazyLoading();
    }
}
```

**Manfaat:**
- Memaksa developer untuk menggunakan eager loading
- Mendeteksi N+1 query problem di development
- Error akan muncul jika ada lazy loading, membantu debugging

### 3. **Eager Loading di AdminDashboardController**

**Sebelum:**
```php
$itemsActionNeeded = Item::whereIn('status', [Item::STATUS_SIAP_LELANG])
    ->orWhere(function ($q) use ($today) {
        $q->where('location', Item::LOCATION_RAK_GUDANG)
          ->whereHas('pawnTransaction', function ($pt) use ($today) {
              $pt->where('due_date', '<', $today);
          });
    })
    ->latest()
    ->take(10)
    ->get();
```

**Sesudah:**
```php
$itemsActionNeeded = Item::with('pawnTransaction')
    ->whereIn('status', [Item::STATUS_SIAP_LELANG])
    ->orWhere(function ($q) use ($today) {
        $q->where('location', Item::LOCATION_RAK_GUDANG)
          ->whereHas('pawnTransaction', function ($pt) use ($today) {
              $pt->where('due_date', '<', $today);
          });
    })
    ->latest()
    ->take(10)
    ->get();
```

**Manfaat:**
- Mengurangi jumlah query dari N+1 menjadi 2 query
- Performa lebih cepat saat load dashboard admin

### 4. **Optimasi Session Configuration** (`.env`)

**Sebelum:**
```
SESSION_LIFETIME=120
```

**Sesudah:**
```
SESSION_LIFETIME=720
```

**Manfaat:**
- Session tidak expired terlalu cepat (dari 2 jam menjadi 12 jam)
- Mengurangi kemungkinan user harus re-authenticate berulang kali

### 5. **Middleware Alias** (`bootstrap/app.php`)

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

Dan di `routes/web.php`:
```php
// Sebelum
Route::middleware(['auth', RoleMiddleware::class . ':petugas,admin,owner'])

// Sesudah
Route::middleware(['auth', 'role:petugas,admin,owner'])
```

**Manfaat:**
- Kode lebih clean dan mudah dibaca
- Konsisten dengan Laravel best practices
- Lebih mudah maintenance

## Cara Test

1. **Clear cache dan restart server:**
```bash
php artisan optimize:clear
php artisan serve
```

2. **Test redirect:**
   - Buka `http://localhost:8000` (tidak login) → harus redirect ke `/login`
   - Login sebagai user → buka `http://localhost:8000` → langsung ke dashboard sesuai role

3. **Test performa:**
   - Perhatikan log server, seharusnya tidak ada lagi redirect loop
   - Response time seharusnya < 100ms untuk route yang sudah di-cache

4. **Test lazy loading prevention:**
   - Jika ada N+1 query, akan muncul error di development
   - Production tidak akan terpengaruh

## Rekomendasi Tambahan

### 1. Install Laravel Debugbar (Development)
```bash
composer require barryvdh/laravel-debugbar --dev
```

### 2. Monitor Query Performance
Tambahkan di `AppServiceProvider::boot()` untuk development:
```php
if ($this->app->environment('local')) {
    \DB::listen(function ($query) {
        if ($query->time > 100) {
            \Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'time' => $query->time
            ]);
        }
    });
}
```

### 3. Gunakan Query Caching untuk Dashboard
Untuk dashboard yang jarang berubah, gunakan cache:
```php
$totalPiutangAktif = Cache::remember('dashboard.total_piutang', 300, function () {
    return PawnTransaction::whereIn('status', [
        PawnTransaction::STATUS_TERSIMPAN,
        PawnTransaction::STATUS_DIPERPANJANG
    ])->sum('loan_amount');
});
```

### 4. Index Database
Pastikan kolom yang sering di-query sudah punya index:
- `pawn_transactions.status`
- `pawn_transactions.due_date`
- `items.status`
- `items.location`
- `pawn_transactions.created_at`

## Expected Results

Setelah perbaikan ini:
- ✅ Tidak ada redirect loop
- ✅ Response time < 100ms untuk route simple
- ✅ N+1 query terdeteksi dan diperbaiki
- ✅ Session management lebih baik
- ✅ Kode lebih clean dan maintainable
