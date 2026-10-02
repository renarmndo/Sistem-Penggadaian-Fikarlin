# 🏦 Sistem Penggadaian Fikarlin

Sistem informasi manajemen penggadaian berbasis web menggunakan Laravel 11.

## 📋 Fitur Utama

### 👤 Petugas Front-Office (FR-1)
- ✅ Master Data Nasabah
- ✅ Transaksi Gadai Baru dengan Auto-Generate SBG (Surat Bukti Gadai)
- ✅ Pelunasan & Perpanjangan (Scan Barcode SBG)
- ✅ Transaksi Beli Barang Bekas
- ✅ Print SBG & Nota Otomatis
- ✅ Buku Kas & Riwayat Transaksi Harian

### 📦 Admin Gudang (FR-2)
- ✅ Kelola Stok & Lokasi Rak Gudang
- ✅ Monitoring Kontrak Jatuh Tempo
- ✅ Update Status: Macet → Siap Lelang
- ✅ Modul Penjualan Lelang

### 👨‍💼 Owner/Pengawas (FR-3)
- ✅ Dashboard Real-Time (Piutang, Laba/Rugi, Transaksi)
- ✅ Parameter Sistem (Bunga, Biaya Admin, Logo)
- ✅ Kelola Akun Pengguna (Role Management)
- ✅ Laporan Transaksi & Lelang (Export/Print)

## 🛠️ Tech Stack

- **Framework:** Laravel 11.x
- **Database:** MySQL
- **Frontend:** Blade Templates + Tailwind CSS
- **Barcode:** Milon Barcode Generator
- **Authentication:** Laravel Breeze

## 📦 Installation

### 1. Clone Repository
```bash
git clone https://github.com/renarmndo/Sistem-Penggadaian-Fikarlin.git
cd Sistem-Penggadaian-Fikarlin
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Configuration
Edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_sistem_penggadaian
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migrate & Seed Database
```bash
php artisan migrate --seed
```

### 6. Build Assets
```bash
npm run build
```

### 7. Run Development Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## 👥 Default User Accounts

Setelah seeding, gunakan akun berikut untuk login:

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@fikarlin.com | password |
| Admin Gudang | admin@fikarlin.com | password |
| Petugas | petugas@fikarlin.com | password |

## 📁 Struktur Database

- **users** - User accounts (owner, admin, petugas)
- **app_settings** - System parameters (bunga, biaya admin, logo)
- **customers** - Master data nasabah
- **pawn_transactions** - Transaksi gadai
- **pawn_payments** - Pelunasan & perpanjangan
- **items** - Barang gadai & inventori
- **purchase_transactions** - Transaksi beli barang bekas
- **inventory_mutations** - Log mutasi stok gudang

## 🔒 Security Features

- ✅ Role-based Access Control (RBAC)
- ✅ Password Hashing (Bcrypt)
- ✅ CSRF Protection
- ✅ Session Management
- ✅ Input Validation & Sanitization
- ✅ Lazy Loading Prevention (N+1 Query Detection)

## 📊 Business Logic

- **Bunga Gadai:** Configurable (default 0.75% per 15 hari)
- **Biaya Admin:** Configurable (default 2% per transaksi)
- **Status Barang:**
  - Tersimpan (Rak Gudang)
  - Terjual (Barang bekas)
  - Macet (Overdue)
  - Siap Lelang
  - Terjual Lelang
- **Status Transaksi:**
  - Tersimpan (Aktif)
  - Diperpanjang
  - Lunas
  - Macet

## 📈 Performance Optimization

- ✅ Eager Loading untuk prevent N+1 queries
- ✅ Database Indexing
- ✅ Session optimization (12 jam lifetime)
- ✅ Query caching ready
- ✅ Route caching support

## 📄 Documentation

Dokumentasi lengkap tersedia di folder `/docs`:
- `prd.md` - Product Requirements Document
- `design.md` - System Design
- `flow.md` - Business Process Flow
- `task.md` - Development Task List
- `performance-fixes.md` - Performance Optimization Guide

## 🧪 Testing

Dokumentasi black box testing tersedia untuk:
- ✅ User Login
- ✅ Master Data Nasabah (11 test cases)
- ✅ Transaksi Gadai
- ✅ Pelunasan & Perpanjangan
- ✅ Modul Lelang

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📝 License

This project is proprietary software for Fikarlin Pawnshop.

## 👨‍💻 Developer

Developed by **Renardo** for Fikarlin Pawnshop Management System

---

⭐ **Star this repository** if you find it useful!
