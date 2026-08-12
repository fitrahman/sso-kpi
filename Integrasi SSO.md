# 📖 Panduan Lengkap Integrasi Aplikasi Lokal dengan SSO Server KPI

Dokumen ini adalah **panduan standar dari awal hingga akhir** untuk menyambungkan aplikasi lokal baru (Laravel) dengan **SSO Server KPI (`sso-kpi.test`)**, menggunakan metode **Socialite / OAuth2 Passport**, **Single Log Out (SLO)**, dan **Role Discovery API**.

---

## 🗂️ Ringkasan File yang Perlu Dibuat / Dimodifikasi

| Langkah | File | Aksi |
|---------|------|------|
| 1 | **SSO Admin Portal** | Tambah aplikasi baru → Dapatkan Client ID & Secret |
| 2 | `config/services.php` | Daftarkan driver `laravelpassport` |
| 3 | `app/Providers/AppServiceProvider.php` | Daftarkan Socialite listener |
| 4 | `app/Http/Middleware/CheckLocalRole.php` | Buat middleware polling role & SLO handling |
| 5 | `bootstrap/app.php` | Daftarkan alias middleware `local.role` & `sso.secret` |
| 6 | `app/Http/Middleware/VerifySsoSecret.php` | Buat middleware pengaman Discovery API |
| 7 | `app/Http/Controllers/AuthController.php` | Buat controller login, callback, & single logout |
| 8 | `routes/web.php` | Daftarkan rute `/login`, `/auth/sso/callback`, `/logout`, & Discovery API |
| 9 | `.env` | Tambahkan variabel SSO (`SSO_CLIENT_ID`, `SSO_CLIENT_SECRET`, dll.) |
| 10 | `app/Http/Controllers/SsoWebhookController.php` | Buat controller penerima Webhook (Real-time Sync & Deactivation) |

---

## 📌 Langkah 1: Daftarkan Aplikasi di SSO Admin Portal

1. Buka browser, akses: **`http://sso-kpi.test/admin/applications`**
2. Login sebagai Admin SSO (`admin@kpi.com` / `password123`).
3. Klik tombol **"Tambah Aplikasi"** di pojok kanan atas.
4. Isi formulir:
   - **Nama Aplikasi:** Nama aplikasi Anda (misal: `Sistem 1`)
   - **Redirect URI / Callback URL:** `http://sistem1.test/auth/sso/callback`
   - **Supported Roles Discovery URL (Opsional):** `http://sistem1.test/api/sso/supported-roles`
   - **SSO Discovery Secret (Opsional):** String rahasia yang sama dengan `.env` aplikasi Anda.
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

Tambahkan listener Socialite `laravelpassport` agar Socialite mengenali driver OAuth2 SSO Server:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Register Socialite Provider Laravel Passport
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('laravelpassport', \SocialiteProviders\LaravelPassport\Provider::class);
        });
    }
}
```

---

## 📌 Langkah 4: Middleware Redis Session Caching & Webhooks (Tanpa Synchronous Polling)

Untuk menghindari latensi HTTP *synchronous polling* di setiap request web, manfaatkan **Cache/Redis** lokal dengan *TTL (Time-To-Live)* singkat (misal 60 detik) atau gunakan *Back-channel Logout Webhook*:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class CheckLocalRole
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $accessToken = session('sso_access_token');
            
            if ($accessToken) {
                $cacheKey = 'sso_user_status_' . $user->id;

                // Cache status user selama 60 detik di Redis/Cache lokal untuk performa tinggi
                $apiUser = Cache::remember($cacheKey, 60, function () use ($accessToken) {
                    try {
                        $ssoHost = config('services.laravelpassport.host', 'http://sso-kpi.test');
                        $clientId = config('services.laravelpassport.client_id');
                        
                        $response = Http::withToken($accessToken)
                            ->timeout(2)
                            ->get($ssoHost . '/api/v1/user', [
                                'client_id' => $clientId
                            ]);
                            
                        if ($response->successful()) {
                            return $response->json();
                        } elseif ($response->status() === 401) {
                            return 'unauthorized';
                        }
                    } catch (\Exception $e) {
                        return null; // Fallback jika SSO offline
                    }
                    return null;
                });

                if ($apiUser === 'unauthorized') {
                    Cache::forget($cacheKey);
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('error', 'Sesi SSO Anda telah berakhir.');
                }

                if (is_array($apiUser)) {
                    if (!isset($apiUser['role']) || $apiUser['role'] === 'none' || !(bool)($apiUser['has_access'] ?? true)) {
                        Cache::forget($cacheKey);
                        Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return redirect()->route('access.denied')->with('error', 'Akses Anda telah dicabut.');
                    }

                    $newRole = ucfirst($apiUser['role']);
                    if ($user->global_role !== $apiUser['role'] || $user->local_role !== $newRole) {
                        $user->update([
                            'name' => $apiUser['name'],
                            'global_role' => $apiUser['role'],
                            'local_role' => $newRole,
                        ]);
                    }
                }
            }
        }

        return $next($request);
    }
}
```

---

## 📌 Langkah 5: Registrasi Middleware (`bootstrap/app.php`)

Daftarkan alias middleware `local.role` dan `sso.secret` di `bootstrap/app.php` (Laravel 11/12):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'local.role' => \App\Http\Middleware\CheckLocalRole::class,
        'sso.secret' => \App\Http\Middleware\VerifySsoSecret::class,
    ]);
})
```

---

## 📌 Langkah 6: Middleware Pengaman Discovery API (`app/Http/Middleware/VerifySsoSecret.php`)

Buat middleware untuk membatasi endpoint Supported Roles agar hanya bisa dibaca oleh SSO Server:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifySsoSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $secret = env('SSO_DISCOVERY_SECRET');
        $headerSecret = $request->header('X-SSO-Secret');

        if (!$secret || $headerSecret !== $secret) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid SSO Secret.',
            ], 401);
        }

        return $next($request);
    }
}
```

---

## 📌 Langkah 7: Controller Otentikasi (`app/Http/Controllers/AuthController.php`)

Buat `AuthController.php` untuk mengurus Socialite Login, Callback, & Single Logout.
*PENTING: Jangan gunakan `guest` middleware pada route `/login` atau `/auth/sso/callback` agar re-login berganti akun dari SSO Portal berjalan mulus.*

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
    public function ssoRedirect()
    {
        return Socialite::driver('laravelpassport')->redirect();
    }

    public function ssoCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Otorisasi SSO dibatalkan.');
        }

        try {
            $ssoUser = Socialite::driver('laravelpassport')->user();
            $accessToken = $ssoUser->token;

            $ssoHost = config('services.laravelpassport.host', env('SSO_HOST'));
            $clientId = config('services.laravelpassport.client_id');

            $response = Http::withToken($accessToken)
                ->timeout(5)
                ->get($ssoHost . '/api/v1/user', [
                    'client_id' => $clientId
                ]);

            if ($response->failed()) {
                return redirect()->route('access.denied')->with('error', 'Gagal memverifikasi akun ke server SSO.');
            }

            $apiUser = $response->json();

            // Verifikasi apakah user memiliki hak akses aktif (has_access must be true)
            $hasAccess = isset($apiUser['has_access']) ? (bool) $apiUser['has_access'] : false;
            if (!$hasAccess) {
                return redirect()->route('access.denied')->with('error', 'Anda tidak memiliki hak akses untuk aplikasi ini.');
            }

            // Simpan role ke database lokal
            $user = User::updateOrCreate(
                ['email' => $apiUser['email']],
                [
                    'name'        => $apiUser['name'],
                    'sso_user_id' => $apiUser['id'],
                    'global_role' => $apiUser['role'] ?? 'user',
                    'local_role'  => ucfirst($apiUser['role'] ?? 'user'),
                    'password'    => Hash::make(Str::random(16)),
                ]
            );

            Auth::login($user);
            $request->session()->put('sso_access_token', $accessToken);
            $request->session()->put('last_sso_sync', now());
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect()->route('access.denied')->with('error', 'Terjadi kesalahan SSO: ' . $e->getMessage());
        }
    }

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

## 📌 Langkah 8: Rute Otentikasi & Discovery API (`routes/web.php` atau `routes/api.php`)

Daftarkan rute SSO dan endpoint Discovery API di file rute aplikasi lokal Anda:

```php
use App\Http\Controllers\AuthController;

// ── SSO Public Routes ───────────────────────────────────────────────────────
Route::get('/login', [AuthController::class, 'ssoRedirect'])->name('login');
Route::get('/auth/sso/callback', [AuthController::class, 'ssoCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/access-denied', fn () => view('access_denied'))->name('access.denied');

// ── SSO Supported Roles Discovery API ───────────────────────────────────────
Route::middleware('sso.secret')->get('/api/sso/supported-roles', function () {
    return response()->json([
        'app_name' => 'Sistem 1', // Ganti dengan nama aplikasi Anda
        'roles' => [
            ['key' => 'Admin', 'label' => 'Admin', 'level' => 3],
            ['key' => 'Atasan', 'label' => 'Atasan', 'level' => 2],
            ['key' => 'Pegawai', 'label' => 'Pegawai', 'level' => 1]
        ]
    ]);
});

// ── Protected Routes ────────────────────────────────────────────────────────
Route::middleware(['auth', 'local.role'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
});
```

---

## 📌 Langkah 9: Konfigurasi Environment (`.env`)

Tambahkan variabel SSO ke file `.env` aplikasi lokal Anda:

```env
# ── Konfigurasi SSO Passport Server ──────────────────────────────────────────
SSO_HOST="http://sso-kpi.test"
SSO_CLIENT_ID="1"                                              # ← Dari SSO Admin Portal (Langkah 1)
SSO_CLIENT_SECRET="fX3dwIDhoAMRoOP40E7hW1ih5okJFcUu9Wbe4lao"       # ← Dari SSO Admin Portal (Langkah 1)
SSO_REDIRECT_URI="http://sistem1.test/auth/sso/callback"       # ← Sesuaikan dengan domain lokal Anda
SSO_DISCOVERY_SECRET="super_secret_discovery_key_2026"         # ← Sesuai dengan yang diisi di Langkah 1
```

Setelah mengisi `.env`, jalankan:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## 📌 Langkah 10: Integrasi Webhook (Real-time Sync & Deactivation)

Webhook digunakan agar ketika Administrator memodifikasi akses atau peran pengguna di Dashboard SSO, aplikasi lokal Anda langsung memperbarui data penggunanya secara real-time tanpa menunggu sesi lokal habis.

### A. Buat Webhook Controller (`app/Http/Controllers/SsoWebhookController.php`)

Buatlah berkas controller untuk memproses data event yang dikirimkan oleh SSO Server:

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SsoWebhookController extends Controller
{
    /**
     * Handle incoming real-time Webhook dari SSO Server
     */
    public function handle(Request $request)
    {
        $event = $request->input('event');
        $data  = $request->input('data');

        Log::info('SSO Webhook Received:', [
            'event' => $event,
            'data'  => $data,
        ]);

        if (empty($data['email'])) {
            return response()->json(['success' => false, 'message' => 'Missing user email.'], 400);
        }

        // 1. Menangani pemblokiran akses langsung (user.access_revoked)
        if ($event === 'user.access_revoked') {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $user->update(['is_active' => false]);
            }
            return response()->json([
                'success' => true, 
                'message' => "User {$data['email']} deactivated successfully."
            ]);
        }

        // 2. Menangani pembaruan peran & status akses (user.role_updated)
        if ($event === 'user.role_updated') {
            $rawRole = strtolower(trim($data['role'] ?? 'pegawai'));
            
            // Periksa status akses (access_status)
            $accessStatus = strtolower(trim($data['access_status'] ?? 'approved'));
            $isActive = !in_array($accessStatus, ['rejected', 'none']);

            // Petakan role kiriman SSO ke role lokal aplikasi Anda
            if (in_array($rawRole, ['admin', 'superadmin', 'administrator'])) {
                $roleLokal = 'admin';
            } elseif (in_array($rawRole, ['atasan', 'manager', 'supervisor'])) {
                $roleLokal = 'atasan';
            } else {
                $roleLokal = 'pegawai';
            }

            // Simpan perubahan secara lokal
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'] ?? $data['email'],
                    'role'      => $roleLokal,
                    'is_active' => $isActive,
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            Log::info("User Role updated to {$roleLokal} for {$data['email']} (is_active: " . ($isActive ? 'true' : 'false') . ")");

            return response()->json([
                'success' => true,
                'message' => "Role for {$data['email']} updated in real-time to {$roleLokal}",
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Unhandled webhook event.'], 200);
    }
}
```

### B. Daftarkan Rute Webhook Tanpa CSRF Protection

Daftarkan rute webhook Anda. Agar tidak terkena proteksi CSRF token (karena dipanggil oleh server SSO eksternal), daftarkan rute ini pada **`routes/api.php`** (sehingga otomatis mendapat prefix `/api` dan melewati middleware `web`):

```php
use App\Http\Controllers\SsoWebhookController;

Route::post('/sso/webhook', [SsoWebhookController::class, 'handle']);
```

> [!NOTE]
> URL Webhook lengkap yang harus Anda daftarkan di SSO Admin Portal (Langkah 1) pada bagian **Webhook URL** adalah:
> `http://sistem1.test/api/sso/webhook`

