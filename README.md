# Albert Workshop - Sistem Informasi Servis Komputer

Sistem informasi berbasis web untuk membantu pengelolaan proses servis dan perbaikan komputer di **Albert Workshop**.  
Aplikasi ini dibuat menggunakan **PHP Native**, **MySQL/MariaDB**, **HTML**, **CSS**, dan **JavaScript**.

Sistem memiliki dua jenis pengguna internal, yaitu **Admin** dan **Teknisi**, serta halaman publik untuk pelanggan agar dapat mengecek status servis menggunakan **kode servis** dan **nomor HP**.

---

## Fitur Utama

### Admin

Admin dapat:

- Login ke sistem.
- Melihat dashboard.
- Mengelola data pelanggan.
- Mengarsipkan dan mengaktifkan kembali pelanggan.
- Mengelola perangkat pelanggan.
- Mengelola data teknisi.
- Mengelola data sparepart.
- Mengelola stok dan harga sparepart.
- Membuat data servis baru.
- Menentukan perangkat dan teknisi yang menangani servis.
- Mengedit data servis.
- Melihat detail servis.
- Menambahkan dan menghapus sparepart pada servis.
- Melihat perhitungan biaya jasa, sparepart, dan total servis.
- Menghapus servis dengan pengembalian stok sparepart.
- Melihat dokumentasi servis.
- Mengarsipkan pelanggan setelah proses servis selesai.
- Mengelola akun Admin dan Teknisi.

### Teknisi

Teknisi dapat:

- Login menggunakan akun Teknisi.
- Melihat servis yang ditugaskan kepadanya.
- Melihat detail perangkat dan pelanggan.
- Mengisi atau memperbarui diagnosa.
- Mengubah status pengerjaan servis.
- Menambahkan sparepart yang digunakan.
- Menghapus sparepart dari servis dengan pengembalian stok.
- Mengunggah dokumentasi servis.
- Menghapus dokumentasi miliknya.
- Menentukan apakah dokumentasi dapat dilihat pelanggan.

### Pelanggan

Pelanggan tidak perlu login. Pelanggan dapat membuka halaman **Cek Status Servis** dan memasukkan:

- Kode servis.
- Nomor HP.

Informasi yang dapat dilihat:

- Status servis.
- Data perangkat.
- Teknisi.
- Keluhan.
- Diagnosa.
- Sparepart yang digunakan.
- Biaya jasa.
- Biaya sparepart.
- Total biaya servis.
- Dokumentasi yang diizinkan untuk ditampilkan kepada pelanggan.

---

## Status Servis

Status pengerjaan yang tersedia:

1. Menunggu Pemeriksaan
2. Sedang Diperiksa
3. Menunggu Sparepart
4. Sedang Diperbaiki
5. Pengujian
6. Selesai
7. Sudah Diambil

---

## Teknologi yang Digunakan

- PHP Native
- MySQL / MariaDB
- MySQLi
- HTML5
- CSS3
- JavaScript
- Laragon
- phpMyAdmin

---

## Database

Database yang digunakan:

```text
db_servis_komputer
```

### Tabel

```text
pelanggan
perangkat
teknisi
sparepart
servis
detail_servis
users
dokumentasi_servis
```

### Relasi Utama

```text
pelanggan
   │
   └── perangkat
          │
          └── servis
                 │
                 ├── detail_servis ─── sparepart
                 │
                 └── dokumentasi_servis

teknisi
   ├── servis
   ├── users
   └── dokumentasi_servis

detail_servis
   └── dokumentasi_servis
```

Relasi yang digunakan antara lain:

- `pelanggan.id_pelanggan` → `perangkat.id_pelanggan`
- `perangkat.id_perangkat` → `servis.id_perangkat`
- `teknisi.id_teknisi` → `servis.id_teknisi`
- `servis.id_servis` → `detail_servis.id_servis`
- `sparepart.id_sparepart` → `detail_servis.id_sparepart`
- `teknisi.id_teknisi` → `users.id_teknisi`
- `servis.id_servis` → `dokumentasi_servis.id_servis`
- `teknisi.id_teknisi` → `dokumentasi_servis.id_teknisi`
- `detail_servis.id_detail` → `dokumentasi_servis.id_detail`

---

## Struktur Folder

```text
servis-komputer/
│
├── index.php
├── login.php
├── logout.php
├── cek-servis.php
│
├── config/
│   ├── koneksi.php
│   └── auth.php
│
├── pelanggan/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   ├── proses.php
│   └── arsip.php
│
├── perangkat/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── proses.php
│
├── teknisi/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── proses.php
│
├── sparepart/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── proses.php
│
├── servis/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── detail.php
│   ├── hapus.php
│   └── proses.php
│
├── dokumentasi/
│   ├── upload.php
│   ├── proses_upload.php
│   └── hapus.php
│
├── akun/
│   ├── index.php
│   ├── tambah.php
│   ├── edit.php
│   ├── hapus.php
│   └── proses.php
│
├── template/
│   ├── header.php
│   ├── sidebar.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
│
└── uploads/
    └── servis/
        ├── kerusakan/
        ├── penggantian/
        └── sparepart/
```

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/NAMA-REPOSITORY.git
```

Atau download repository dalam bentuk ZIP.

### 2. Pindahkan Project

Jika menggunakan Laragon, letakkan folder project di:

```text
C:\laragon\www\servis-komputer
```

### 3. Buat Database

Buka phpMyAdmin lalu buat database:

```text
db_servis_komputer
```

Kemudian import file SQL database project jika tersedia.

### 4. Konfigurasi Database

Edit:

```text
config/koneksi.php
```

Contoh konfigurasi:

```php
<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_servis_komputer";

$koneksi = mysqli_connect(
    $host,
    $user,
    $pass,
    $db
);

if (!$koneksi) {
    die(
        "Koneksi database gagal: "
        . mysqli_connect_error()
    );
}

mysqli_set_charset(
    $koneksi,
    "utf8mb4"
);
```

### 5. Jalankan Laragon

Aktifkan:

```text
Apache
MySQL
```

Kemudian buka:

```text
http://localhost/servis-komputer/
```

---

## Sistem Login

Sistem memiliki dua role:

```text
admin
teknisi
```

Password pengguna disimpan menggunakan:

```php
password_hash()
```

dan diverifikasi menggunakan:

```php
password_verify()
```

Hak akses halaman diatur melalui:

```text
config/auth.php
```

---

## Sistem Sparepart

Saat sparepart ditambahkan ke sebuah servis:

```text
stok sparepart
      ↓
dikurangi sesuai jumlah penggunaan
```

Jika sparepart dihapus dari detail servis:

```text
stok dikembalikan
```

Jika seluruh servis dihapus:

```text
stok seluruh sparepart yang digunakan
akan dikembalikan terlebih dahulu
```

Hal ini menjaga agar data stok tetap konsisten.

---

## Dokumentasi Servis

Teknisi dapat mengunggah foto dokumentasi dengan jenis:

```text
Kerusakan
Penggantian
Sparepart
```

Format yang didukung:

```text
JPG
JPEG
PNG
WEBP
```

Ukuran maksimal:

```text
5 MB
```

Dokumentasi dapat diatur menjadi **Publik** atau **Internal**. Dokumentasi publik dapat dilihat pelanggan melalui halaman cek servis.

---

## Arsip Pelanggan

Pelanggan tidak langsung dihapus apabila memiliki histori servis.

Data pelanggan dapat diarsipkan dengan:

```text
aktif = 0
```

Pelanggan hanya dapat diarsipkan apabila tidak memiliki servis yang belum berstatus:

```text
Sudah Diambil
```

Dengan cara ini histori perangkat dan servis pelanggan tetap tersimpan.

---

## Tampilan

Interface menggunakan konsep:

```text
Dark Technical / Workshop Dashboard
```

Karakteristik:

- Dark graphite background.
- Cyan accent.
- Sidebar navigation.
- Responsive layout.
- Status badge.
- Dashboard metrics.
- Responsive tables.
- Form dua kolom.
- Dokumentasi berbentuk gallery/card.
- Halaman tracking pelanggan tanpa sidebar.

---

## Alur Sistem

```text
Pelanggan
    ↓
Perangkat
    ↓
Servis
    ↓
Teknisi melakukan pemeriksaan
    ↓
Diagnosa
    ↓
Penggunaan Sparepart
    ↓
Dokumentasi
    ↓
Pengujian
    ↓
Selesai
    ↓
Sudah Diambil
    ↓
Pelanggan dapat diarsipkan
```

---

## Tujuan Project

Project ini dibuat sebagai sistem informasi untuk membantu proses administrasi dan pencatatan servis komputer, mulai dari penerimaan perangkat hingga penyelesaian servis.

Project juga digunakan sebagai latihan implementasi:

- CRUD.
- Relasi database.
- Foreign key.
- Authentication.
- Authorization berdasarkan role.
- Prepared statement.
- Password hashing.
- File upload.
- Pengelolaan stok.
- Transaction database.
- Responsive web interface.
- Sistem tracking pelanggan.

---

## Keamanan yang Digunakan

Beberapa penerapan keamanan dalam project:

- Password hashing.
- Prepared statement pada proses penting.
- Role-based access control.
- Validasi ID.
- Pembatasan akses servis berdasarkan teknisi.
- Validasi MIME file upload.
- Pembatasan ukuran upload.
- Escape output dengan `htmlspecialchars()`.
- Verifikasi kode servis dan nomor HP pada halaman pelanggan.

> Project ini merupakan project pembelajaran. Untuk penggunaan production masih diperlukan audit keamanan dan konfigurasi server tambahan.

---

## Pengembang

**Albert Dewa Ananta**

Project:

**Albert Workshop - Sistem Informasi Servis Komputer**

---

## License

Project ini dibuat untuk keperluan pembelajaran dan pengembangan portofolio.
