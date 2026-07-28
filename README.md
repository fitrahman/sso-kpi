# 🔑 KPI SSO Portal (Single Sign-On & Identity Management)

Portal Manajemen Identitas dan Autentikasi Single Sign-On (SSO) berbasis **Laravel 12** dan **Laravel Passport (OAuth2)** yang dirancang sebagai portal terpusat untuk mengelola akses pengguna di lingkungan internal Komisi Penyiaran Indonesia (KPI).

Aplikasi ini berfungsi sebagai penyedia identitas pusat (*Identity Provider*) di mana di masa mendatang sistem/aplikasi client lainnya (seperti Sistem Informasi, Aplikasi Sistem Kepegawaian, dll.) dapat diintegrasikan dengan portal ini menggunakan protokol OAuth2.

---

## 🚀 Fitur Utama

- **Pusat Autentikasi OAuth2 (Laravel Passport)**: Berfungsi sebagai *Identity Provider* terpusat yang siap diintegrasikan dengan berbagai aplikasi klien menggunakan metode OAuth2 Authorization Code Grant.
- **Registrasi Akun dengan Persetujuan Admin**: Pengguna baru dapat mendaftar secara mandiri, tetapi akun baru dalam status `pending` dan hanya aktif setelah disetujui oleh Administrator.
- **Pengajuan Edit Profil dengan Persetujuan Admin**: Pengguna dapat mengajukan perubahan data profil mereka (Nama, Telepon, dll). Perubahan data baru akan diterapkan ke database setelah disetujui oleh Administrator di dashboard.
- **Pemberitahuan Email**: Sistem mengirimkan email otomatis (tanpa email verifikasi paksa) saat pendaftaran disetujui/ditolak, serta saat pengajuan akses aplikasi disetujui/ditolak.
- **Manajemen Hak Akses Aplikasi**: Administrator dapat mengelola daftar aplikasi terintegrasi serta menyetujui atau menolak permohonan akses dari pengguna untuk aplikasi tertentu.

---

## 🛠️ Langkah Instalasi di Laragon

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

## 🌐 Integrasi Aplikasi Klien (Lokal & Produksi/Global)

Agar aplikasi klien (seperti Sistem Informasi, Dashboard internal, dll.) dapat terhubung ke portal **SSO KPI** ini (baik di lingkungan lokal maupun setelah di-deploy ke server produksi/global), ikuti panduan berikut:

### 1. Dapatkan Kredensial OAuth Client
1. Masuk ke database/admin panel **SSO KPI Portal** Anda.
2. Daftarkan entri baru di tabel `oauth_clients` untuk aplikasi klien Anda untuk mendapatkan **Client ID** dan **Client Secret** (bisa dibuat secara otomatis menggunakan Laravel Passport CLI: `php artisan passport:client`).
3. Tentukan **Redirect URL** (alamat callback pada aplikasi klien Anda di mana portal akan mengarahkan kembali setelah login sukses).

### 2. Konfigurasi di Sisi Aplikasi Klien
Pada berkas `.env` aplikasi klien Anda, tambahkan/sesuaikan variabel berikut:

```env
# ==========================================
# SSO Passport Configuration
# ==========================================

# 1. Masukkan Client ID yang didapatkan dari SSO Portal
SSO_CLIENT_ID="MASUKKAN_CLIENT_ID_ANDA"

# 2. Masukkan Client Secret yang didapatkan dari SSO Portal
SSO_CLIENT_SECRET="MASUKKAN_CLIENT_SECRET_ANDA"

# 3. Alamat callback sistem klien Anda (harus sama persis dengan yang didaftarkan di SSO)
SSO_REDIRECT_URI="http://nama-sistem-klien.test/auth/sso/callback"

# 4. Alamat host SSO Portal (Pusat Autentikasi)
# Gunakan http://sso-kpi.test untuk lokal Laragon, atau ganti ke domain produksi global Anda (misal: https://sso.kpi.go.id)
SSO_HOST="http://sso-kpi.test"
```

### 3. Bersihkan Cache Klien
Setelah memperbarui konfigurasi `.env` pada aplikasi klien, bersihkan cache agar konfigurasi baru terbaca:
```bash
php artisan config:clear
php artisan cache:clear
```
