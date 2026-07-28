# 🔑 KPI SSO Portal (Single Sign-On & Identity Management)

Portal Manajemen Identitas dan Autentikasi Single Sign-On (SSO) berbasis **Laravel 12** dan **Laravel Passport (OAuth2)** yang dirancang sebagai portal terpusat untuk mengelola akses pengguna di lingkungan internal Komisi Penyiaran Indonesia (KPI).

Aplikasi ini berfungsi sebagai penyedia identitas pusat (*Identity Provider*) di mana di masa mendatang sistem/aplikasi client lainnya (seperti Sistem Informasi, Aplikasi Pengawasan, dll.) dapat diintegrasikan dengan portal ini menggunakan protokol OAuth2.

---

## 🚀 Fitur Utama

- **Pusat Autentikasi OAuth2 (Laravel Passport)**: Berfungsi sebagai *Identity Provider* terpusat yang siap diintegrasikan dengan berbagai aplikasi klien menggunakan metode OAuth2 Authorization Code Grant.
- **Registrasi Akun dengan Persetujuan Admin**: Pengguna baru dapat mendaftar secara mandiri, tetapi akun baru dalam status `pending` dan hanya aktif setelah disetujui oleh Administrator.
- **Pengajuan Edit Profil dengan Persetujuan Admin**: Pengguna dapat mengajukan perubahan data profil mereka (Nama, Telepon, dll). Perubahan data baru akan diterapkan ke database setelah disetujui oleh Administrator di dashboard.
- **Pemberitahuan Email**: Sistem mengirimkan email otomatis (tanpa email verifikasi paksa) saat pendaftaran disetujui/ditolak, serta saat pengajuan akses aplikasi disetujui/ditolak.
- **Desain Modern & Premium**: Antarmuka responsif dengan gaya *rounded-2xl* modern, aksen warna merah khas KPI, dan box alert yang bersih serta informatif.
- **Manajemen Hak Akses Aplikasi**: Administrator dapat mengelola daftar aplikasi terintegrasi serta menyetujui atau menolak permohonan akses dari pengguna untuk aplikasi tertentu.

---

## 🛠️ Langkah Instalasi di Lingkungan Laragon

### 1. Prasyarat
* **Laragon** terpasang dengan PHP versi 8.2 ke atas.
* Driver **MySQL** aktif di Laragon.
* Fitur **Auto Virtual Hosts** di Laragon aktif untuk membuat domain otomatis `.test`.

### 2. Konfigurasi Domain Virtual Host
Pastikan folder diletakkan di dalam direktori root Laragon Anda (`C:\laragon\www\`):
* `C:\laragon\www\sso-kpi` -> `http://sso-kpi.test`

*(Laragon akan otomatis membuat konfigurasi Apache/Nginx serta mendaftarkan domain tersebut di berkas `hosts` Windows Anda).*

### 3. Setup Aplikasi

1. Buka terminal di direktori proyek `c:\laragon\www\sso-kpi` dan jalankan:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```
2. Pastikan konfigurasi database di berkas `.env` sudah mengarah ke MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sso_kpi
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Buat database kosong bernama `sso_kpi` di phpMyAdmin / MySQL client Anda.
4. Jalankan migrasi dan seeder awal:
   ```bash
   php artisan migrate:fresh --seed
   ```
5. Buat kunci enkripsi untuk Passport:
   ```bash
   php artisan passport:keys
   ```

---

## 🔑 Akun Kredensial untuk Pengujian

Gunakan akun tes berikut untuk masuk dan menguji alur sistem:

### 1. Akun Administrator (SSO Portal)
* **Email**: `admin@kpi.com`
* **Kata Sandi**: `password123`
* **Peran**: Administrator (Memiliki akses ke panel persetujuan pengguna baru, persetujuan perubahan profil, dan permohonan akses aplikasi).

### 2. Akun Pengguna Biasa
* **Email**: `humas@kpi.com`
* **Kata Sandi**: `password123`
* **Peran**: Humas (Memiliki akses ke Dashboard User, dapat melakukan pengajuan edit profil, dan mengajukan akses ke aplikasi).

---

## 📋 Alur Verifikasi Fitur Portal

### A. Pengajuan Edit Profil dengan Persetujuan Admin
1. Masuk ke `http://sso-kpi.test` sebagai pengguna biasa (misal: `humas@kpi.com` / `password123`).
2. Klik tombol **Profil Saya** di pojok kanan atas dashboard.
3. Klik tombol **Edit Profil**, ubah Nama atau Nomor Telepon, lalu klik **Simpan Perubahan**.
4. Dashboard akan memunculkan banner pemberitahuan berwarna oranye yang menginformasikan bahwa pengajuan edit profil Anda sedang menunggu persetujuan Admin.
5. Log out, lalu log in sebagai administrator (`admin@kpi.com` / `password123`).
6. Buka menu **Edit Profil Requests** di sidebar kiri.
7. Anda akan melihat daftar permintaan perubahan data dengan format perbandingan data lama vs data baru.
8. Klik **Setujui** (atau **Tolak**).
9. Jika disetujui, data pengguna di tabel `users` akan diperbarui secara otomatis.
