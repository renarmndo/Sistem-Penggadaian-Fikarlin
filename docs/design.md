# Design Guideline — Sistem Informasi Manajemen Operasional Penggadaian
### Konter Buulolo Cell99

Dokumen ini menjadi acuan desain antarmuka web sistem. Fokus utama: layout konsisten, menu berbasis role, sidebar toggle, topbar, komponen UI, dan implementasi menggunakan **Tailwind CSS**.

---

## 1. Prinsip Desain

- **Sederhana & fungsional** — Petugas front-office harus cepat memahami alur transaksi.
- **Konsisten antar role** — Owner, Admin, dan Petugas memakai layout dasar yang sama.
- **Berbasis data** — angka nominal, status barang, jatuh tempo, dan laporan harus mudah dibaca.
- **Profesional & terpercaya** — sistem berkaitan dengan uang, barang jaminan, dan laporan usaha.
- **Responsive** — nyaman dipakai di desktop internal, tetap dapat dibuka di tablet/laptop kecil.

---

## 2. Design Token Tailwind CSS

Gunakan token berikut di `tailwind.config.js` agar warna dan layout konsisten.

```js
/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{js,ts,jsx,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1E3A5F',
        primaryDark: '#172E4B',
        secondary: '#C79A2B',
        success: '#2E9E5B',
        warning: '#E08E2C',
        danger: '#D64545',
        info: '#3B82C4',
        appBg: '#F5F6F8',
        surface: '#FFFFFF',
        border: '#E2E5EA',
        textPrimary: '#1F2430',
        textSecondary: '#6B7280',
      },
      fontFamily: {
        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        card: '0 8px 24px rgba(15, 23, 42, 0.06)',
      },
      borderRadius: {
        card: '12px',
      },
    },
  },
  plugins: [],
}
```

### 2.1 Warna Utama

| Token | Hex | Penggunaan |
|---|---|---|
| `primary` | `#1E3A5F` | Sidebar, topbar, tombol utama |
| `primaryDark` | `#172E4B` | Hover sidebar/topbar |
| `secondary` | `#C79A2B` | Menu aktif, aksen gadai, badge penting |
| `success` | `#2E9E5B` | Status ditebus/lunas/berhasil |
| `warning` | `#E08E2C` | Mendekati jatuh tempo/kandidat lelang |
| `danger` | `#D64545` | Jatuh tempo, gagal, denda |
| `info` | `#3B82C4` | Siap jual/lelang, notifikasi umum |
| `appBg` | `#F5F6F8` | Background halaman |
| `surface` | `#FFFFFF` | Card, panel, modal |
| `border` | `#E2E5EA` | Border input, table, separator |
| `textPrimary` | `#1F2430` | Teks utama |
| `textSecondary` | `#6B7280` | Label, hint, subtitle |

---

## 3. Struktur Layout Aplikasi

Semua role memakai layout utama yang sama.

```txt
┌───────────────────────────────────────────────────────────────┐
│ Topbar: toggle, breadcrumb/search, notification, profile       │
├───────────────┬───────────────────────────────────────────────┤
│ Sidebar       │ Main Content                                  │
│ role menu     │ Page Header                                   │
│ collapsible   │ Summary Cards / Action Bar                    │
│               │ Table / Form / Report / Detail                │
└───────────────┴───────────────────────────────────────────────┘
```

### 3.1 App Shell

```html
<div class="min-h-screen bg-appBg text-textPrimary">
  <aside class="fixed inset-y-0 left-0 z-40 bg-primary text-white transition-all duration-300">
    <!-- Sidebar -->
  </aside>

  <div class="transition-all duration-300 lg:pl-64">
    <header class="sticky top-0 z-30 h-16 border-b border-border bg-surface/95 backdrop-blur">
      <!-- Topbar -->
    </header>

    <main class="p-4 sm:p-6 lg:p-8">
      <!-- Page content -->
    </main>
  </div>
</div>
```

### 3.2 Ukuran Layout

| Bagian | Desktop | Collapsed | Mobile |
|---|---:|---:|---:|
| Sidebar | `w-64` / 256px | `w-20` / 80px | drawer overlay |
| Topbar | `h-16` / 64px | `h-16` | `h-16` |
| Main padding | `p-8` | `p-8` | `p-4` |
| Card gap | `gap-4` / `gap-6` | sama | `gap-4` |

---

## 4. Sidebar

Sidebar berisi logo, identitas role, menu utama, dan tombol collapse.

### 4.1 Sidebar Expanded

```txt
┌────────────────────────┐
│ Buulolo Cell99         │
│ Sistem Penggadaian     │
├────────────────────────┤
│ Dashboard              │
│ Data Nasabah           │
│ Gadai Baru             │
│ Pelunasan & Perpanjangan│
│ Beli Barang Bekas      │
│ Penjualan Lelang       │
├────────────────────────┤
│ 👤 Nama User           │
│ Role: Petugas          │
└────────────────────────┘
```

### 4.2 Sidebar Collapsed

```txt
┌──────┐
│ BC   │
├──────┤
│ 🏠   │
│ 👤   │
│ 💰   │
│ 🔄   │
│ 📱   │
│ 🏷️   │
└──────┘
```

### 4.3 Class Sidebar

```html
<aside class="fixed inset-y-0 left-0 z-40 flex flex-col bg-primary text-white shadow-xl transition-all duration-300 lg:translate-x-0">
  <div class="flex h-16 items-center justify-between border-b border-white/10 px-4">
    <div class="min-w-0">
      <p class="truncate text-sm font-semibold">Buulolo Cell99</p>
      <p class="truncate text-xs text-white/70">Sistem Penggadaian</p>
    </div>
    <button class="rounded-lg p-2 text-white/80 hover:bg-white/10 hover:text-white">
      <!-- toggle icon -->
    </button>
  </div>

  <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
    <!-- menu item -->
  </nav>

  <div class="border-t border-white/10 p-4">
    <!-- user info -->
  </div>
</aside>
```

### 4.4 Menu Item

```html
<a class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-white/80 hover:bg-primaryDark hover:text-white">
  <span class="h-5 w-5">Icon</span>
  <span>Label Menu</span>
</a>
```

Menu aktif:

```html
<a class="flex items-center gap-3 rounded-lg bg-secondary px-3 py-2.5 text-sm font-semibold text-white shadow-sm">
  <span class="h-5 w-5">Icon</span>
  <span>Dashboard</span>
</a>
```

---

## 5. Topbar

Topbar berisi tombol toggle sidebar, breadcrumb/judul halaman, pencarian cepat, notifikasi, dan profil user.

```txt
┌───────────────────────────────────────────────────────────────┐
│ ☰  Dashboard / Transaksi Gadai     Cari transaksi...   🔔 👤 │
└───────────────────────────────────────────────────────────────┘
```

### 5.1 Class Topbar

```html
<header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-border bg-surface px-4 shadow-sm lg:px-6">
  <div class="flex items-center gap-3">
    <button class="rounded-lg border border-border p-2 text-textSecondary hover:bg-appBg hover:text-textPrimary lg:hidden">
      <!-- mobile sidebar toggle -->
    </button>
    <div>
      <p class="text-xs text-textSecondary">Dashboard</p>
      <h1 class="text-base font-semibold text-textPrimary">Ringkasan Hari Ini</h1>
    </div>
  </div>

  <div class="flex items-center gap-3">
    <input class="hidden w-72 rounded-lg border border-border bg-appBg px-3 py-2 text-sm outline-none focus:border-primary focus:bg-white md:block" placeholder="Cari nasabah / no transaksi..." />
    <button class="relative rounded-lg border border-border p-2 text-textSecondary hover:bg-appBg">
      <!-- notification icon -->
    </button>
    <button class="flex items-center gap-2 rounded-lg border border-border px-3 py-2 hover:bg-appBg">
      <!-- profile -->
    </button>
  </div>
</header>
```

---

## 6. Menu Per Role

### 6.1 Menu Owner

Owner fokus pada dashboard eksekutif, rekap transaksi, laporan laba/rugi, kelola akun pengguna, dan pengaturan parameter sistem.

| Menu | Isi Halaman | Aksi Utama |
|---|---|---|
| Dashboard | Ringkasan eksekutif (total piutang aktif, total barang gudang, estimasi bunga masuk) | Lihat detail (FR-3.1) |
| Rekap Transaksi | Rekap terpusat seluruh transaksi gadai, pelunasan, perpanjangan, pembelian, dan lelang | Filter, cetak (FR-3.4) |
| Laporan Laba/Rugi | Laporan keuangan & laba/rugi real-time periode terpilih | Cetak/export PDF (FR-3.4) |
| Pengaturan Parameter | Parameter sistem: persentase bunga, rate denda, tenor baku | Ubah & simpan (FR-3.2) |
| Manajemen Pengguna | Kelola akun pengguna sistem (Owner, Admin, Petugas) | Tambah/Ubah/Nonaktifkan (FR-3.3) |

Urutan sidebar Owner:

```txt
Dashboard
Rekap Transaksi
Laporan Laba/Rugi
Pengaturan Parameter
Manajemen Pengguna
```

### 6.2 Menu Admin

Admin fokus pada kelola stok & lokasi rak gudang, monitoring jatuh tempo, dan status barang macet ke siap lelang.

| Menu | Isi Halaman | Aksi Utama |
|---|---|---|
| Dashboard | Ringkasan stok gudang, alert mendekati/melewati jatuh tempo | Quick Action (FR-2.2) |
| Lokasi & Stok Gudang | Kelola stok dan posisi/rak barang jaminan di gudang | Detail & update lokasi (FR-2.1) |
| Kontrak Jatuh Tempo | Daftar kontrak gadai mendekati & melewati jatuh tempo | Monitoring (FR-2.2) |
| Siap Jual / Lelang | Ubah status barang macet/wanprestasi menjadi status siap jual/lelang | Ubah status & verifikasi (FR-2.3) |
| Penjualan Lelang | Catat transaksi penjualan barang lelang kepada pembeli | Input penjualan (FR-2.4) |

Urutan sidebar Admin:

```txt
Dashboard
Lokasi & Stok Gudang
Kontrak Jatuh Tempo
Siap Jual / Lelang
Penjualan Lelang
```

### 6.3 Menu Petugas

Petugas fokus pada operasional front-office: master nasabah, gadai baru (cetak SBG), pelunasan & perpanjangan, dan pembelian barang bekas.

| Menu | Isi Halaman | Aksi Utama |
|---|---|---|
| Dashboard | Shortcut transaksi cepat, riwayat transaksi hari ini | Mulai transaksi |
| Data Nasabah | Input dan kelola master data nasabah/pelanggan | Tambah/Ubah (FR-1.1) |
| Gadai Baru | Form transaksi gadai baru (taksiran, plafon, tenor, hitung bunga) | Submit, barcode, cetak SBG (FR-1.2, FR-1.5) |
| Pelunasan & Perpanjangan | Scan Barcode SBG, denda otomatis, tebus lunas (`LUNAS`) / perpanjangan (`DIPERPANJANG`) | Simpan, cetak struk/nota (FR-1.3, FR-1.5) |
| Beli Barang Bekas | Form pembelian barang elektronik bekas dari nasabah | Simpan (kas keluar & etalase), cetak nota (FR-1.4, FR-1.5) |
| Penjualan Lelang | Modul penjualan barang lelang di front-office | Input harga jual -> update `TERJUAL` (FR-2.4) |

Urutan sidebar Petugas:

```txt
Dashboard
Data Nasabah
Gadai Baru
Pelunasan & Perpanjangan
Beli Barang Bekas
Penjualan Lelang
```



---

## 7. Layout Halaman Standar

Semua halaman mengikuti struktur berikut.

```txt
Page Header
├── Judul halaman
├── Deskripsi singkat
└── Tombol aksi utama

Content
├── Summary cards / filter / action bar
└── Table / Form / Detail / Report
```

### 7.1 Page Header

```html
<div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
  <div>
    <p class="text-sm text-textSecondary">Petugas / Transaksi</p>
    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-textPrimary">Gadai Baru</h1>
    <p class="mt-1 text-sm text-textSecondary">Input data nasabah, barang jaminan, dan nominal pinjaman.</p>
  </div>

  <button class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primaryDark">
    Tambah Data
  </button>
</div>
```

### 7.2 Content Card

```html
<section class="rounded-card border border-border bg-surface p-5 shadow-card">
  <!-- content -->
</section>
```

---

## 8. Layout Dashboard Per Role

### 8.1 Dashboard Owner

```txt
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Pinjaman Aktif │ Pendapatan │ Laba/Rugi   │ Jatuh Tempo  │
├────────────────────────────────────────────────────────────┤
│ Grafik tren transaksi / pendapatan                         │
├────────────────────────────────────────────────────────────┤
│ Tabel transaksi terbaru semua role                         │
└────────────────────────────────────────────────────────────┘
```

Komponen:

- Summary cards 4 kolom.
- Grafik pendapatan/transaksi jika tersedia.
- Tabel transaksi terbaru.
- Shortcut ke laporan laba/rugi dan pengaturan bunga/denda.

### 8.2 Dashboard Admin

```txt
┌──────────────┬──────────────┬──────────────┐
│ Total Barang │ Jatuh Tempo │ Siap Lelang  │
├────────────────────────────────────────────┤
│ Daftar barang perlu tindakan               │
├────────────────────────────────────────────┤
│ Tabel inventaris terbaru                   │
└────────────────────────────────────────────┘
```

Komponen:

- Summary cards 3 kolom.
- Alert barang melewati jatuh tempo.
- Tabel inventaris terbaru.
- Shortcut verifikasi siap jual/lelang.

### 8.3 Dashboard Petugas

```txt
┌──────────────┬──────────────┬──────────────┬──────────────┐
│ Gadai Baru  │ Pelunasan    │ Perpanjangan │ Beli Barang  │
├────────────────────────────────────────────────────────────┤
│ Riwayat transaksi hari ini                                │
└────────────────────────────────────────────────────────────┘
```

Komponen:

- Action cards transaksi cepat.
- Riwayat transaksi hari ini.
- Tombol cetak ulang nota.

---

## 9. Layout Form Transaksi

Form transaksi menggunakan layout 2 kolom di desktop dan 1 kolom di mobile.

```txt
┌──────────────────────────────┬──────────────────────────────┐
│ Data Nasabah                 │ Ringkasan Transaksi          │
│ Data Barang                  │ Nominal pinjaman             │
│ Detail Gadai                 │ Bunga                         │
│                              │ Jatuh tempo                   │
│                              │ Tombol simpan/cetak           │
└──────────────────────────────┴──────────────────────────────┘
```

### 9.1 Class Grid Form

```html
<div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_360px]">
  <div class="space-y-6">
    <section class="rounded-card border border-border bg-surface p-5 shadow-card">
      <!-- form utama -->
    </section>
  </div>

  <aside class="h-fit rounded-card border border-border bg-surface p-5 shadow-card lg:sticky lg:top-24">
    <!-- ringkasan transaksi -->
  </aside>
</div>
```

### 9.2 Input Field

```html
<label class="block">
  <span class="mb-1.5 block text-sm font-medium text-textPrimary">Nama Nasabah</span>
  <input class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10" />
</label>
```

### 9.3 Select Field

```html
<select class="w-full rounded-lg border border-border bg-white px-3 py-2.5 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10">
  <option>Pilih status</option>
</select>
```

### 9.4 Ringkasan Nominal

```html
<div class="space-y-3">
  <div class="flex items-center justify-between text-sm">
    <span class="text-textSecondary">Taksiran Barang</span>
    <span class="font-semibold tabular-nums text-textPrimary">Rp 1.500.000</span>
  </div>
  <div class="flex items-center justify-between text-sm">
    <span class="text-textSecondary">Bunga</span>
    <span class="font-semibold tabular-nums text-textPrimary">Rp 150.000</span>
  </div>
  <div class="border-t border-border pt-3">
    <div class="flex items-center justify-between">
      <span class="font-medium text-textPrimary">Total Tagihan</span>
      <span class="text-lg font-bold tabular-nums text-primary">Rp 1.650.000</span>
    </div>
  </div>
</div>
```

---

## 10. Layout Tabel

Tabel digunakan untuk transaksi, inventaris, laporan, dan riwayat.

```html
<div class="overflow-hidden rounded-card border border-border bg-surface shadow-card">
  <div class="border-b border-border p-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-base font-semibold text-textPrimary">Daftar Transaksi</h2>
      <div class="flex gap-2">
        <input class="rounded-lg border border-border px-3 py-2 text-sm" placeholder="Cari..." />
        <button class="rounded-lg border border-border px-3 py-2 text-sm hover:bg-appBg">Filter</button>
      </div>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-border text-sm">
      <thead class="bg-appBg text-left text-xs font-semibold uppercase tracking-wide text-textSecondary">
        <tr>
          <th class="px-4 py-3">No Transaksi</th>
          <th class="px-4 py-3">Nasabah</th>
          <th class="px-4 py-3">Status</th>
          <th class="px-4 py-3 text-right">Nominal</th>
          <th class="px-4 py-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-border bg-white">
        <tr class="hover:bg-appBg/70">
          <td class="px-4 py-3 font-medium text-textPrimary">GD-001</td>
          <td class="px-4 py-3 text-textSecondary">Nama Nasabah</td>
          <td class="px-4 py-3">Badge</td>
          <td class="px-4 py-3 text-right font-semibold tabular-nums">Rp 1.500.000</td>
          <td class="px-4 py-3 text-right">Detail</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
```

---

## 11. Komponen UI Utama

### 11.1 Button

Primary:

```html
<button class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark focus:outline-none focus:ring-2 focus:ring-primary/30">
  Simpan Transaksi
</button>
```

Secondary:

```html
<button class="rounded-lg border border-primary px-4 py-2.5 text-sm font-semibold text-primary hover:bg-primary/5">
  Batal
</button>
```

Danger:

```html
<button class="rounded-lg bg-danger px-4 py-2.5 text-sm font-semibold text-white hover:bg-danger/90">
  Hapus
</button>
```

### 11.2 Badge Status

| Status | Class |
|---|---|
| `Tersimpan` | `bg-info/10 text-info border-info/20` |
| `Diperpanjang` | `bg-warning/10 text-warning border-warning/20` |
| `Lunas` | `bg-success/10 text-success border-success/20` |
| `Siap Jual/Lelang` | `bg-secondary/10 text-secondary border-secondary/20` |
| `Terjual` | `bg-textSecondary/10 text-textSecondary border-border` |
| `Stok Etalase` | `bg-primary/10 text-primary border-primary/20` |


Template:

```html
<span class="inline-flex items-center rounded-full border px-2.5 py-1 text-xs font-semibold">
  Tersimpan
</span>
```

### 11.3 Summary Card

```html
<div class="rounded-card border border-border bg-surface p-5 shadow-card">
  <div class="flex items-start justify-between gap-4">
    <div>
      <p class="text-sm font-medium text-textSecondary">Total Pinjaman Aktif</p>
      <p class="mt-2 text-2xl font-bold tabular-nums text-textPrimary">Rp 12.500.000</p>
      <p class="mt-1 text-xs text-success">+8 transaksi aktif</p>
    </div>
    <div class="rounded-xl bg-primary/10 p-3 text-primary">
      Icon
    </div>
  </div>
</div>
```

### 11.4 Empty State

```html
<div class="rounded-card border border-dashed border-border bg-surface p-8 text-center">
  <p class="text-sm font-semibold text-textPrimary">Data belum tersedia</p>
  <p class="mt-1 text-sm text-textSecondary">Tambahkan transaksi baru untuk mulai mengisi data.</p>
</div>
```

### 11.5 Modal Konfirmasi

```html
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
  <div class="w-full max-w-md rounded-card bg-surface p-6 shadow-xl">
    <h2 class="text-lg font-semibold text-textPrimary">Konfirmasi Pelunasan</h2>
    <p class="mt-2 text-sm text-textSecondary">Pastikan nominal pembayaran sudah benar sebelum transaksi disimpan.</p>
    <div class="mt-6 flex justify-end gap-3">
      <button class="rounded-lg border border-border px-4 py-2 text-sm font-semibold hover:bg-appBg">Batal</button>
      <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primaryDark">Konfirmasi</button>
    </div>
  </div>
</div>
```

---

## 12. Desain Halaman Per Menu

### 12.1 Owner — Dashboard

- Header: `Dashboard Owner`.
- Summary cards: pinjaman aktif, pendapatan bulan ini, laba/rugi, barang jatuh tempo.
- Konten utama: grafik transaksi bulanan, transaksi terbaru.
- Aksi cepat: lihat laporan, atur bunga/denda.

### 12.2 Owner — Semua Transaksi

- Header: `Semua Transaksi`.
- Filter: tanggal, jenis transaksi, status, petugas.
- Tabel: no transaksi, tanggal, jenis, nasabah, petugas, nominal, status.
- Aksi: detail, cetak jika diperlukan.

### 12.3 Owner — Laporan

- Header: `Laporan`.
- Tab: harian, bulanan, laba/rugi.
- Filter rentang tanggal.
- Summary total.
- Tabel detail.
- Tombol cetak/export.

### 12.4 Owner — Pengaturan

- Header: `Pengaturan Bunga & Denda`.
- Form: persentase bunga, tenor default, denda keterlambatan.
- Card preview simulasi perhitungan.
- Tombol simpan.

### 12.5 Admin — Dashboard

- Header: `Dashboard Admin`.
- Summary cards: total barang gudang, mendekati jatuh tempo, siap jual/lelang.
- Tabel: barang perlu tindakan.
- Shortcut: inventaris, jatuh tempo, siap jual/lelang.

### 12.6 Admin — Inventaris

- Header: `Inventaris Gudang`.
- Filter: status, kategori, lokasi, asal transaksi.
- Tabel: kode barang, nama barang, asal, status, lokasi, tanggal masuk.
- Aksi: detail, update lokasi/status valid.

### 12.7 Admin — Jatuh Tempo

- Header: `Barang Jatuh Tempo`.
- Filter: mendekati jatuh tempo, lewat tempo.
- Tabel: no gadai, nasabah, barang, jatuh tempo, hari terlambat, status.
- Aksi: verifikasi kandidat lelang.

### 12.8 Admin — Siap Jual/Lelang

- Header: `Siap Jual/Lelang`.
- Tabel: barang, nilai pinjaman, taksiran, harga jual, status.
- Form modal: input harga jual/lelang.
- Aksi: tandai terjual.

### 12.9 Petugas — Dashboard

- Header: `Dashboard Petugas`.
- Action cards: gadai baru, pelunasan, perpanjangan, beli barang bekas.
- Tabel: riwayat transaksi hari ini.
- Aksi: cetak ulang nota.

### 12.10 Petugas — Gadai Baru

- Header: `Transaksi Gadai Baru`.
- Layout 2 kolom: form utama + ringkasan transaksi.
- Section form: data nasabah, data barang, nilai taksiran, pinjaman.
- Ringkasan: nominal pinjaman, bunga, tenor, jatuh tempo.
- Aksi: simpan transaksi, cetak nota.

### 12.11 Petugas — Pelunasan

- Header: `Pelunasan Gadai`.
- Search: no transaksi / nama nasabah.
- Detail transaksi: barang, tanggal gadai, jatuh tempo, nominal.
- Ringkasan tagihan: pokok, bunga, denda, total.
- Aksi: konfirmasi pelunasan, cetak nota pelunasan.

### 12.12 Petugas — Perpanjangan

- Header: `Perpanjangan Gadai`.
- Search transaksi aktif.
- Detail tagihan berjalan.
- Input tenor perpanjangan.
- Ringkasan jatuh tempo baru.
- Aksi: simpan perpanjangan, cetak nota.

### 12.13 Petugas — Beli Barang Bekas

- Header: `Beli Barang Bekas`.
- Form: data penjual, data barang, kondisi, harga beli.
- Ringkasan: total pembayaran.
- Aksi: simpan transaksi, cetak kuitansi.

### 12.14 Petugas — Riwayat Hari Ini

- Header: `Riwayat Transaksi Hari Ini`.
- Filter: jenis transaksi.
- Tabel: jam, jenis, nasabah/penjual, nominal, status.
- Aksi: detail, cetak ulang.

---

## 13. Responsiveness

### Desktop `lg:`

- Sidebar tampil fixed.
- Main content diberi padding kiri sesuai lebar sidebar.
- Form transaksi menggunakan 2 kolom.
- Summary cards 3–4 kolom.

### Tablet `md:`

- Sidebar dapat collapse.
- Summary cards 2 kolom.
- Search topbar tetap tampil.

### Mobile `< md`

- Sidebar menjadi drawer overlay.
- Topbar hanya menampilkan toggle, judul, notifikasi, profil.
- Search pindah ke dalam halaman/filter panel.
- Form menjadi 1 kolom.
- Tabel wajib `overflow-x-auto`.

---

## 14. Aturan Konsistensi

- Semua halaman memakai `App Shell` yang sama.
- Semua judul halaman memakai `Page Header`.
- Semua konten utama memakai `Content Card`.
- Semua tabel memakai header abu muda dan row hover.
- Semua nominal uang memakai `tabular-nums`, rata kanan di tabel, dan format `Rp 1.500.000`.
- Semua status wajib memakai badge warna + label teks.
- Semua tombol utama memakai warna `primary`.
- Semua menu aktif memakai warna `secondary`.
- Semua form memakai border `border`, radius `rounded-lg`, dan focus ring `primary/10`.
- Semua halaman transaksi memakai ringkasan nominal di sisi kanan pada desktop.

---

## 15. Aksesibilitas

- Kontras teks terhadap background minimal WCAG AA.
- Jangan gunakan warna saja untuk status; selalu sertakan label teks.
- Tombol icon harus punya `aria-label`.
- Input harus punya label.
- Modal harus bisa ditutup dengan tombol batal/close.
- Fokus keyboard harus terlihat.
- Ukuran klik minimal `40px` tinggi/lebar untuk tombol utama.

---

## 16. Ikonografi

Gunakan satu library ikon konsisten, disarankan **Lucide React**.

| Menu | Ikon Disarankan |
|---|---|
| Dashboard | `LayoutDashboard` |
| Transaksi Gadai | `BadgeDollarSign` / `CircleDollarSign` |
| Pelunasan | `CheckCircle2` |
| Perpanjangan | `RefreshCcw` |
| Beli Barang Bekas | `Smartphone` |
| Inventaris | `Package` |
| Jatuh Tempo | `Clock` |
| Siap Jual/Lelang | `Gavel` |
| Laporan | `BarChart3` |
| Pengaturan | `Settings` |
| User | `Users` |
| Cetak Nota | `ReceiptText` / `Printer` |

---

## 17. Ringkasan Implementasi Layout

Komponen layout yang perlu dibuat:

- `AppLayout`
- `Sidebar`
- `SidebarItem`
- `Topbar`
- `PageHeader`
- `SummaryCard`
- `ContentCard`
- `DataTable`
- `StatusBadge`
- `FormField`
- `TransactionSummary`
- `ConfirmModal`
- `PrintButton`

Struktur penggunaan:

```txt
AppLayout
├── Sidebar role-based
├── Topbar
└── Page
    ├── PageHeader
    ├── SummaryCard / FilterBar / ActionBar
    └── ContentCard
        ├── Form
        ├── Table
        └── Detail
```

Dengan struktur ini, semua menu Owner, Admin, dan Petugas punya desain konsisten, tetapi isi halaman tetap mengikuti kebutuhan tiap role.
