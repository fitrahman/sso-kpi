# 🔑 KPI SSO Portal (Single Sign-On & Identity Management)

Portal Manajemen Identitas dan Autentikasi Single Sign-On (SSO) berbasis **Laravel 12** dan **Laravel Passport (OAuth2)** yang dirancang sebagai portal terpusat untuk mengelola akses pengguna di lingkungan internal Komisi Penyiaran Indonesia (KPI).

Aplikasi ini berfungsi sebagai penyedia identitas pusat (*Identity Provider*) di mana sistem/aplikasi client lainnya (seperti Sistem Informasi, Aplikasi Sistem Kepegawaian, dll.) dapat diintegrasikan dengan portal ini menggunakan protokol OAuth2.

---

## 🚀 Fitur Utama

- **Pusat Autentikasi OAuth2 (Laravel Passport)**: Berfungsi sebagai *Identity Provider* terpusat menggunakan metode OAuth2 Authorization Code Grant.
- **Registrasi Akun dengan Persetujuan Admin**: Pengguna baru dapat mendaftar secara mandiri, tetapi akun baru dalam status `pending` dan hanya aktif setelah disetujui oleh Administrator.
- **Pemisahan Logika Akses & Peran (Role)**:
  - **Akses Portal**: Disimpan di tabel `client_user_access` (kolom `is_active` dan `status`). Menentukan apakah pengguna diizinkan masuk ke aplikasi klien terkait.
  - **Role Lokal**: Disimpan di tabel `user_client_roles` (kolom `role`). Menentukan hak akses/peran di dalam sistem klien tersebut (selalu memiliki nilai, default `'user'`).
- **Dynamic Role Discovery**:
  - SSO Server dapat melakukan sinkronisasi daftar role secara otomatis via HTTP GET ke endpoint `/api/sso/supported-roles` milik masing-masing aplikasi klien yang diamankan dengan `X-SSO-Secret`.
  - Dilengkapi dengan `RoleDiscoveryService` untuk sinkronisasi otomatis.
- **Strict Role Validation**:
  - Validasi ketat menggunakan `RoleValidationService` memastikan tidak ada penyimpanan/perubahan role baru ke tabel `user_client_roles` yang tidak sesuai dengan daftar role resmi (`supported_roles`) masing-masing klien.
- **SSO Webhooks Asinkronus**: Mengirimkan pemberitahuan instan secara asinkronus (*Laravel Queue*) ke aplikasi klien saat peran pengguna berubah (`user.role_updated`) atau ketika akses dicabut/dinonaktifkan (`user.access_revoked`). Dilengkapi dengan tanda tangan HMAC-SHA256 untuk memverifikasi asal request.
- **Single Log Out (SLO)**:
  - Ketika user logout dari SSO Portal atau aplikasi klien, seluruh token akses OAuth akan dicabut (*revoked*) secara instan.
  - Middleware klien mendeteksi status `401 Unauthorized` pada polling sinkronisasi dan otomatis mengeluarkan (logout) sesi lokal klien secara instan (mencegah sesi nyangkut).
- **Pengajuan Edit Profil dengan Persetujuan Admin**: Pengguna dapat mengajukan perubahan data profil mereka. Perubahan data baru akan diterapkan ke database setelah disetujui oleh Administrator di dashboard.
- **Pemberitahuan Email**: Sistem mengirimkan email otomatis saat pendaftaran disetujui/ditolak, serta saat pengajuan akses aplikasi disetujui/ditolak.
- **Manajemen Hak Akses Aplikasi**: Administrator dapat mengelola daftar aplikasi terintegrasi serta menyetujui atau menolak permohonan akses dari pengguna untuk aplikasi tertentu.

---

## 🛠️ CLI Tools (Artisan Commands)

SSO Server menyediakan perintah Artisan khusus untuk audit dan pembersihan data role:

1. **Audit Data**: Membaca data role lokal tanpa mengubah database untuk mencari ketidakcocokan terhadap supported_roles klien.
   ```bash
   php artisan audit:invalid-roles
   ```
2. **Perbaikan Data**: Memperbaiki data role yang tidak valid dengan role level terendah dari supported_roles secara aman (menggunakan chunking & database transaction).
   - *Dry-run (hanya preview)*:
     ```bash
     php artisan fix:invalid-roles
     ```
   - *Eksekusi nyata*:
     ```bash
     php artisan fix:invalid-roles --apply
     ```

---

## 🛠️ Langkah Instalasi di Laragon

### 1. Prasyarat
* **Laragon** terpasang dengan PHP versi 8.2 ke atas (pastikan modul `mbstring` aktif).
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
6. Jalankan queue worker untuk memproses Webhook secara asinkronus:
   ```bash
   php artisan queue:work
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

Lihat panduan lengkap integrasi, API, dan skema pemrosesan SSO Webhook pada file **[Integrasi SSO.md](Integrasi%20SSO.md)**.
