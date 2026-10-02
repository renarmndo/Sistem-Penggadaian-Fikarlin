# Task Breakdown — Sistem Informasi Manajemen Operasional Penggadaian
### Konter Buulolo Cell99

Dokumen ini menjadi acuan progress development sistem berbasis **Laravel**, menggabungkan kebutuhan dari:

- `docs/prd.md` — kebutuhan produk dan scope sistem.
- `docs/flow.md` — alur kerja berbasis role: Owner, Admin, Petugas.
- `docs/design.md` — layout, sidebar, topbar, menu role, dan Tailwind CSS.

Framework utama:

- Backend: **Laravel**.
- Frontend: **Blade + Tailwind CSS**.
- Database: relational database melalui Laravel Migration dan Eloquent ORM.
- Autentikasi: Laravel authentication berbasis session.
- Hak akses: role-based access control untuk `Owner`, `Admin`, dan `Petugas`.

---

## 0. Prinsip Pengerjaan

- Sistem dibuat sebagai aplikasi web internal untuk Buulolo Cell99.
- Semua fitur mengikuti scope PRD.
- Semua alur mengikuti flow berbasis role.
- Semua halaman mengikuti design guideline Tailwind CSS.
- Setiap fitur dikerjakan dengan urutan: database → model → route → controller/service → view → validasi → testing.
- Fitur di luar scope seperti mobile app, payment gateway, e-commerce, dan logistik tidak dikerjakan.

---

## 1. Setup Project Laravel

### 1.1 Inisialisasi Project
- [x] Buat project Laravel.
- [x] Setup file `.env`.
- [x] Setup koneksi database.
- [x] Jalankan server lokal Laravel.
- [x] Setup struktur folder project.
- [x] Setup Git jika diperlukan.

### 1.2 Setup Frontend Tailwind CSS
- [x] Install dan konfigurasi Tailwind CSS untuk Laravel.
- [x] Tambahkan design token warna dari `docs/design.md` ke konfigurasi Tailwind.
- [x] Setup font utama `Inter` atau fallback system font.
- [x] Setup file CSS utama.
- [x] Pastikan build asset berjalan.

### 1.3 Setup Base Layout
- [x] Buat layout utama Blade `layouts/app.blade.php`.
- [x] Buat komponen `Sidebar`.
- [x] Buat komponen `Topbar`.
- [x] Buat komponen `PageHeader`.
- [x] Buat komponen `ContentCard`.
- [x] Buat layout responsive desktop/tablet/mobile.
- [x] Buat sidebar expanded/collapsed.
- [x] Buat mobile sidebar drawer.
- [x] Buat menu aktif berdasarkan route.

---

## 2. Database Design & Migration

### 2.1 Tabel User dan Role
- [x] Buat migration `users`.
- [x] Tambahkan kolom role: `owner`, `admin`, `petugas`.
- [x] Tambahkan kolom status aktif/nonaktif jika diperlukan.
- [x] Buat seeder user demo untuk tiap role.
- [x] Pastikan password menggunakan hashing Laravel.

### 2.2 Tabel Nasabah / Pelanggan
- [x] Buat migration `customers`.
- [x] Field minimal: nama, **nomor KTP / identitas (wajib)**, nomor HP, alamat.
- [x] Buat constraint data unik untuk nomor KTP.
- [x] Buat index untuk pencarian nomor KTP / nama.

### 2.3 Tabel Barang & Lokasi Inventaris
- [x] Buat migration `items`.
- [x] Field minimal: kode barang, nama barang, kategori, merk/tipe, kondisi, foto.
- [x] Status barang: `tersimpan` (Rak Gudang), `siap_lelang` (Macet/Siap Lelang), `terjual` (Penjualan), `lunas` (Ditebus), `stok_etalase` (Beli Bekas).
- [x] Field lokasi fisik: `rak_gudang`, `rak_lelang`, `etalase`.
- [x] Field asal barang: `gadai` atau `beli_barang_bekas`.
- [x] Buat relasi ke transaksi asal.

### 2.4 Tabel Transaksi Gadai Baru & SBG Barcode
- [x] Buat migration `pawn_transactions`.
- [x] Relasi ke `customers`, `items`, dan `users` sebagai petugas.
- [x] Field barcode & tiket: `ticket_number` (auto-generate), `barcode_hash`.
- [x] Field nominal: taksiran, **nilai plafon pinjaman**, persentase bunga, denda, total tagihan.
- [x] Field tanggal: tanggal gadai, **tanggal jatuh tempo (otomatis)**, tanggal lunas jika ada.
- [x] Field status transaksi: `tersimpan`, `lunas`, `diperpanjang`, `siap_lelang`, `terjual`.
- [x] Simpan snapshot aturan bunga/tenor/denda saat transaksi dibuat.

### 2.5 Tabel Pelunasan dan Perpanjangan
- [x] Buat migration `pawn_payments`.
- [x] Relasi ke `pawn_transactions`.
- [x] Tipe pembayaran: `pelunasan` (`LUNAS`) atau `perpanjangan`.
- [x] Simpan pokok, bunga, denda keterlambatan (auto-calculate server), total bayar.
- [x] Simpan jatuh tempo lama dan jatuh tempo baru untuk perpanjangan.
- [x] Relasi ke `users` sebagai petugas.

### 2.6 Tabel Transaksi Beli Barang Bekas & Arus Kas
- [x] Buat migration `purchase_transactions`.
- [x] Relasi ke `customers` (penjual dengan KTP).
- [x] Relasi ke `items` (otomatis lokasi `etalase` & status `stok_etalase`).
- [x] Relasi ke `users` sebagai petugas.
- [x] Field harga beli (recorded as cash outflow / arus kas keluar), tanggal pembelian, catatan kondisi.

### 2.7 Tabel Inventaris, Mutasi, dan Notifikasi Admin
- [x] Buat migration `inventory_mutations`.
- [x] Relasi ke `items`.
- [x] Tipe mutasi: `masuk_rak_gudang`, `masuk_etalase`, `pindah_rak_lelang`, `keluar_ditebus`, `keluar_terjual`.
- [x] Simpan lokasi lama dan lokasi baru.
- [x] Relasi ke `users` sebagai pelaku aksi (Petugas / Admin / System Auto).

### 2.8 Tabel Pengaturan Sistem
- [x] Buat migration `app_settings`.
- [x] Simpan persentase bunga default & formula kalkulasi plafon.
- [x] Simpan tenor default (hari/bulan).
- [x] Simpan aturan denda keterlambatan per hari.
- [x] Buat seeder pengaturan awal.

---

## 3. Model, Relationship, dan Business Logic

### 3.1 Eloquent Model
- [x] Buat model `User`.
- [x] Buat model `Customer`.
- [x] Buat model `Item`.
- [x] Buat model `PawnTransaction`.
- [x] Buat model `PawnPayment`.
- [x] Buat model `PurchaseTransaction`.
- [x] Buat model `InventoryMutation`.
- [x] Buat model `AppSetting`.

### 3.2 Service Class Logika Bisnis & Otomasi Server
- [x] Buat service kalkulasi transaksi gadai (taksiran -> plafon -> bunga -> tanggal tempo otomatis).
- [x] Buat service generator Barcode & Nomor Tiket SBG.
- [x] Buat **Laravel Artisan Command / Scheduler** (`pawn:auto-detect-overdue`) untuk monitoring tanggal server dan auto-update status ke `siap_lelang`.
- [x] Buat service kalkulasi denda keterlambatan via scan Barcode SBG.
- [x] Buat service mutasi inventaris (Rak Gudang, Rak Lelang, Stok Etalase).
- [x] Buat service pencatatan arus kas keluar (Beli Bekas) & kas masuk (Gadai/Pelunasan/Lelang).
- [x] Buat service laporan laba/rugi real-time terpusat untuk Owner.

---

## 4. Authentication & Role Access

### 4.1 Autentikasi
- [x] Buat halaman login.
- [x] Buat proses login dengan session Laravel.
- [x] Buat proses logout.
- [x] Redirect user ke dashboard sesuai role.
- [x] Batasi akses halaman untuk user belum login.

### 4.2 Middleware Role
- [x] Buat middleware role access.
- [x] Batasi route Owner hanya untuk role `owner`.
- [x] Batasi route Admin hanya untuk role `admin`.
- [x] Batasi route Petugas hanya untuk role `petugas`.
- [x] Buat fallback unauthorized page atau redirect.

### 4.3 Menu Berbasis Role
- [x] Tampilkan sidebar Owner untuk role `owner`.
- [x] Tampilkan sidebar Admin untuk role `admin`.
- [x] Tampilkan sidebar Petugas untuk role `petugas`.
- [x] Sembunyikan menu yang bukan hak akses role.
- [x] Pastikan URL langsung tetap terlindungi middleware.

---

## 5. Komponen UI Laravel Blade

### 5.1 Komponen Layout
- [x] Buat Blade component `x-app-layout`.
- [x] Buat Blade component `x-sidebar`.
- [x] Buat Blade component `x-sidebar-item`.
- [x] Buat Blade component `x-topbar`.
- [x] Buat Blade component `x-page-header`.
- [x] Buat Blade component `x-content-card`.

### 5.2 Komponen Data
- [x] Buat Blade component `x-summary-card`.
- [x] Buat Blade component `x-status-badge`.
- [x] Buat Blade component `x-data-table` atau pattern tabel reusable.
- [x] Buat Blade component `x-filter-bar`.
- [x] Buat Blade component `x-empty-state`.

### 5.3 Komponen Form
- [x] Buat Blade component `x-input`.
- [x] Buat Blade component `x-select`.
- [x] Buat Blade component `x-textarea`.
- [x] Buat Blade component `x-button`.
- [x] Buat Blade component `x-transaction-summary`.
- [x] Buat Blade component `x-confirm-modal` jika memakai Alpine.js.

### 5.4 Interaksi UI
- [x] Implementasi sidebar toggle.
- [x] Implementasi mobile drawer.
- [x] Implementasi dropdown profile.
- [x] Implementasi notifikasi sederhana untuk barang jatuh tempo.
- [x] Implementasi modal konfirmasi pelunasan/perpanjangan.

---

## 6. Route dan Controller Struktur Laravel

### 6.1 Route Group
- [x] Buat route group `auth`.
- [x] Buat route group `owner` dengan middleware role owner.
- [x] Buat route group `admin` dengan middleware role admin.
- [x] Buat route group `petugas` dengan middleware role petugas.
- [x] Gunakan route name konsisten.

### 6.2 Controller Owner (FR-3.1 - FR-3.4)
- [x] Buat `Owner/DashboardController` (Dashboard Eksekutif — FR-3.1).
- [x] Buat `Owner/SettingController` (Pengaturan Bunga, Denda, Tenor — FR-3.2).
- [x] Buat `Owner/UserController` (Kelola Akun Pengguna — FR-3.3).
- [x] Buat `Owner/ReportController` (Laporan Transaksi, Lelang, Laba/Rugi — FR-3.4).

### 6.3 Controller Admin (FR-2.1 - FR-2.4)
- [x] Buat `Admin/DashboardController` (Ringkasan Stok & Alert — FR-2.2).
- [x] Buat `Admin/WarehouseController` (Stok & Lokasi Rak Gudang — FR-2.1).
- [x] Buat `Admin/DueDateController` (Monitoring Jatuh Tempo — FR-2.2).
- [x] Buat `Admin/AuctionController` (Verifikasi Status Siap Lelang — FR-2.3).

### 6.4 Controller Petugas (FR-1.1 - FR-1.5, FR-2.4)
- [x] Buat `Petugas/DashboardController` (Shortcut & Operasional Front-Office).
- [x] Buat `Petugas/CustomerController` (Master Data Nasabah — FR-1.1).
- [x] Buat `Petugas/PawnTransactionController` (Gadai Baru & Cetak SBG — FR-1.2, FR-1.5).
- [x] Buat `Petugas/PawnPaymentController` (Pelunasan, Denda, Perpanjangan — FR-1.3, FR-1.5).
- [x] Buat `Petugas/PurchaseTransactionController` (Beli Barang Bekas & Nota — FR-1.4, FR-1.5).
- [x] Buat `Petugas/AuctionSaleController` (Input Penjualan Lelang Front-Office — FR-2.4).

---

## 7. Modul Owner (FR-3.1 - FR-3.4)

### 7.1 Dashboard Eksekutif (FR-3.1)
- [x] Tampilkan total piutang aktif / plafon berjalan.
- [x] Tampilkan total barang di gudang.
- [x] Tampilkan estimasi bunga masuk.
- [x] Tampilkan shortcut laporan dan pengaturan parameter.

### 7.2 Pengaturan Parameter Sistem (FR-3.2)
- [x] Buat form pengaturan persentase bunga.
- [x] Buat form pengaturan rate/tarif denda keterlambatan.
- [x] Buat form pengaturan tenor baku (durasi default jatuh tempo).
- [x] Simpan konfigurasi ke database (`app_settings`).

### 7.3 Kelola Akun Pengguna / User Management (FR-3.3)
- [x] Buat CRUD kelola akun pengguna (Owner, Admin, Petugas).
- [x] Fitur tambah user baru, ubah role, reset password, dan status aktif/nonaktif.

### 7.4 Laporan Terpusat & Real-Time (FR-3.4)
- [x] Buat laporan transaksi gadai.
- [x] Buat laporan inventaris barang lelang.
- [x] Buat laporan laba/rugi periode terpilih.
- [x] Buat fitur cetak / export PDF laporan.

---

## 8. Modul Admin (FR-2.1 - FR-2.4)

### 8.1 Kelola Stok & Lokasi Rak Gudang (FR-2.1)
- [x] Tampilkan daftar barang jaminan aktif di gudang.
- [x] Input & update lokasi rak/posisi barang fisik gudang.
- [x] Catat mutasi posisi rak gudang.

### 8.2 Monitoring Jatuh Tempo (FR-2.2)
- [x] Pantau daftar kontrak gadai yang mendekati jatuh tempo.
- [x] Pantau daftar kontrak gadai yang telah melewati jatuh tempo.

### 8.3 Verifikasi & Ubah Status Barang Macet (FR-2.3)
- [x] Terima alert auto-detect server barang macet.
- [x] Ubah & verifikasi status barang macet/jatuh tempo menjadi status `siap_lelang`.
- [x] Verifikasi pemindahan barang secara fisik dari rak gudang ke rak lelang.

### 8.4 Penjualan Barang Lelang (FR-2.4)
- [x] Tampilkan daftar barang status `siap_lelang`.
- [x] Form catat transaksi penjualan barang lelang kepada pembeli.
- [x] Update status barang menjadi `terjual`.

---

## 9. Modul Petugas (FR-1.1 - FR-1.5)

### 9.1 Master Data Nasabah (FR-1.1)
- [x] Buat form input dan kelola master data nasabah/pelanggan (termasuk nomor KTP).
- [x] Pencarian nasabah via nomor KTP / nama.

### 9.2 Transaksi Gadai Baru (FR-1.2, FR-1.5)
- [x] Input data nasabah dan spesifikasi barang.
- [x] Hitung taksiran, plafon pinjaman, bunga, dan tanggal jatuh tempo secara otomatis.
- [x] Submit transaksi gadai baru (status `tersimpan`).
- [x] Generate nomor tiket/barcode unik.
- [x] Cetak Surat Bukti Gadai (SBG).

### 9.3 Perpanjangan, Denda, & Pelunasan (FR-1.3, FR-1.5)
- [x] Memindai / input Barcode SBG untuk penarikan data transaksi instan.
- [x] Hitung denda keterlambatan secara otomatis.
- [x] Opsi **Tebus Lunas**: Update status transaksi/barang menjadi `lunas`, cetak struk pelunasan.
- [x] Opsi **Perpanjangan**: Update status menjadi `diperpanjang`, perbarui tanggal jatuh tempo otomatis, cetak nota perpanjangan.

### 9.4 Transaksi Beli Barang Bekas (FR-1.4, FR-1.5)
- [x] Form transaksi pembelian barang elektronik bekas dari nasabah.
- [x] Rekam arus kas keluar secara otomatis.
- [x] Update stok etalase toko secara otomatis.
- [x] Cetak nota pembelian barang bekas.

### 9.5 Penjualan Barang Lelang Front-Office (FR-2.4)
- [x] Form input transaksi penjualan barang lelang saat pembeli datang ke front-office.
- [x] Update status barang menjadi `terjual`.

---

## 10. Cetak Nota dan Laporan

### 10.1 Nota Transaksi
- [x] Buat template nota gadai.
- [x] Buat template nota pelunasan.
- [x] Buat template nota perpanjangan.
- [x] Buat template kuitansi pembelian barang bekas.
- [x] Pastikan format nominal memakai `Rp`.
- [x] Tambahkan informasi tanggal, petugas, nasabah/penjual, dan detail barang.
- [x] Buat tampilan print-friendly CSS.

### 10.2 Export/Cetak Laporan
- [x] Buat tampilan cetak laporan harian.
- [x] Buat tampilan cetak laporan bulanan.
- [x] Buat tampilan cetak laporan laba/rugi.
- [x] Buat tampilan cetak laporan inventaris.
- [x] Tambahkan filter tanggal pada laporan.

---

## 11. Validasi, Keamanan, dan Error Handling

### 11.1 Form Request Validation
- [x] Buat Form Request untuk login jika diperlukan.
- [x] Buat Form Request transaksi gadai.
- [x] Buat Form Request pelunasan.
- [x] Buat Form Request perpanjangan.
- [x] Buat Form Request pembelian barang bekas.
- [x] Buat Form Request pengaturan bunga/denda.

### 11.2 Validasi Data Bisnis
- [x] Cegah transaksi gadai tanpa nasabah.
- [x] Cegah transaksi gadai tanpa barang.
- [x] Cegah pelunasan ganda.
- [x] Cegah perpanjangan untuk transaksi tidak aktif.
- [x] Cegah barang dilelang tanpa verifikasi Admin.
- [x] Cegah status barang lompat tidak sesuai flow.

### 11.3 Keamanan
- [x] Pastikan semua halaman internal wajib login.
- [x] Pastikan role access dicek pada route dan controller.
- [x] Pastikan password selalu hashed.
- [x] Aktifkan CSRF protection Laravel pada form.
- [x] Validasi upload foto barang jika fitur foto dipakai.
- [x] Batasi tipe dan ukuran file upload.

### 11.4 UX Error Handling
- [x] Tampilkan error validasi dekat field input.
- [x] Tampilkan flash message sukses/gagal.
- [x] Tampilkan empty state jika data kosong.
- [x] Tampilkan confirm modal untuk aksi penting.
- [x] Pastikan format uang konsisten.

---

## 12. Testing Laravel

### 12.1 Unit Test Business Logic
- [x] Test hitung bunga.
- [x] Test hitung denda.
- [x] Test hitung total pelunasan.
- [x] Test hitung jatuh tempo baru perpanjangan.
- [x] Test laporan laba/rugi.

### 12.2 Feature Test Role Access
- [x] Test Owner bisa akses dashboard Owner.
- [x] Test Admin tidak bisa akses pengaturan Owner.
- [x] Test Petugas tidak bisa akses halaman Admin.
- [x] Test user belum login diarahkan ke login.

### 12.3 Feature Test Modul Utama
- [x] Test transaksi gadai baru berhasil disimpan.
- [x] Test status barang menjadi `tersimpan` setelah gadai.
- [x] Test pelunasan mengubah status barang menjadi `ditebus`.
- [x] Test perpanjangan mengubah jatuh tempo.
- [x] Test pembelian barang bekas mencatat barang masuk gudang.
- [x] Test Admin bisa verifikasi barang siap jual/lelang.
- [x] Test laporan menampilkan data sesuai filter.

### 12.4 Black-Box Testing Manual
- [x] Uji login Owner, Admin, Petugas.
- [x] Uji sidebar menu sesuai role.
- [x] Uji dashboard tiap role.
- [x] Uji transaksi gadai baru.
- [x] Uji pelunasan gadai.
- [x] Uji perpanjangan gadai.
- [x] Uji transaksi beli barang bekas.
- [x] Uji inventaris gudang.
- [x] Uji barang jatuh tempo.
- [x] Uji siap jual/lelang.
- [x] Uji laporan harian, bulanan, laba/rugi.
- [x] Uji cetak nota/kuitansi.

---

## 13. User Acceptance Test Per Role

### 13.1 UAT Owner (FR-3.1 - FR-3.4)
- [x] Validasi Dashboard Eksekutif (FR-3.1: piutang aktif, barang gudang, estimasi bunga).
- [x] Validasi Pengaturan Parameter Sistem (FR-3.2: persentase bunga, rate denda, tenor baku).
- [x] Validasi Kelola Akun Pengguna / User Management (FR-3.3: kelola user & role).
- [x] Validasi Laporan Transaksi, Lelang, & Laba/Rugi Real-Time (FR-3.4).

### 13.2 UAT Admin (FR-2.1 - FR-2.4)
- [x] Validasi Kelola Stok & Lokasi Rak Gudang (FR-2.1).
- [x] Validasi Monitoring Kontrak Jatuh Tempo (FR-2.2).
- [x] Validasi Ubah Status Barang Macet → Siap Jual/Lelang (FR-2.3).
- [x] Validasi Transaksi Penjualan Barang Lelang (FR-2.4).

### 13.3 UAT Petugas (FR-1.1 - FR-1.5)
- [x] Validasi Input & Kelola Master Data Nasabah (FR-1.1).
- [x] Validasi Transaksi Gadai Baru, Auto-Plafon & Cetak SBG (FR-1.2, FR-1.5).
- [x] Validasi Perpanjangan (`DIPERPANJANG`), Denda, & Pelunasan (`LUNAS`) via Scan Barcode SBG (FR-1.3, FR-1.5).
- [x] Validasi Transaksi Beli Barang Bekas, Kas Keluar, & Stok Etalase (FR-1.4, FR-1.5).
- [x] Validasi Cetak Document (SBG, Struk Pelunasan, Nota Pembelian — FR-1.5).

---

## 14. Deployment Internal

### 14.1 Persiapan Deploy
- [x] Siapkan environment production/internal.
- [x] Set `.env` production.
- [x] Jalankan migration production.
- [x] Jalankan seeder role/user awal.
- [x] Build asset Tailwind.
- [x] Set permission storage/cache Laravel.
- [x] Pastikan queue/scheduler jika dipakai berjalan.

### 14.2 Data Awal
- [x] Input user Owner.
- [x] Input user Admin.
- [x] Input user Petugas.
- [x] Input pengaturan bunga, tenor, dan denda awal.
- [x] Input kategori barang awal.

### 14.3 Dokumentasi Pengguna
- [x] Buat panduan Owner.
- [x] Buat panduan Admin.
- [x] Buat panduan Petugas.
- [x] Buat panduan cetak nota/laporan.
- [x] Buat panduan backup manual jika diperlukan.

---

## 15. Prioritas Development MVP Laravel

Urutan kerja minimum agar sistem cepat bisa digunakan:

1. [x] Setup Laravel, database, dan Tailwind CSS.
2. [x] Buat layout utama: sidebar toggle, topbar, page header, content card.
3. [x] Buat authentication dan role middleware.
4. [x] Buat migration/model utama: users, customers, items, pawn_transactions, pawn_payments, purchase_transactions, inventory_mutations, app_settings.
5. [x] Buat dashboard sederhana per role.
6. [x] Buat modul data nasabah dan barang.
7. [x] Buat transaksi gadai baru.
8. [x] Buat cetak nota gadai.
9. [x] Buat pelunasan gadai.
10. [x] Buat perpanjangan gadai.
11. [x] Buat transaksi beli barang bekas.
12. [x] Buat inventaris gudang.
13. [x] Buat barang jatuh tempo dan verifikasi siap jual/lelang.
14. [x] Buat laporan harian dan bulanan.
15. [x] Buat laporan laba/rugi.
16. [x] Buat testing fitur utama.
17. [x] Buat UAT per role.
18. [x] Deploy internal.

---

## 16. Backlog Pengembangan Lanjutan

Fitur berikut tidak masuk scope skripsi/MVP, tetapi dapat dikembangkan setelah sistem inti stabil:

- [ ] Payment gateway.
- [ ] Aplikasi mobile Android/iOS.
- [ ] Integrasi e-commerce.
- [ ] Integrasi logistik.
- [ ] Multi-cabang.
- [ ] Backup otomatis terjadwal.
- [ ] Audit log aktivitas pengguna.
- [ ] Export Excel/PDF lanjutan.
- [ ] Notifikasi WhatsApp/SMS jatuh tempo.
