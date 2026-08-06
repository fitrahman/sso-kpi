# 📖 Panduan Lengkap Integrasi Aplikasi Lokal dengan SSO Server KPI

Dokumen ini adalah **panduan standar dari awal hingga akhir** untuk menyambungkan aplikasi lokal baru (Laravel) dengan **SSO Server KPI (`sso-kpi.test`)**, menggunakan metode **Socialite / OAuth2 Passport** dan **Sinkronisasi Role Berkala Real-time (CheckLocalRole Middleware - Tanpa Perlu Logout)** seperti yang diterapkan pada **Sistem 1, Sistem 2, dan Sistem 3**.

---

## 🗂️ Ringkasan File yang Perlu Dibuat / Dimodifikasi

| Langkah | File | Aksi |
|---------|------|------|
| 1 | **SSO Admin Portal** | Tambah aplikasi baru → Dapatkan Client ID & Secret |
| 2 | `config/services.php` | Daftarkan driver `laravelpassport` |
| 3 | `app/Providers/AppServiceProvider.php` | Daftarkan Socialite listener & auto-sync role saat boot |
| 4 | `app/Http/Middleware/CheckLocalRole.php` | Buat middleware polling role berkala (tiap 15 detik) |
| 5 | `bootstrap/app.php` | Daftarkan alias middleware `local.role` |
| 6 | `app/Http/Controllers/AuthController.php` | Buat controller login, callback (Socialite), & single logout |
| 7 | `routes/web.php` | Daftarkan rute `/login`, `/auth/sso/callback`, `/logout`, & proteksi rute |
| 8 | `.env` | Tambahkan variabel SSO (`SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, dll.) |

---

## 📌 Langkah 1: Daftarkan Aplikasi di SSO Admin Portal

1. Buka browser, akses: **`http://sso-kpi.test/admin/applications`**
2. Login sebagai Admin SSO (`admin@kpi.com` / `admin123`).
3. Klik tombol **"+ Tambah Aplikasi"** di pojok kanan atas.
4. Isi formulir:
   - **Nama Aplikasi:** Nama aplikasi Anda (misal: `Sistem 1`)
   - **Redirect URI / Callback URL:** `http://sistem1.test/auth/sso/callback`
   - **Role Lokal yang Didukung:** Pisahkan dengan koma (misal: `admin, atasan, pegawai`)
5. Klik **Simpan & Buat Aplikasi**.
6. **Salin Client ID dan Client Secret** dari notifikasi yang muncul.

---

## 📌 Langkah 2: Konfigurasi Services (`config/services.php`)

Daftarkan provider `laravelpassport` pada file `config/services.php` aplikasi lokal Anda:

```php
'laravelpassport' => [
    'client_id'     => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect'      => env('SSO_REDIRECT_URI'),
    'host'          => env('SSO_HOST', 'http://sso-kpi.test'),
],
```

---

## 📌 Langkah 3: AppServiceProvider (`app/Providers/AppServiceProvider.php`)

Tambahkan listener Socialite `laravelpassport` dan sinkronisasi role otomatis saat aplikasi di-boot:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Register Socialite Provider Laravel Passport
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('laravelpassport', \SocialiteProviders\LaravelPassport\Provider::class);
        });

        // 2. Auto-sync daftar role lokal ke SSO Server
        if (env('SSO_HOST') && env('SSO_CLIENT_ID')) {
            try {
                Http::timeout(3)->post(env('SSO_HOST') . '/api/client-roles/sync', [
                    'client_id'     => (int) env('SSO_CLIENT_ID'),
                    'client_secret' => env('SSO_CLIENT_SECRET'),
                    'roles'         => ['admin', 'atasan', 'pegawai'], // Sesuaikan dengan role lokal Anda
                ]);
            } catch (\Exception $e) {
                // Ignore jika SSO Server offline
            }
        }
    }
}
```

---

## 📌 Langkah 4: Middleware Sinkronisasi Berkala (`app/Http/Middleware/CheckLocalRole.php`)

Middleware ini secara otomatis mengecek perubahan role ke SSO Server **setiap 15 detik** tanpa mengganggu kecepatan aplikasi, dan langsung melakukan **force logout** jika hak akses dicabut:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CheckLocalRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Sinkronisasi berkala dengan SSO Server tiap 15 detik
            $lastSync = session('last_sso_sync');
            $accessToken = session('sso_access_token');
            
            if ($accessToken && (!$lastSync || now()->diffInSeconds($lastSync) > 15)) {
                try {
                    $ssoHost = config('services.laravelpassport.host', env('SSO_HOST', 'http://sso-kpi.test'));
                    $clientId = config('services.laravelpassport.client_id', env('SSO_CLIENT_ID'));
                    
                    $response = Http::withToken($accessToken)
                        ->timeout(3)
                        ->get($ssoHost . '/api/user', [
                            'client_id' => $clientId
                        ]);
                        
                    if ($response->successful()) {
                        $apiUser = $response->json();
                        
                        // Jika role = 'none', akses dicabut oleh Admin SSO! Force logout.
                        if (!isset($apiUser['role']) || $apiUser['role'] === 'none') {
                            Auth::logout();
                            $request->session()->invalidate();
                            $request->session()->regenerateToken();
                            return redirect()->route('access.denied')->with('error', 'Akses Anda ke aplikasi ini telah dicabut oleh Administrator.');
                        }
                        
                        // Perbarui data lokal jika role berubah di SSO
                        $newRole = strtolower($apiUser['role']);
                        if ($user->role !== $newRole || $user->name !== $apiUser['name']) {
                            $user->update([
                                'name' => $apiUser['name'],
                                'role' => $newRole,
                            ]);
                        }
                        
                        session(['last_sso_sync' => now()]);
                    }
                } catch (\Exception $e) {
                    // Abaikan koneksi jika offline
                }
            }
        }

        return $next($request);
    }
}
```

---

## 📌 Langkah 5: Registrasi Middleware (`bootstrap/app.php`)

Daftarkan alias middleware `local.role` di `bootstrap/app.php` (Laravel 11):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'local.role' => \App\Http\Middleware\CheckLocalRole::class,
    ]);
})
```

---

## 📌 Langkah 6: Controller Otentikasi (`app/Http/Controllers/AuthController.php`)

Buat `AuthController.php` untuk mengurus Socialite Login, Callback, & Single Logout:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * 1. Redirect ke SSO Server via Socialite
     */
    public function ssoRedirect()
    {
        return Socialite::driver('laravelpassport')->redirect();
    }

    /**
     * 2. Handle Callback dari SSO Server
     */
    public function ssoCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Otorisasi SSO dibatalkan.');
        }

        try {
            // Ambil user dari Socialite driver
            $ssoUser = Socialite::driver('laravelpassport')->user();
            $accessToken = $ssoUser->token;

            $ssoHost = config('services.laravelpassport.host', env('SSO_HOST'));
            $clientId = config('services.laravelpassport.client_id', env('SSO_CLIENT_ID'));

            // Request informasi user & role dari SSO API
            $response = Http::withToken($accessToken)
                ->timeout(5)
                ->get($ssoHost . '/api/user', [
                    'client_id' => $clientId
                ]);

            if ($response->failed()) {
                return redirect()->route('access.denied')->with('error', 'Gagal memverifikasi akun ke server SSO.');
            }

            $apiUser = $response->json();

            // Verifikasi hak akses (role tidak boleh 'none')
            if (!isset($apiUser['role']) || $apiUser['role'] === 'none') {
                return redirect()->route('access.denied')->with('error', 'Anda tidak memiliki hak akses untuk aplikasi ini.');
            }

            $roleLokal = strtolower($apiUser['role']);

            // Update atau buat user di DB lokal
            $user = User::updateOrCreate(
                ['email' => $apiUser['email']],
                [
                    'name'     => $apiUser['name'],
                    'role'     => $roleLokal,
                    'password' => Hash::make(Str::random(16)),
                ]
            );

            // Login user & simpan token + waktu sync
            Auth::login($user);
            $request->session()->put('sso_access_token', $accessToken);
            $request->session()->put('last_sso_sync', now());
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect()->route('access.denied')->with('error', 'Terjadi kesalahan SSO: ' . $e->getMessage());
        }
    }

    /**
     * 3. Single Logout (SLO)
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $ssoHost = config('services.laravelpassport.host', env('SSO_HOST', 'http://sso-kpi.test'));
        
        return redirect($ssoHost . '/sso-logout');
    }
}
```

---

## 📌 Langkah 7: Rute Otentikasi & Proteksi (`routes/web.php`)

Daftarkan rute SSO dan proteksi rute aplikasi lokal:

```php
use App\Http\Controllers\AuthController;

// ── SSO Public Routes ───────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'ssoRedirect'])->name('login');
Route::get('/auth/sso/callback', [AuthController::class, 'ssoCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/access-denied', fn () => view('access_denied'))->name('access.denied');

// ── Protected Routes (Menggunakan auth + local.role untuk polling sync) ─────
Route::middleware(['auth', 'local.role'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});
```

---

## 📌 Langkah 8: Konfigurasi Environment (`.env`)

Tambahkan variabel SSO ke file `.env` aplikasi lokal Anda:

```env
# ── Konfigurasi SSO Passport Server ──────────────────────────────────────────
SSO_HOST="http://sso-kpi.test"
SSO_CLIENT_ID="1"                                        # ← Dari SSO Admin Portal (Langkah 1)
SSO_CLIENT_SECRET="fX3dwIDhoAMRoOP40E7hW1ih5okJFcUu9Wbe4lao" # ← Dari SSO Admin Portal (Langkah 1)
SSO_REDIRECT_URI="http://sistem1.test/auth/sso/callback" # ← Sesuaikan dengan domain lokal Anda
```

Setelah mengisi `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📌 Langkah 9: Real-time SSO Webhook (Opsi Optimal - Asinkronus)

Setiap kali Administrator di portal SSO melakukan **pembaruan role** atau **pencabutan hak akses** pengguna, SSO Server akan mengirimkan HTTP POST request asinkronus langsung ke **Webhook URL** aplikasi klien Anda.

### 1. Struktur Payload Webhook

Webhook dikirim dengan format JSON sebagai berikut:

```json
{
  "event": "user.role_updated", // atau "user.access_revoked"
  "timestamp": 1799988222,
  "data": {
    "user_id": 42,
    "email": "nama.pegawai@kpi.go.id",
    "name": "Nama Pegawai",
    "role": "pegawai" // "none" jika akses dicabut
  }
}
```

### 2. Verifikasi Tanda Tangan Webhook (HMAC-SHA256)

Untuk memastikan request benar-benar berasal dari SSO Server KPI, SSO Server mengirimkan header `X-SSO-Signature`. Header ini adalah tanda tangan HMAC-SHA256 dari JSON string payload menggunakan **Webhook Secret** yang Anda daftarkan di SSO Admin Portal.

Contoh middleware verifikasi webhook di aplikasi klien Anda:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifyWebhookSignature
{
    public function handle(Request $request, Closure $next)
    {
        $signature = $request->header('X-SSO-Signature');
        $secret = env('SSO_WEBHOOK_SECRET');

        if (!$signature || !$secret) {
            return response()->json(['message' => 'Signature or Secret missing'], 401);
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($signature, $computedSignature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        return $next($request);
    }
}
```

Daftarkan rute webhook di `routes/api.php` aplikasi klien Anda:

```php
Route::post('/api/sso-webhook', [AuthController::class, 'handleSSOWebhook'])
    ->middleware(\App\Http\Middleware\VerifyWebhookSignature::class);
```

---

## ✅ Checklist Akhir

- [ ] Aplikasi sudah terdaftar di SSO Admin Portal (Langkah 1)
- [ ] Driver `laravelpassport` dikonfigurasi di `config/services.php`
- [ ] `AppServiceProvider` mendaftarkan Socialite listener & sync role saat boot
- [ ] Middleware `CheckLocalRole` dibuat & didaftarkan di `bootstrap/app.php`
- [ ] `AuthController` dibuat dengan Socialite redirect, callback, & single logout
- [ ] Rute diproteksi dengan `middleware(['auth', 'local.role'])`
- [ ] `.env` diisi dengan `SSO_HOST`, `SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, `SSO_REDIRECT_URI`
- [ ] `php artisan config:clear` sudah dijalankan

---

## 🧪 Uji Coba

1. Akses URL aplikasi lokal (misal `http://sistem1.test`).
2. Browser dialihkan ke SSO Server (`http://sso-kpi.test/login`).
3. Login dengan akun SSO.
4. Pengguna berhasil masuk ke dashboard aplikasi lokal.
5. Ubah role pengguna di **SSO Admin Portal** (`/admin/applications/{id}/users`).
6. Pindah halaman di aplikasi lokal → role pengguna otomatis ter-update dalam 15 detik **tanpa perlu logout**!
