# Kire Store

Website Kire Store dibangun dengan Laravel 10, Bootstrap 5, dan MySQL. Tersedia halaman beranda, registrasi dan login, dashboard pengguna/admin, pemesanan top up Mobile Legends, serta order jasa joki Mobile Legends.

## Persiapan database

Buat database MySQL bernama `kirestore` di Laragon/phpMyAdmin:

```sql
CREATE DATABASE kirestore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Periksa `.env` dan sesuaikan `DB_HOST`, `DB_PORT`, `DB_USERNAME`, serta `DB_PASSWORD` jika konfigurasi MySQL lokalmu berbeda. Sebelum menjalankan seeder, ganti `ADMIN_PASSWORD` dengan kata sandi milikmu.

## Menjalankan aplikasi

Dari terminal di folder proyek:

```bash
composer install
php artisan migrate --seed
php artisan serve
```

Buka alamat yang ditampilkan oleh `php artisan serve`. Alternatifnya, gunakan auto virtual host Laragon dengan `APP_URL` yang sesuai.

Seeder membuat akun administrator dari `ADMIN_NAME`, `ADMIN_EMAIL`, dan `ADMIN_PASSWORD` di `.env`. Nilai bawaan untuk pengembangan lokal adalah:

- Email: `admin@kirestore.test`
- Kata sandi: `password`

Segera ganti kata sandi bawaan tersebut sebelum aplikasi digunakan bersama atau dipublikasikan. Registrasi dari halaman publik selalu membuat akun ber-role `user`; akses admin hanya diberikan melalui konfigurasi/seeder.

## Dashboard

- Pengguna dapat melihat ringkasan akun dan saldo.
- Pengguna dapat memilih paket diamond Mobile Legends, memasukkan User ID dan Zone ID, serta melihat status pesanannya.
- Pengguna dapat mengirim order joki dengan nickname, ID/Zone ID, rank saat ini/tujuan, layanan, dan catatan; daftar pemain serta status order tampil di dashboard.
- Admin dapat melihat jumlah akun, daftar pengguna terbaru, pesanan top up, roster pemain joki, mengirim penawaran harga, dan memperbarui status pesanan.
- Admin dapat membuat akun worker dan menugaskan order joki yang sudah mendapat penawaran harga; worker hanya melihat tugas yang ditugaskan kepadanya.
- Worker dapat memulai pengerjaan dan menandai ordernya selesai; pendaftaran publik tetap hanya membuat akun user.
- Dashboard dan halaman admin dilindungi middleware autentikasi serta pemeriksaan role.

Admin membuat akun worker dari bagian **Daftar worker** di dashboard admin dengan nama, email, dan kata sandi awal. Setelah order joki mendapat penawaran harga, pilih worker pada daftar pemain lalu tekan **Tugaskan**. Worker login menggunakan akun tersebut dan membuka dashboard untuk melihat tugasnya; progres hanya dapat berpindah dari **Harga dikirim** ke **Sedang dikerjakan**, lalu **Selesai**.

## Top up Mobile Legends

Pengguna yang sudah login dapat membuka menu **Top Up ML** pada navigasi. Katalog paket dan harga awal berada di `config/kirestore.php` dan dapat disesuaikan. Pesanan tersimpan dengan status menunggu konfirmasi; pembayaran dan pengiriman diamond masih diproses manual oleh admin. Integrasi payment gateway atau provider top up otomatis belum tersedia.

## Order jasa joki Mobile Legends

Pelanggan dapat memilih layanan Joki Rank, Push Win Rate, atau Placement Match, lalu mengisi data akun pemain dan rank. Harga belum ditetapkan saat order dibuat; admin mengisi penawaran minimal Rp1.000 serta memperbarui status order di dashboard admin. Jangan minta atau menyimpan password maupun kode OTP akun pemain.

## Pengujian

```bash
php artisan test
```

Pengujian menggunakan SQLite in-memory dan tidak mengubah database MySQL lokal.
Aktifkan ekstensi `pdo_sqlite` di `php.ini` bila PHP menampilkan pesan `could not find driver`.
