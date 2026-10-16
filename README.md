# Tugas 2: Membuat Sistem Informasi Manajemen Data Sederhana
## BookNest - Katalog Data Buku

**Mata Kuliah:** Penerapan Teknologi Internet  
**Dosen Pengampu:** Donny Reza, M.Kom  

### Disusun Oleh:
- **Nama:** Tabhita Gialaista  
- **NIM:** 10124093  
- **Kelas:** IF-2  

---

Katalog buku sederhana untuk Tugas 2. PHP Native 8.1+, MySQL 8.0.16+ atau MariaDB 10.2.1+, tanpa framework/Composer. Ekstensi PHP: pdo_mysql dan mbstring.

## Menjalankan di Laragon

1. Ekstrak folder `booknest` ke `C:\laragon\www\booknest`.
2. Klik **Start All**.
3. Impor `database.sql` melalui HeidiSQL atau phpMyAdmin. Skema membuat database `booknest` tanpa menghapus tabel lama.
4. Sesuaikan koneksi di `config.php`. Default: host `127.0.0.1`, port `3306`, database `booknest`, pengguna `root`, kata sandi kosong.
5. Buka `http://localhost/booknest/login.php`.
6. Login: **admin** / **Admin123!**.

Akun awal hanya untuk tugas lokal. Sebelum dipublikasikan, ganti hash kata sandi dalam tabel `users`, gunakan akun database terbatas dan HTTPS. Membuat hash: `php -r "echo password_hash('KataSandiBaru', PASSWORD_DEFAULT);"`. Jangan unggah kredensial database asli.

## Batas fitur

- CRUD katalog: kode unik, judul, penulis, penerbit, tahun terbit.
- Login/logout dengan session, satu jenis akses, tanpa role atau UI pengelolaan akun.
- Bahasa Indonesia/Inggris disimpan di session dan berlaku pada seluruh halaman serta pesan.
- Tidak ada anggota, peminjaman, pengembalian, denda atau statistik.

## Alur dan penerapan tugas

Login → daftar buku → tambah/ubah atau konfirmasi hapus → daftar buku. Bahasa dapat diganti pada setiap halaman. CRUD mengarahkan pengguna tanpa session login ke `login.php`. Hapus hanya terjadi setelah form konfirmasi POST dikirim.

`app.php`: session, CSRF, PDO, sanitasi output dan validasi if/else. `login.php`: verifikasi hash dan regenerasi ID session. `index.php`: CRUD dengan switch, form POST dan prepared statements. `translations.php`: terjemahan. `assets/`: CSS dan pesan proses submit. `database.sql`: skema dan akun awal. `tests/validation.php`: pemeriksaan mandiri.

Semua kolom wajib diisi. Panjang maksimum: kode 30, judul 160, penulis dan penerbit 120 karakter. Tahun bilangan bulat 1 sampai 2100. Kode unik dijaga database. Input dipangkas, teks asli disimpan, dan `htmlspecialchars()` dipakai saat ditampilkan. `filter_var()` memvalidasi angka. Input tetap tampil jika validasi gagal. Detail kegagalan database dicatat di log server.

ERD: `users(id PK, username UNIQUE, password_hash)` dan `books(id PK, code UNIQUE, title, author, publisher, year)`. Tabel independen karena transaksi buku bukan bagian tugas.

## Pemeriksaan

Jalankan `php tests/validation.php`. Uji manual: akses tanpa login, login salah/benar, tambah, kode duplikat, tahun tidak valid, ubah, batal hapus, hapus, ganti bahasa lalu pindah halaman, logout lalu akses daftar. Uji layar ponsel dan navigasi Tab/Enter. Pemeriksaan CLI tidak mengubah database.

## Pengumpulan

Sertakan source, `database.sql`, dan laporan PDF berisi tangkapan layar serta alur program. Unggah ke GitHub publik lalu kirim tautan melalui form tugas paling lambat **31 Oktober 2026**. Publikasi dilakukan pemilik proyek.
