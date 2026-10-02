# Flow — Alur Sistem Informasi Manajemen Operasional Penggadaian
### Konter Buulolo Cell99

Dokumen ini menjelaskan alur sistem berdasarkan **role pengguna** (Petugas, Admin, Owner), **otomasi sistem**, dan pemetaan kebutuhan fungsional (FR-1.1 s/d FR-3.4) sesuai [prd.md](file:///c:/Users/rendani/Documents/JOKI/REG%20B/Fikarlin-001/sistem-penggadaian/docs/prd.md).

---

## 1. Kesimpulan Alur Sistem Usulan

Alur sistem dirancang untuk memberikan efisiensi operasional dengan pembagian tugas yang jelas:

- **Petugas (Front-Office / Kasir)**: Mengelola master nasabah (FR-1.1), transaksi gadai baru dengan auto-calculate & cetak SBG (FR-1.2, FR-1.5), pelunasan, denda & perpanjangan (FR-1.3), transaksi beli barang bekas (FR-1.4), serta modul penjualan lelang (FR-2.4).
- **Admin (Gudang & Pengelola Operasional)**: Kelola stok & lokasi rak gudang (FR-2.1), pantau jatuh tempo (FR-2.2), ubah/verifikasi status barang macet → `SIAP LELANG` (FR-2.3), serta verifikasi fisik rak lelang.
- **Monitoring Server (Auto-Detect)**: Otomasi tanggal server yang mendeteksi jatuh tempo dan memperbarui status barang wanprestasi menjadi `SIAP LELANG`.
- **Owner (Pemilik / Pengawas)**: Dashboard eksekutif (FR-3.1), atur parameter sistem bunga, denda, tenor (FR-3.2), kelola akun pengguna (FR-3.3), serta lihat & cetak laporan laba/rugi real-time (FR-3.4).

---

## 2. Alur Login & Routing Berdasarkan Role

```mermaid
flowchart TD
    A[User membuka aplikasi web] --> B[Halaman Login]
    B --> C[Input username dan password]
    C --> D{Kredensial valid?}
    D -- Tidak --> E[Tampilkan pesan login gagal]
    E --> B
    D -- Ya --> F{Cek role user}
    F -- Owner --> G[Dashboard Owner - Parameter, User & Laporan Real-Time]
    F -- Admin --> H[Dashboard Admin - Rak Gudang & Status Siap Lelang]
    F -- Petugas --> I[Dashboard Petugas - Operasional Front-Office & Scan SBG]
```

---

## 3. Flow Petugas — Input Transaksi Awal & Percabangan Alur (FR-1.1 - FR-1.5)

Petugas melayani pelanggan yang datang membawa KTP dan barang jaminan/jual.

```mermaid
flowchart TD
    A[Petugas Login] --> B[Pelanggan Datang Membawa KTP & Barang]
    B --> C[Input Master Data Nasabah KTP & Spesifikasi Barang]
    C --> D[Sistem Validasi & Hitung Otomatis Plafon, Bunga, & Tanggal Tempo]
    D --> E{Transaksi Disepakati?}
    E -- Tidak --> F[Batal Transaksi]
    E -- Ya --> G{Jenis Transaksi}

    G -- Beli Bekas --> H[Petugas Simpan Transaksi Beli]
    H --> I[Sistem Rekam Arus Kas Keluar]
    I --> J[Sistem Update Stok Etalase]
    J --> K[Cetak Nota Pembelian Bekas]

    G -- Gadai Baru --> L[Petugas Tekan Tombol Submit]
    L --> M[Sistem Simpan Status: TERSIMPAN]
    M --> N[Sistem Auto-Generate Nomor Tiket / Barcode]
    N --> O[Cetak Surat Bukti Gadai - SBG]
    O --> P[Sistem Kirim Notifikasi ke Admin]
    P --> Q[Admin Tempatkan Barang di Rak Gudang]
```

---

## 4. Flow Auto-Detect Server & Penanganan Barang Lelang (FR-2.2 - FR-2.4)

Monitoring otomatis berbasis tanggal server berjalan secara kontinu untuk mendeteksi jatuh tempo.

```mermaid
flowchart TD
    A[Monitoring Tanggal Server Auto-Detect] --> B{Apakah melewati jatuh tempo?}
    B -- Tidak --> C[Status Tetap TERSIMPAN di Rak Gudang]
    B -- Ya / Wanprestasi --> D[Sistem Ubah Status menjadi SIAP LELANG]
    D --> E[Sistem Kirim Alert Notifikasi ke Admin]
    E --> F[Admin Verifikasi & Pindahkan Barang ke Rak Lelang]
    F --> G[Barang Siap Dijual di Rak Lelang]
    G --> H[Petugas / Admin Catat Transaksi Penjualan Lelang]
    H --> I[Sistem Update Status Barang menjadi TERJUAL]
    I --> J[Pendapatan Masuk Rekap Transaksi & Laporan Laba/Rugi Real-Time]
```

---

## 5. Flow Pelanggan Datang Membayar (FR-1.3)

Ketika pelanggan datang membawa Surat Bukti Gadai (SBG) untuk pelunasan atau perpanjangan.

```mermaid
flowchart TD
    A[Pelanggan Datang Membawa SBG] --> B[Petugas Memindai / Scan Barcode SBG]
    B --> C[Sistem Cari Data Transaksi & Auto-Calculate Denda Keterlambatan]
    C --> D[Tampilkan Total Tagihan Pokok, Bunga, Denda]
    D --> E{Pilihan Pelanggan}

    E -- Tebus Lunas --> F[Input Pembayaran Pelunasan]
    F --> G[Sistem Update Status: LUNAS]
    G --> H[Cetak Struk Pelunasan]
    H --> I[Barang Diserahkan Kembali ke Pelanggan]

    E -- Perpanjangan --> J[Input Pembayaran Bunga & Denda]
    J --> K[Sistem Update Status: DIPERPANJANG & Update Tanggal Tempo Otomatis]
    K --> L[Cetak Nota Perpanjangan / SBG Baru]
    L --> M[Barang Tetap Tersimpan di Rak Gudang]
```

---

## 6. Flow Role Owner — Parameter & Laporan Terpusat (FR-3.1 - FR-3.4)

Owner dapat langsung mengakses web untuk mengatur parameter, mengelola akun, dan men-generate laporan.

```mermaid
flowchart TD
    A[Owner Login Web] --> B[Dashboard Ringkasan Eksekutif]
    B --> C{Pilih Akses Menu}
    C -- Monitoring Real-Time --> D[Lihat Piutang Aktif, Barang Gudang, Estimasi Bunga]
    C -- Atur Parameter --> E[Atur Persentase Bunga, Rate Denda, Tenor Baku]
    C -- Kelola Pengguna --> F[Kelola Akun Owner, Admin, Petugas]
    C -- Generate Laporan --> G[Lihat & Cetak Laporan Gadai, Lelang, Laba/Rugi Real-Time]
```

---

## 7. Definisi Status Barang Jaminan (State Machine)

```mermaid
stateDiagram-v2
    [*] --> TERSIMPAN: Submit Gadai Baru (Rak Gudang)
    [*] --> STOK_ETALASE: Transaksi Beli Bekas (Etalase)

    TERSIMPAN --> DIPERPANJANG: Bayar Bunga/Denda Perpanjangan
    DIPERPANJANG --> TERSIMPAN: Konfirmasi Tanggal Tempo Baru

    TERSIMPAN --> LUNAS: Tebus Lunas via Scan SBG
    DIPERPANJANG --> LUNAS: Tebus Lunas via Scan SBG

    TERSIMPAN --> SIAP_LELANG: Auto-Detect Server (Jatuh Tempo/Macet)
    DIPERPANJANG --> SIAP_LELANG: Auto-Detect Server (Jatuh Tempo/Macet)

    SIAP_LELANG --> TERJUAL: Catat Penjualan Lelang (Rak Lelang)

    LUNAS --> [*]
    TERJUAL --> [*]
    STOK_ETALASE --> TERJUAL: Dijual Langsung di Toko
```

Matriks Status Barang:

| Status | Deskripsi | Pemicu Perubahan |
|---|---|---|
| `TERSIMPAN` | Barang gadai baru telah diregistrasi dan ditempatkan di gudang | Petugas submit transaksi gadai baru |
| `DIPERPANJANG` | Tanggal jatuh tempo diperbarui setelah pembayaran bunga/denda perpanjangan | Petugas proses opsi perpanjangan |
| `LUNAS` | Barang telah ditebus penuh oleh nasabah | Petugas proses tebus lunas |
| `SIAP LELANG` | Barang wanprestasi/macet, menunggu verifikasi pemindahan ke rak lelang | Sistem auto-detect jatuh tempo terlampaui |
| `TERJUAL` | Barang lelang telah terjual ke pembeli | Petugas / Admin catat penjualan lelang |

---

## 8. Matriks Hak Akses Kebutuhan Fungsional (RBAC)

| Kode FR | Kebutuhan Fungsional | Petugas | Admin | Owner |
|---|---|:---:|:---:|:---:|
| **FR-1.1** | Input & kelola master data nasabah | **Ya** | - | - |
| **FR-1.2** | Catat transaksi gadai baru (taksiran, plafon, tenor) | **Ya** | - | - |
| **FR-1.3** | Catat perpanjangan, denda, & pelunasan gadai | **Ya** | - | - |
| **FR-1.4** | Catat transaksi beli barang bekas | **Ya** | - | - |
| **FR-1.5** | Cetak SBG, kuitansi pelunasan, & nota pembelian | **Ya** | - | - |
| **FR-2.1** | Kelola stok & lokasi rak gudang | - | **Ya** | - |
| **FR-2.2** | Pantau daftar kontrak mendekati & lewat jatuh tempo | - | **Ya** | - |
| **FR-2.3** | Ubah status barang macet → siap jual/lelang | - | **Ya** | - |
| **FR-2.4** | Catat transaksi penjualan barang lelang | **Ya** | **Ya** | - |
| **FR-3.1** | Dashboard eksekutif (piutang, stok, bunga) | - | - | **Ya** |
| **FR-3.2** | Atur parameter sistem (bunga, denda, tenor) | - | - | **Ya** |
| **FR-3.3** | Kelola akun pengguna sistem | - | - | **Ya** |
| **FR-3.4** | Lihat & cetak laporan transaksi, lelang, laba/rugi | - | - | **Ya** |
