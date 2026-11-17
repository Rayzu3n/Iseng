# NusaBoard

NusaBoard adalah aplikasi publikasi komunitas berbasis Laravel 13 yang digunakan sebagai lingkungan belajar keamanan web secara lokal. Aplikasi ini memiliki autentikasi, database, pengelolaan artikel, komentar, halaman administrasi, dan beberapa kerentanan yang sengaja ditanam untuk latihan.

> **Peringatan:** aplikasi ini memang mengandung kode yang rentan. Jalankan hanya di komputer sendiri dengan data fiktif. Jangan deploy aplikasi ini ke hosting publik dan jangan membukanya ke jaringan luar.

## Teknologi

- Laravel 13
- PHP 8.3+
- MariaDB / MySQL
- Blade
- CSS biasa, tanpa proses build frontend
- PHPUnit

## Fitur

- Registrasi, login, logout, dan sesi pengguna
- Dasbor anggota dengan ringkasan konten
- Pembuatan, pengeditan, penerbitan, dan penghapusan artikel
- Halaman detail artikel dan komentar
- Pencarian artikel dan kategori
- Ringkasan administrasi dan direktori pengguna
- Akun demo, kategori, artikel, draf, dan komentar dari seeder
- Catatan latihan keamanan pada bagian aplikasi yang sengaja dibuat rentan
- Antarmuka Bahasa Indonesia

## Persyaratan

- PHP 8.3 atau lebih baru
- Composer 2
- MariaDB atau MySQL
- Ekstensi PHP yang diperlukan Laravel, termasuk pdo_mysql, mbstring, openssl, pdo, tokenizer, xml, ctype, curl, fileinfo, dan json

## Instalasi lokal

Jalankan perintah dari folder proyek ini.

1. Pasang dependensi:

~~~bash
composer install
~~~

2. Buat file konfigurasi jika belum ada:

~~~bash
cp .env.example .env
~~~

Jangan jalankan perintah tersebut jika kamu sudah memiliki file .env yang berisi konfigurasi lokal sendiri.

3. Buat database MariaDB:

~~~sql
CREATE DATABASE nusaboard_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
~~~

4. Periksa pengaturan di file .env. Sesuaikan nama pengguna dan kata sandi database. Pastikan bahasa menggunakan APP_LOCALE=id dan APP_FALLBACK_LOCALE=id.

5. Buat application key, siapkan tabel, dan isi data demo:

~~~bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan config:clear
~~~

6. Jalankan server lokal:

~~~bash
php artisan serve --host=127.0.0.1 --port=8000
~~~

Buka http://127.0.0.1:8000 di browser.

Tidak diperlukan Node.js atau npm untuk menjalankan antarmuka.

## Akun demo

Semua akun demo menggunakan kata sandi lokal password.

| Peran | Email |
|---|---|
| Admin | admin@nusaboard.test |
| Editor | editor@nusaboard.test |
| Anggota | member@nusaboard.test |

Registrasi baru otomatis membuat akun dengan peran anggota.

## Latihan keamanan

| Materi | Lokasi untuk diperiksa | Fokus pembelajaran |
|---|---|---|
| SQL Injection | Pencarian publik di HomeController.php | Interpolasi SQL mentah dan parameter binding |
| Stored XSS | CommentController.php dan articles/show.blade.php | Input tersimpan dan output escaping |
| IDOR / BOLA | Edit, update, dan delete di ArticleController.php | Pemeriksaan kepemilikan dan policy |
| Mass Assignment | Article.php dan ArticleController.php | Validasi atribut dan perlindungan model |
| Otorisasi peran | AdminController.php dan rute /admin | Autentikasi berbeda dari otorisasi |

Setiap halaman terkait memiliki catatan lab. Komentar kode yang memuat tulisan VULNERABLE LAB menandai bagian yang sengaja dibuat tidak aman.

## Menjalankan pengujian

~~~bash
php artisan test
~~~

## Mengembalikan data demo

Perintah berikut menghapus tabel lokal, membuat ulang struktur database, lalu mengisi data contoh:

~~~bash
php artisan migrate:fresh --seed
~~~

Perintah ini menghapus perubahan pada database latihan lokal.

## Dokumentasi

- Panduan Penggunaan: PANDUAN.md. Berisi langkah menjalankan website, menggunakan fitur, akun demo, dan alur belajar.
- Repositori proyek: https://github.com/Rayzu3n/Iseng

## Catatan sebelum produksi

Jangan gunakan kode ini sebagai fondasi aplikasi produksi. Sebelum deployment, perbaiki pencarian SQL, rendering komentar, mass assignment, otorisasi edit dan hapus, serta akses halaman admin. Tambahkan policy, query berparameter, output escaping, validasi allowlist, dan pengujian keamanan regresi.
