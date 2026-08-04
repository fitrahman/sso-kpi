# 📖 Panduan Lengkap Integrasi Aplikasi Lokal dengan SSO Server KPI

Dokumen ini adalah **panduan standar dari awal hingga akhir** untuk menyambungkan aplikasi lokal baru (Laravel) dengan **SSO Server KPI (`sso-kpi.test`)**, termasuk fitur **Sinkronisasi Role Otomatis** dan **Webhook Real-Time (Tanpa Perlu Logout)**.

---

## 🗂️ Ringkasan File yang Perlu Dibuat / Dimodifikasi

| Langkah | File | Aksi |
|---------|------|------|
| 1 | **SSO Admin Portal** | Tambah aplikasi baru → Dapatkan Client ID & Secret |
| 2 | `routes/web.php` | Tambah rute `/login`, `/auth/sso/callback`, `/logout` |
| 3 | `app/Http/Controllers/Auth/LoginController.php` | Buat controller SSO (redirect, callback, logout) |
| 4 | `routes/api.php` | Tambah rute webhook publik `/sso/webhook` |
| 5 | `app/Http/Controllers/Auth/SsoWebhookController.php` | Buat controller penerima webhook real-time |
| 6 | `app/Http/Middleware/EnsureUserHasRole.php` | Tambah `$user->refresh()` agar role aktif tanpa logout |
| 7 | `app/Providers/AppServiceProvider.php` | Tambah sinkronisasi role lokal otomatis saat booting |
| 8 | `.env` | Tambahkan variabel SSO (`Client ID`, `Secret`, dll.) |

---

## 📌 Langkah 1: Daftarkan Aplikasi di SSO Admin Portal

1. Buka browser, akses: **`http://sso-kpi.test/admin/applications`**
2. Login sebagai Admin SSO:
   - Email: `admin@kpi.com` | Password: `admin123`
3. Klik tombol **"+ Tambah Aplikasi"** di pojok kanan atas.
4. Isi formulir:
   - **Nama Aplikasi:** Nama aplikasi lokal Anda (misal: `Aplikasi Keuangan KPI`)
   - **Redirect URI / Callback URL:** `http://namaproject.test/auth/sso/callback`
   - **Role Lokal yang Didukung:** daftar role lokal Anda, pisah koma (misal: `admin, atasan, pegawai`)
5. Klik **Simpan & Buat Aplikasi**.
6. **Salin Client ID dan Client Secret** dari notifikasi yang muncul — akan dipakai di Langkah 8.

> ⚠️ **Penting:** Nilai `Redirect URI` harus **persis sama** dengan `SSO_REDIRECT_URI` di `.env` aplikasi lokal Anda.

---

## 📌 Langkah 2: Rute Otentikasi Web (`routes/web.php`)

Daftarkan 3 rute SSO pada file `routes/web.php` aplikasi lokal Anda:

```php
use App\Http\Controllers\Auth\LoginController;

// ── SSO Otentikasi ──────────────────────────────────────────────────────────
Route::get('/login', [LoginController::class, 'ssoRedirect'])->name('login');
Route::get('/auth/sso/callback', [LoginController::class, 'ssoCallback']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
```

> Hapus atau ganti rute `/login` lama yang mengarah ke form login bawaan Laravel.

---

## 📌 Langkah 3: Login & Callback Controller (`app/Http/Controllers/Auth/LoginController.php`)

Buat atau ganti file `LoginController.php` dengan kode berikut:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /**
     * Langkah A: Redirect pengguna ke Halaman Login SSO Server
     */
    public function ssoRedirect()
    {
        $query = http_build_query([
            'client_id'     => env('SSO_CLIENT_ID'),
            'redirect_uri'  => env('SSO_REDIRECT_URI'),
            'response_type' => 'code',
            'scope'         => '',
        ]);

        return redirect(env('SSO_HOST') . '/oauth/authorize?' . $query);
    }

    /**
     * Langkah B: Terima callback dari SSO Server & otentikasi pengguna
     */
    public function ssoCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Otorisasi SSO dibatalkan.');
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect('/login')->with('error', 'Kode otorisasi SSO tidak ditemukan.');
        }

        try {
            $ssoHost = env('SSO_HOST');

            // 1. Tukar Authorization Code dengan Access Token
            $tokenResponse = Http::asForm()->post($ssoHost . '/oauth/token', [
                'grant_type'    => 'authorization_code',
                'client_id'     => env('SSO_CLIENT_ID'),
                'client_secret' => env('SSO_CLIENT_SECRET'),
                'redirect_uri'  => env('SSO_REDIRECT_URI'),
                'code'          => $code,
            ]);

            if (!$tokenResponse->successful()) {
                return redirect('/login')->with('error', 'Gagal memverifikasi token SSO.');
            }

            $accessToken = $tokenResponse->json('access_token');

            // 2. Ambil Profil & Role Lokal pengguna dari API SSO
            $userResponse = Http::withToken($accessToken)
                ->get($ssoHost . '/api/user', [
                    'client_id' => env('SSO_CLIENT_ID'),
                ]);

            if (!$userResponse->successful()) {
                return redirect('/login')->with('error', 'Gagal mengambil informasi profil pengguna.');
            }

            $ssoUserData = $userResponse->json();

            // 3. Cek apakah pengguna diizinkan mengakses aplikasi ini
            if (isset($ssoUserData['role']) && $ssoUserData['role'] === 'none') {
                return redirect('/login')->with('error', 'Anda tidak memiliki hak akses ke aplikasi ini. Hubungi Administrator.');
            }

            // 4. Map role dari SSO ke role valid di aplikasi lokal
            $rawRole   = strtolower($ssoUserData['role'] ?? 'pegawai');
            $roleLokal = $rawRole; // Sesuaikan mapping jika nama role berbeda

            // 5. Provisioning / Sync pengguna ke database aplikasi lokal
            $user = User::updateOrCreate(
                ['email' => $ssoUserData['email']],
                [
                    'name'      => $ssoUserData['name'],
                    'role'      => $roleLokal,
                    'is_active' => true,
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            // 6. Login sesi lokal aplikasi
            Auth::login($user);
            $request->session()->put('sso_access_token', $accessToken);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan SSO: ' . $e->getMessage());
        }
    }

    /**
     * Langkah C: Single Logout — hapus sesi lokal & sesi SSO Server
     */
    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(env('SSO_HOST') . '/sso-logout');
    }
}
```

---

## 📌 Langkah 4: Rute Webhook Publik (`routes/api.php`)

Tambahkan 1 baris rute publik tanpa middleware di `routes/api.php`:

```php
use App\Http\Controllers\Auth\SsoWebhookController;

// ── SSO Real-Time Webhook (tidak butuh autentikasi) ─────────────────────────
Route::post('/sso/webhook', [SsoWebhookController::class, 'handle']);
```

---

## 📌 Langkah 5: Webhook Controller (`app/Http/Controllers/Auth/SsoWebhookController.php`)

Buat file controller baru. Controller ini menerima sinyal perubahan role dari SSO Server secara real-time dan langsung memperbarui database lokal:

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoWebhookController extends Controller
{
    /**
     * Menerima & memproses Webhook Real-time dari SSO Server
     */
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $data  = $request->input('data');

        Log::info('SSO Webhook Received:', [
            'event' => $event,
            'data'  => $data,
        ]);

        if ($event === 'user.role_updated' && !empty($data['email'])) {
            $rawRole = strtolower(trim($data['role'] ?? ''));

            // ── Sesuaikan mapping role SSO → role lokal aplikasi Anda ──────────
            if (in_array($rawRole, ['admin', 'superadmin', 'administrator'])) {
                $roleLokal = 'admin';
            } elseif (in_array($rawRole, ['atasan', 'manager', 'supervisor'])) {
                $roleLokal = 'atasan';
            } else {
                $roleLokal = 'pegawai';
            }
            // ────────────────────────────────────────────────────────────────────

            // Update atau buat pengguna secara real-time di database aplikasi lokal
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'] ?? $data['email'],
                    'role'     => $roleLokal,
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            Log::info("User Role updated to {$roleLokal} for {$data['email']}");

            return response()->json([
                'success' => true,
                'message' => "Role for {$data['email']} updated in real-time to {$roleLokal}",
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Event processed'], 200);
    }
}
```

> 💡 **Sesuaikan blok mapping role** (bagian yang ditandai) agar cocok dengan nama-nama role yang ada di database aplikasi lokal Anda.

---

## 📌 Langkah 6: Middleware Hak Akses (`app/Http/Middleware/EnsureUserHasRole.php`)

Jika aplikasi Anda sudah memiliki middleware `EnsureUserHasRole`, **tambahkan `$user->refresh()`** di dalamnya agar perubahan role dari Webhook langsung aktif tanpa pengguna harus logout:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Usage dalam routes: ->middleware('role:admin,atasan')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Reload data user dari DB agar perubahan role dari Webhook langsung aktif tanpa logout
        if ($user) {
            $user->refresh();
        }

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
```

Pastikan middleware ini sudah terdaftar di `bootstrap/app.php` atau `app/Http/Kernel.php`:

```php
// Di bootstrap/app.php (Laravel 11+)
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\EnsureUserHasRole::class,
    ]);
})
```

---

## 📌 Langkah 7: Sinkronisasi Role Lokal Otomatis Saat Booting (`app/Providers/AppServiceProvider.php`)

Tambahkan kode berikut pada method `boot()` di `AppServiceProvider.php` agar daftar role lokal aplikasi **otomatis tersinkron ke SSO Server** setiap kali aplikasi berjalan:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Sinkronisasi daftar role lokal ke SSO Server
        if (env('SSO_HOST') && env('SSO_CLIENT_ID')) {
            try {
                Http::timeout(3)->post(env('SSO_HOST') . '/api/client-roles/sync', [
                    'client_id'     => (int) env('SSO_CLIENT_ID'),
                    'client_secret' => env('SSO_CLIENT_SECRET'),
                    'roles'         => ['admin', 'atasan', 'pegawai'], // ← Sesuaikan daftar role aplikasi ini
                ]);
            } catch (\Exception $e) {
                // Silent: Abaikan jika SSO Server sedang offline
            }
        }
    }
}
```

---

## 📌 Langkah 8: Konfigurasi Environment (`.env`) Aplikasi Lokal

Terakhir, tambahkan 4 variabel SSO ke file `.env` aplikasi lokal Anda. Nilai `SSO_CLIENT_ID` dan `SSO_CLIENT_SECRET` didapat dari **Langkah 1** saat mendaftarkan aplikasi di Admin Portal SSO:

```env
# ── Konfigurasi SSO Passport Server ──────────────────────────────────────────
SSO_HOST="http://sso-kpi.test"
SSO_CLIENT_ID="4"                                        # ← Dari SSO Admin Portal (Langkah 1)
SSO_CLIENT_SECRET="SimpegKpiSecretKey998877665544332211" # ← Dari SSO Admin Portal (Langkah 1)
SSO_REDIRECT_URI="http://namaproject.test/auth/sso/callback" # ← Sesuaikan dengan URL project Anda
```

Setelah mengisi `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ✅ Checklist Akhir

Pastikan seluruh poin berikut sudah selesai sebelum menguji integrasi:

- [ ] Aplikasi sudah terdaftar di SSO Admin Portal (Langkah 1)
- [ ] Rute `/login`, `/auth/sso/callback`, `/logout` sudah ada di `routes/web.php`
- [ ] `LoginController.php` sudah dibuat/diperbarui
- [ ] Rute webhook `/sso/webhook` sudah ada di `routes/api.php`
- [ ] `SsoWebhookController.php` sudah dibuat
- [ ] `EnsureUserHasRole.php` sudah ditambahkan `$user->refresh()`
- [ ] `AppServiceProvider.php` sudah ditambahkan logika sync roles
- [ ] `.env` sudah diisi dengan `SSO_HOST`, `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, `SSO_REDIRECT_URI`
- [ ] `php artisan config:clear` sudah dijalankan

---

## 🧪 Uji Coba

1. Buka browser, akses URL aplikasi lokal Anda (misal: `http://namaproject.test`).
2. Sistem otomatis akan mengalihkan ke halaman login SSO Server (`http://sso-kpi.test`).
3. Login dengan akun SSO.
4. Setelah berhasil, browser akan kembali ke aplikasi lokal dan pengguna langsung masuk ke dashboard.
5. Coba ubah role pengguna di **SSO Admin Portal** → refresh halaman di aplikasi lokal → role langsung berubah tanpa logout.
