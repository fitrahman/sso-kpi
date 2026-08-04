# 📖 Panduan Lengkap Integrasi Aplikasi Lokal dengan SSO Server KPI

Dokumen ini adalah **panduan standar dan referensi kode lengkap** untuk menyambungkan aplikasi lokal baru (Laravel, Node.js, Go, dsb.) dengan **SSO Server KPI (`sso-kpi.test`)**, termasuk fitur **Sinkronisasi Role Otomatis** dan **Webhook Real-Time (Tanpa Perlu Logout)**.

---

## 📌 1. Konfigurasi Environment (`.env`) Aplikasi Lokal

Tambahkan 4 variabel konfigurasi SSO pada file `.env` di project aplikasi lokal Anda:

```env
# ── Konfigurasi SSO Passport Server ──────────────────────────────────────────
SSO_HOST="http://sso-kpi.test"
SSO_CLIENT_ID="4"                                        # Dapatkan dari SSO Admin Portal
SSO_CLIENT_SECRET="SimpegKpiSecretKey998877665544332211" # Dapatkan dari SSO Admin Portal
SSO_REDIRECT_URI="http://namaproject.test/auth/sso/callback" # Masukkan http dari project yang ingin dimasukan
```

---

## 📌 2. Sinkronisasi Role Lokal Otomatis Saat Booting (`app/Providers/AppServiceProvider.php`)

Agar role lokal aplikasi Anda **secara otomatis terdaftar & muncul di Admin Portal SSO Server** saat aplikasi lokal dijalankan:

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
        // Kirim & sinkronkan daftar role lokal ke SSO Server
        if (env('SSO_HOST') && env('SSO_CLIENT_ID')) {
            try {
                Http::timeout(3)->post(env('SSO_HOST') . '/api/client-roles/sync', [
                    'client_id'     => (int) env('SSO_CLIENT_ID'),
                    'client_secret' => env('SSO_CLIENT_SECRET'),
                    'roles'         => ['admin', 'atasan', 'pegawai'], // Sesuaikan dengan daftar role lokal aplikasi ini
                ]);
            } catch (\Exception $e) {
                // Silent catch jika SSO Server sedang offline
            }
        }
    }
}
```

---

## 📌 3. Controller Handler Webhook Real-Time (`app/Http/Controllers/Auth/SsoWebhookController.php`)

Controller ini bertugas **menangkap sinyal perubahan role dari SSO Server secara real-time** dan langsung meng-update database aplikasi lokal:

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
            $rawRole = strtolower(trim($data['role'] ?? 'pegawai'));

            // Map role mentah dari SSO ke role valid aplikasi lokal
            if (in_array($rawRole, ['admin', 'superadmin', 'administrator'])) {
                $roleLokal = 'admin';
            } elseif (in_array($rawRole, ['atasan', 'manager', 'supervisor'])) {
                $roleLokal = 'atasan';
            } else {
                $roleLokal = 'pegawai';
            }

            // Update atau buat user secara real-time di DB aplikasi lokal
            $user = User::updateOrCreate(
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

---

## 📌 4. Rute Webhook Publik (`routes/api.php`)

Daftarkan endpoint webhook publik (tanpa middleware otentikasi sesi lokal):

```php
use App\Http\Controllers\Auth\SsoWebhookController;

// ── SSO Real-Time Webhook (Publik) ──────────────────────────────────────────
Route::post('/sso/webhook', [SsoWebhookController::class, 'handle']);
```

---

## 📌 5. Middleware Pembaruan Role Seketika (`app/Http/Middleware/EnsureUserHasRole.php`)

Agar perubahan role yang diterima via Webhook **langsung aktif di browser tanpa perlu logout**:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
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

---

## 📌 6. Login & Callback Controller (`app/Http/Controllers/Auth/LoginController.php`)

Controller otentikasi utama yang menangani redirect ke SSO Server, tukar token, provisioning pengguna awal, dan Single Logout:

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
     * 1. Redirect ke Halaman Login / Otorisasi SSO Server
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
     * 2. Receive Callback from SSO Server & Authenticate User
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

            // A. Tukar Code dengan Access Token
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

            // B. Ambil Profil & Role Lokal dari API SSO
            $userResponse = Http::withToken($accessToken)
                ->get($ssoHost . '/api/user', [
                    'client_id' => env('SSO_CLIENT_ID'),
                ]);

            if (!$userResponse->successful()) {
                return redirect('/login')->with('error', 'Gagal mengambil informasi profil pengguna.');
            }

            $ssoUserData = $userResponse->json();

            // C. Cek apakah user diizinkan mengakses aplikasi ini
            if (isset($ssoUserData['role']) && $ssoUserData['role'] === 'none') {
                return redirect('/login')->with('error', 'Anda tidak memiliki hak akses ke aplikasi ini.');
            }

            $roleLokal = strtolower($ssoUserData['role'] ?? 'pegawai');

            // D. Provisioning / Sync User di DB Aplikasi Lokal
            $user = User::updateOrCreate(
                ['email' => $ssoUserData['email']],
                [
                    'name'      => $ssoUserData['name'],
                    'role'      => $roleLokal,
                    'is_active' => true,
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            // E. Login Sesi Aplikasi Lokal
            Auth::login($user);
            $request->session()->put('sso_access_token', $accessToken);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan SSO: ' . $e->getMessage());
        }
    }

    /**
     * 3. Single Logout (SLO)
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

## 📌 7. Rute Otentikasi Web (`routes/web.php`)

Daftarkan rute otentikasi SSO pada aplikasi lokal:

```php
use App\Http\Controllers\Auth\LoginController;

// Rute SSO Auth
Route::get('/login', [LoginController::class, 'ssoRedirect'])->name('login');
Route::get('/auth/sso/callback', [LoginController::class, 'ssoCallback']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
```
