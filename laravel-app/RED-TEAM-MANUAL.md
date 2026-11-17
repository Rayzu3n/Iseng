# NusaBoard Red Team Manual

Panduan ini menempatkan kamu sebagai penguji keamanan (Red Team). Tugasmu adalah memetakan permukaan aplikasi, menemukan titik lemah, membuktikan dampaknya secara terkendali, dan menulis laporan temuan. Kamu tidak diminta memperbaiki kode terlebih dahulu.

> **Ruang lingkup wajib:** semua misi hanya boleh dilakukan pada instalasi NusaBoard milikmu sendiri di `http://127.0.0.1:8000`, menggunakan akun dan data fiktif yang disediakan proyek. Jangan mencoba teknik ini pada situs publik, akun orang lain, atau sistem yang tidak kamu miliki izin tertulisnya. Jangan gunakan payload yang menghapus atau mengubah data di luar kebutuhan pembuktian.

## Misi utama

| Misi | Target | Tujuan pembuktian |
|---|---|---|
| 00 | Pemetaan aplikasi | Mengenali halaman, route, role, dan fitur input |
| 01 | SQL Injection | Membuktikan input pencarian memengaruhi struktur kueri |
| 02 | Stored XSS | Membuktikan komentar ditampilkan sebagai HTML aktif |
| 03 | IDOR / BOLA | Membuktikan akun dapat membuka objek milik akun lain |
| 04 | Mass Assignment | Membuktikan field yang tidak dimaksudkan dapat diubah melalui request |
| 05 | Broken Access Control | Membuktikan anggota biasa dapat membuka halaman administrasi |
| 06 | Laporan | Menulis bukti, dampak, dan tingkat risiko dari tiap temuan |

## Persiapan

1. Jalankan NusaBoard secara lokal. Dari folder `laravel-app/`, gunakan `composer run dev`.
2. Pastikan alamat yang dibuka adalah `http://127.0.0.1:8000`.
3. Pastikan database berisi data demo. Jika perlu, isi ulang dengan `php artisan migrate:fresh --seed`. Perintah ini menghapus data pada database yang sedang dikonfigurasi.
4. Buka browser Developer Tools (biasanya F12). Tab Network membantu melihat request dan response yang kamu hasilkan sendiri.
5. Buat catatan untuk setiap misi. Simpan screenshot, URL lokal, akun demo yang digunakan, waktu pengujian, dan hasil yang terlihat. Jangan menyimpan kata sandi pribadi.

## Akun demo

Semua akun berikut memakai kata sandi lokal `password`.

| Role | Email |
|---|---|
| Admin | `admin@nusaboard.test` |
| Editor | `editor@nusaboard.test` |
| Anggota | `member@nusaboard.test` |

Gunakan akun berbeda untuk membandingkan batas akses. Registrasi biasa akan membuat akun dengan role anggota.

---

## Misi 00: Reconnaissance dan pemetaan

**Tujuan:** pahami aplikasi sebelum menguji kerentanannya.

### Langkah pengintaian

1. Buka beranda `/` dan catat fitur yang tampak.
2. Periksa tautan menuju login, register, detail artikel, dashboard, dan administrasi.
3. Daftar sebagai anggota atau masuk menggunakan akun demo.
4. Catat fitur yang hanya muncul setelah login.
5. Gunakan tab Network pada Developer Tools untuk melihat method HTTP, path, parameter, dan status response ketika kamu memakai fitur normal.

### Petunjuk

- Mulailah dari `routes/web.php` jika kamu melakukan grey-box testing dan memiliki akses source code.
- Periksa form pencarian, form komentar, form artikel, dan tautan pengelolaan.
- Jangan menganggap link yang tidak terlihat berarti route tersebut terlindungi.

**Bukti selesai:** peta endpoint berisi method, path, status login yang dibutuhkan, serta role yang kamu pakai saat mengaksesnya.

## Misi 01: SQL Injection pada pencarian

**Target:** pencarian publik di beranda.

**Tujuan:** tunjukkan bahwa input pencarian diolah sebagai bagian dari string SQL, bukan sebagai nilai parameter yang terpisah.

### Langkah

1. Buka beranda dan cari judul normal, misalnya `Laravel`. Amati hasil normalnya.
2. Perhatikan URL dan parameter `q` yang dikirim oleh form.
3. Pada instalasi lokal saja, coba masukkan tanda kutip tunggal (`'`) dan amati apakah respons berubah atau menghasilkan error.
4. Lanjutkan dengan probe Boolean yang hanya membaca data:
   ```text
   ' OR 1=1 -- 
   ```
5. Bandingkan hasilnya dengan pencarian normal. Perhatikan apakah artikel yang seharusnya tidak cocok dengan kata kunci ikut muncul.

### Petunjuk bertingkat

- **Petunjuk 1:** periksa nilai `q` pada request GET ke beranda.
- **Petunjuk 2:** cari cara query dibangun di `HomeController@index`.
- **Petunjuk 3:** bandingkan perilaku input biasa dengan input yang mengubah kondisi logika kueri.

**Bukti selesai:** screenshot pencarian normal dan pencarian probe, beserta penjelasan singkat tentang perbedaan hasilnya.

## Misi 02: Stored Cross-Site Scripting (XSS)

**Target:** form komentar pada halaman detail artikel.

**Tujuan:** buktikan bahwa komentar yang disimpan dapat ditafsirkan browser sebagai markup aktif.

### Langkah

1. Masuk sebagai akun anggota.
2. Buka salah satu artikel yang diterbitkan.
3. Kirim komentar biasa terlebih dahulu dan pastikan komentar muncul.
4. Untuk uji lokal ini, kirim satu proof-of-concept yang hanya menampilkan dialog:
   ```html
   <script>alert('NusaBoard XSS')</script>
   ```
5. Muat ulang halaman artikel dan amati apakah dialog muncul ketika komentar ditampilkan.
6. Hentikan pengujian setelah pembuktian. Jangan mengganti proof-of-concept dengan kode yang mengambil data, mengirim request ke pihak ketiga, atau mengubah browser pengguna.

### Petunjuk bertingkat

- **Petunjuk 1:** ikuti komentar dari form sampai halaman artikel.
- **Petunjuk 2:** periksa penyimpanan pada `CommentController` dan output di `articles/show.blade.php`.
- **Petunjuk 3:** bandingkan output komentar dengan output isi artikel yang menggunakan escaping.

**Bukti selesai:** screenshot komentar tersimpan dan bukti dialog lokal, ditambah lokasi kode yang menyebabkan browser menafsirkan konten sebagai HTML.

## Misi 03: IDOR / Broken Object Level Authorization

**Target:** route edit artikel `/manage/articles/{id}/edit`.

**Tujuan:** buktikan bahwa status login saja sudah cukup untuk mengakses artikel milik akun lain.

### Langkah

1. Masuk menggunakan akun anggota.
2. Buka halaman Artikel Saya dan catat artikel milik akun tersebut.
3. Buka artikel seed milik akun demo lain melalui route edit menggunakan ID numerik.
4. Uji contoh ID lokal hasil seed seperti `/manage/articles/1/edit` atau `/manage/articles/2/edit`. Jika database pernah dimodifikasi, ID bisa berbeda.
5. Amati apakah halaman edit terbuka meskipun penulisnya bukan akun yang sedang masuk.
6. Jangan menyimpan perubahan pada artikel orang lain selama misi ini. Cukup buktikan akses yang tidak semestinya.

### Petunjuk bertingkat

- **Petunjuk 1:** bandingkan isi halaman Artikel Saya dengan hasil membuka route edit.
- **Petunjuk 2:** cari route dengan parameter `{article}` di `routes/web.php`.
- **Petunjuk 3:** periksa apakah controller melakukan pemeriksaan kepemilikan atau policy sebelum menampilkan form.

**Bukti selesai:** screenshot halaman edit yang berhasil dibuka oleh akun yang bukan pemiliknya, tanpa mengubah data artikel.

## Misi 04: Mass Assignment

**Target:** request pembaruan artikel dan atribut model Article.

**Tujuan:** buktikan bahwa server menerima atribut tambahan yang tidak seharusnya dapat dikendalikan langsung oleh pengguna.

### Langkah

1. Masuk sebagai anggota dan buat artikel uji milikmu sendiri.
2. Buka form edit artikel tersebut.
3. Di Developer Tools, buka tab Elements dan periksa field pada form. Amati bahwa form normal tidak menampilkan field `user_id`.
4. Buka tab Network dan kirim pembaruan normal untuk melihat bentuk request.
5. Pada request lokal milikmu sendiri, gunakan fitur edit/resend yang tersedia di browser atau alat inspeksi request untuk menambahkan field `user_id` dengan ID admin demo (biasanya `1` setelah database di-seed ulang). Pertahankan field form lain yang wajib.
6. Kirim hanya pada artikel uji milikmu. Amati apakah artikel berpindah kepemilikan dan hilang dari daftar artikel akun asal.
7. Pulihkan data demo setelah pengujian jika diperlukan.

### Petunjuk bertingkat

- **Petunjuk 1:** bandingkan field yang ada di form dengan field yang diterima server.
- **Petunjuk 2:** periksa penggunaan `$request->all()` pada method `update`.
- **Petunjuk 3:** periksa atribut `$fillable` pada `app/Models/Article.php`, khususnya atribut kepemilikan.

**Bukti selesai:** request yang kamu ubah, response server, dan perubahan kepemilikan pada artikel uji. Jangan lakukan uji ini pada artikel seed milik akun lain.

## Misi 05: Broken Access Control pada halaman admin

**Target:** route `/admin`.

**Tujuan:** buktikan bahwa pengguna yang sudah login sebagai anggota dapat membuka halaman administrasi.

### Langkah

1. Keluar dari akun admin jika sedang digunakan.
2. Masuk sebagai `member@nusaboard.test`.
3. Perhatikan bahwa navigasi tidak menampilkan tautan Administrasi.
4. Ketik alamat berikut secara langsung:
   ```text
   http://127.0.0.1:8000/admin
   ```
5. Amati status response dan apakah isi halaman administrasi dapat dilihat.
6. Jangan mengubah atau menghapus data pengguna.

### Petunjuk bertingkat

- **Petunjuk 1:** bedakan menu navigasi dengan route aplikasi.
- **Petunjuk 2:** cari definisi route `/admin` di `routes/web.php`.
- **Petunjuk 3:** periksa apakah middleware hanya memeriksa autentikasi atau juga role.

**Bukti selesai:** screenshot halaman admin saat login sebagai anggota, ditambah route dan middleware yang relevan.

---

## Template laporan temuan

Buat satu laporan untuk setiap misi yang berhasil dibuktikan.

| Bagian | Isi |
|---|---|
| ID temuan | Contoh: NB-01 |
| Nama | Jenis kerentanan |
| Target | Route atau fitur yang diuji |
| Akun | Role akun demo yang digunakan |
| Prasyarat | Kondisi sebelum pengujian |
| Langkah reproduksi | Ringkasan tindakan di localhost |
| Bukti | Screenshot, request, response, dan waktu |
| Dampak | Apa yang dapat diakses atau diubah |
| Severity | Informasional / Rendah / Sedang / Tinggi |
| Catatan | Batas pengujian atau hal yang belum terbukti |

Jangan memberi severity hanya berdasarkan nama kerentanan. Nilai berdasarkan dampak yang benar-benar berhasil kamu buktikan pada aplikasi ini.

## Checklist akhir

- [ ] Misi 00: peta route dan fitur selesai.
- [ ] Misi 01: indikasi SQL Injection didokumentasikan.
- [ ] Misi 02: Stored XSS dibuktikan dengan dialog lokal.
- [ ] Misi 03: akses objek milik akun lain diuji.
- [ ] Misi 04: atribut tambahan pada request diuji hanya pada artikel milik sendiri.
- [ ] Misi 05: akses anggota terhadap halaman admin diuji.
- [ ] Laporan temuan dibuat berdasarkan bukti.

## Setelah Red Team selesai

Tahap Blue Team dapat dilakukan sebagai latihan lanjutan: perbaiki akar masalah, tambahkan pengujian regresi, lalu ulangi misi yang sama untuk memastikan celah sudah tertutup. Tahap itu opsional untuk sesi Red Team awal.

**Batas akhir:** seluruh pengujian dalam manual ini hanya untuk NusaBoard yang kamu jalankan sendiri pada localhost. Jangan memindahkan probe atau payload ke situs publik atau sistem tanpa izin.
