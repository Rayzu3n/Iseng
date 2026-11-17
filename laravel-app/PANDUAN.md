# Panduan Penggunaan NusaBoard

Panduan ini menjelaskan cara menjalankan NusaBoard, mencoba fitur website, memakai akun demo, dan memulai latihan pemeriksaan keamanan.

## 1. Mengenal NusaBoard

NusaBoard adalah website publikasi komunitas. Pengguna dapat membaca artikel, membuat akun, menulis artikel, mengelola tulisan, dan memberikan komentar.

Website ini juga merupakan lab belajar keamanan aplikasi Laravel. Beberapa bagian sengaja memiliki kekurangan keamanan. Panduan penggunaan menjelaskan cara menjalankan aplikasi, sedangkan [RED-TEAM-MANUAL.md](RED-TEAM-MANUAL.md) berisi misi untuk memetakan aplikasi, menguji celah, membuktikan dampak, dan membuat laporan sebagai Red Team.

**Penting:** NusaBoard versi latihan hanya boleh dijalankan di komputer sendiri melalui alamat 127.0.0.1. Jangan deploy versi ini ke server publik, jangan gunakan data asli, dan jangan memberikan akses ke orang lain melalui jaringan.

## 2. Menjalankan website

### Persiapan

Pastikan perangkat sudah memiliki PHP 8.3+, Composer 2, MariaDB atau MySQL, dan ekstensi PHP untuk Laravel seperti pdo_mysql, mbstring, openssl, serta curl.

Masuk ke folder aplikasi:

~~~bash
cd ~/Iseng/laravel-app
~~~

### Memasang dependensi

~~~bash
composer install
~~~

Composer akan membuat folder vendor yang berisi dependensi Laravel. Jika muncul error bahwa vendor/autoload.php tidak ditemukan, biasanya dependensi belum dipasang. Jalankan composer install dari folder laravel-app.

### Menyiapkan file lingkungan

Buat file .env jika belum ada:

~~~bash
cp .env.example .env
~~~

Jika file .env sudah ada, jangan menyalinnya lagi. Buka file tersebut dan pastikan pengaturan berikut tersedia:

~~~env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nusaboard_lab
DB_USERNAME=root
DB_PASSWORD=
~~~

Sesuaikan DB_USERNAME dan DB_PASSWORD dengan akun MariaDB di perangkatmu.

### Membuat database

Masuk ke MariaDB:

~~~bash
mariadb -u root -p
~~~

Buat database:

~~~sql
CREATE DATABASE nusaboard_lab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
~~~

Keluar dari MariaDB:

~~~sql
exit;
~~~

Jika akun root tidak menggunakan kata sandi, opsi -p dapat dihilangkan sesuai konfigurasi MariaDB lokalmu.

### Menyiapkan aplikasi

~~~bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan config:clear
~~~

Perintah migrate:fresh --seed membuat ulang tabel dan mengisi akun serta konten demo. Perintah ini menghapus data lama di database nusaboard_lab.

### Menjalankan server

~~~bash
php artisan serve --host=127.0.0.1 --port=8000
~~~

Buka alamat http://127.0.0.1:8000 di browser. Biarkan terminal server tetap berjalan selama website digunakan. Untuk menghentikan server, tekan Ctrl+C pada terminal tersebut.

## 3. Akun demo

Semua akun demo memiliki kata sandi:

~~~text
password
~~~

| Peran | Email |
|---|---|
| Administrator | admin@nusaboard.test |
| Editor | editor@nusaboard.test |
| Anggota | member@nusaboard.test |

Gunakan akun demo untuk mengenal perbedaan peran. Akun ini hanya untuk database latihan lokal, bukan akun layanan sungguhan.

## 4. Mengenal halaman website

### Beranda / Jelajahi

Alamat: /

- Melihat artikel yang sudah diterbitkan.
- Membaca artikel unggulan.
- Mencari artikel berdasarkan judul.
- Melihat kategori dan jumlah artikel.
- Membuka halaman detail artikel.

Pengguna yang sudah masuk juga dapat memakai tombol Tulis artikel.

### Daftar dan masuk

Alamat: /register dan /login

Pengguna baru dapat membuat akun dengan nama, email, dan kata sandi. Registrasi akan membuat akun dengan peran anggota.

Pengguna terdaftar dapat masuk memakai email dan kata sandi. Tombol Keluar tersedia setelah berhasil masuk.

### Dasbor

Alamat: /dashboard

Dasbor menampilkan ringkasan jumlah artikel, artikel yang diterbitkan, komentar, kategori, daftar tulisan terbaru, dan peran akun.

### Artikel Saya

Alamat: /manage/articles

Halaman ini menampilkan artikel yang terkait dengan akun yang sedang masuk. Dari sini pengguna dapat membuat artikel baru, membuka formulir edit, melihat artikel yang sudah diterbitkan, dan menghapus artikel.

### Membuat atau mengedit artikel

Formulir artikel memiliki beberapa bagian:

- Judul artikel: judul utama tulisan.
- Ringkasan singkat: penjelasan pendek yang tampil di daftar artikel.
- Isi artikel: konten utama tulisan.
- Kategori: topik tulisan.
- Status publikasi: pilih Draf atau Terbit.
- Artikel unggulan: penanda artikel pilihan.

Gunakan konten fiktif selama latihan. Jangan memasukkan informasi pribadi.

### Detail artikel dan komentar

Alamat: /articles/{slug}

Pengunjung dapat membaca artikel yang sudah diterbitkan. Pengguna yang sudah masuk dapat mengirim komentar. Komentar akan tersimpan dan ditampilkan pada halaman artikel.

### Administrasi

Alamat: /admin

Halaman ini menampilkan ringkasan akun, artikel, komentar, kategori, dan direktori pengguna. Akses halaman ini sengaja memiliki kekurangan pemeriksaan peran untuk latihan keamanan.

## 5. Alur belajar keamanan

Kerjakan di instalasi lokal milikmu sendiri. Tujuan latihan adalah memahami kode dan memperbaikinya, bukan menguji website pihak lain.

### Latihan 1: SQL Injection

Lokasi kode: app/Http/Controllers/HomeController.php

Amati bagaimana nilai pencarian dimasukkan ke kueri SQL. Cari perbedaan antara penyusunan SQL mentah dan query builder Laravel yang memakai parameter binding.

Pertanyaan:
- Dari mana nilai pencarian berasal?
- Bagian mana yang menyusun pernyataan SQL?
- Mengapa input pengguna tidak seharusnya digabungkan langsung ke kueri?
- Bagaimana bentuk perbaikan menggunakan parameter?

### Latihan 2: Stored Cross-Site Scripting (Stored XSS)

Lokasi kode: app/Http/Controllers/CommentController.php dan resources/views/articles/show.blade.php

Ikuti alur komentar mulai dari formulir, penyimpanan ke database, hingga ditampilkan kembali di halaman artikel. Bandingkan output komentar dengan output isi artikel. Perhatikan perbedaan escaping pada Blade.

Pertanyaan:
- Apakah komentar disimpan atau hanya ditampilkan sementara?
- Bagaimana Blade menampilkan teks biasa dengan aman?
- Apa risiko menampilkan konten pengguna tanpa escaping?
- Bagaimana cara memastikan konten ditampilkan sebagai teks?

### Latihan 3: IDOR / BOLA

Lokasi kode: app/Http/Controllers/ArticleController.php

Periksa method edit, update, dan destroy. Bandingkan dengan method index yang hanya menampilkan artikel milik pengguna yang sedang masuk.

Pertanyaan:
- Apakah mengetahui ID artikel cukup untuk mengaksesnya?
- Di mana seharusnya kepemilikan artikel diperiksa?
- Apa fungsi Laravel Policy atau Gate?
- Bagaimana menguji bahwa pengguna tidak dapat mengubah artikel milik pengguna lain?

### Latihan 4: Mass Assignment

Lokasi kode: app/Http/Controllers/ArticleController.php dan app/Models/Article.php

Perhatikan data request yang diterima controller dan atribut yang diizinkan model.

Pertanyaan:
- Data mana yang benar-benar dibutuhkan untuk membuat artikel?
- Mengapa mengambil seluruh request berisiko?
- Apa perbedaan fillable dan guarded?
- Mengapa validasi dan allowlist atribut perlu diterapkan?

### Latihan 5: Broken Access Control

Lokasi kode: routes/web.php dan app/Http/Controllers/AdminController.php

Bandingkan tampilan navigasi dengan pemeriksaan akses pada rute server.

Pertanyaan:
- Apakah menyembunyikan tautan admin sudah cukup untuk melindungi halaman?
- Apa perbedaan autentikasi dan otorisasi?
- Bagaimana middleware atau policy membatasi akses berdasarkan peran?

## 6. Format catatan latihan

Untuk setiap temuan, tulis laporan singkat dengan format berikut:

1. Nama kerentanan.
2. File dan method yang bermasalah.
3. Penyebab masalah.
4. Dampak terhadap aplikasi.
5. Rekomendasi perbaikan.
6. Bukti bahwa perbaikan berhasil melalui pengujian lokal.

Jangan hanya menuliskan nama kerentanannya. Jelaskan hubungan antara input, proses backend, database, dan output yang ditampilkan.

## 7. Menjalankan pengujian

~~~bash
php artisan test
~~~

Jalankan ulang pengujian setelah melakukan perubahan kode. Tambahkan pengujian baru untuk memastikan kerentanan yang ditemukan benar-benar sudah diperbaiki.

## 8. Mengembalikan data demo

~~~bash
php artisan migrate:fresh --seed
~~~

**Peringatan:** perintah ini menghapus seluruh tabel pada database yang sedang dikonfigurasi. Pastikan koneksi benar-benar mengarah ke database latihan nusaboard_lab.

## 9. Pemecahan masalah

### Error vendor/autoload.php tidak ditemukan

Jalankan dari folder laravel-app:

~~~bash
composer install
~~~

### File .env tidak ditemukan

~~~bash
cp .env.example .env
~~~

### Bahasa masih tampil dalam Bahasa Inggris

Pastikan file .env memiliki APP_LOCALE=id dan APP_FALLBACK_LOCALE=id, lalu jalankan:

~~~bash
php artisan config:clear
~~~

### Gagal terhubung ke database

Periksa apakah MariaDB sedang berjalan, database nusaboard_lab sudah dibuat, dan DB_USERNAME serta DB_PASSWORD di .env sudah benar.

### Port 8000 sedang digunakan

~~~bash
php artisan serve --host=127.0.0.1 --port=8001
~~~

Lalu buka http://127.0.0.1:8001.

---

**Ingat:** NusaBoard adalah proyek latihan dengan kerentanan yang disengaja. Jangan gunakan versi rentan ini untuk layanan publik atau data sungguhan.
