# Tugas 2: Membuat Sistem Informasi Manajemen Data Sederhana
## BookNest - Katalog Data Buku

**Mata Kuliah:** Aplikasi Teknologi Online  
**Dosen Pengampu:** Donny Reza, M.Kom  

### Disusun Oleh:
- **Nama:** Tabhita Gialaista  
- **NIM:** 10124093  
- **Kelas:** IF-2  

---

Katalog buku sederhana untuk Tugas 2. PHP Native 8.1+, MySQL 8.0.16+ atau MariaDB 10.2.1+, tanpa framework/Composer. Ekstensi PHP: pdo_mysql.

## Menjalankan di Laragon

1. Ekstrak folder ke `C:\laragon\www\booknest`.
2. Klik **Start All**.
3. Impor `database.sql` melalui HeidiSQL atau phpMyAdmin. Skema membuat database `booknest`. Tabel `users` dibuat ulang, sedangkan tabel `books` tidak dihapus jika sudah ada.
4. Sesuaikan koneksi di `koneksi.php`. Default: host `127.0.0.1`, database `booknest`, pengguna `root`, kata sandi kosong.
5. Buka `http://localhost/booknest/login.php`.
6. Login: **admin** / **Admin123!**.

Akun awal hanya untuk tugas lokal dan kata sandinya disimpan apa adanya di tabel `users`. Untuk mengganti: `UPDATE users SET password = 'KataSandiBaru' WHERE username = 'admin';`. Jangan unggah kredensial database asli.

## Batas fitur

- CRUD katalog: kode unik, judul, penulis, penerbit, tahun terbit.
- Login/logout dengan session, satu jenis akses, tanpa role atau UI pengelolaan akun.
- Bahasa Indonesia/Inggris disimpan di session dan berlaku pada seluruh halaman serta pesan.
- Tidak ada anggota, peminjaman, pengembalian, denda atau statistik.

## Alur dan penerapan tugas

Login → daftar buku → tambah/ubah atau konfirmasi hapus → daftar buku. Bahasa dapat diganti pada setiap halaman. Pengguna tanpa session login selalu mendapat halaman login. Hapus hanya terjadi setelah form konfirmasi POST dikirim. Form dan pengolahan datanya berada dalam satu alamat (tanpa pindah halaman), seperti pada materi "Form dan Pengolahan Data Dalam Satu File".

Berkas:

- `index.php`: pengatur halaman (belum login: halaman login, sudah login:)