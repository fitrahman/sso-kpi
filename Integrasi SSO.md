# 📖 Panduan Lengkap Integrasi Aplikasi Klien dengan SSO Server KPI

Dokumen ini adalah **panduan resmi dan menyeluruh** untuk menghubungkan aplikasi lokal (Laravel) dengan **SSO Server KPI (`sso-kpi.test`)** sebagai Identity Provider (IdP). Panduan ini mencakup **dua skenario**: aplikasi baru (*greenfield*) dan aplikasi lama yang sudah memiliki database pengguna sendiri (*legacy integration*).

---

## 📋 Daftar Isi Panduan

| Bagian | Topik |
|--------|-------|
| **A** | Cara Kerja Sistem — Memahami Alur Sebelum Memulai |
| **B** | Skenario Integrasi — Aplikasi Baru vs Aplikasi Lama (Legacy) |
| **C** | Langkah 1 — Daftarkan Aplikasi di SSO Admin Portal |
| **D** | Langkah 2 — Konfigurasi Environment (`.env`) |
| **E** | Langkah 3 — Konfigurasi Services (`config/services.php`) |
| **F** | Langkah 4 — Buat Auth Controller (Login, Callback, Logout) |
| **G** | Langkah 5 — Buat Middleware Pengaman Sesi |
| **H** | Langkah 6 — Daftarkan Middleware & Rute |
| **I** | Langkah 7 — Buat Webhook Controller (Sinkronisasi Real-time) |
| **J** | Langkah 8 — Daftar Peran ke SSO (Role Discovery) |
| **K** | Langkah 9 — Migrasi Data Pengguna Lama (Khusus Legacy) |
| **L** | Verifikasi & Pengujian |
| **M** | Troubleshooting Umum |

---

## A. 💡 Cara Kerja Sistem — Memahami Alur Sebelum Memulai

Sebelum mulai coding, pahami dulu **bagaimana SSO Server berkomunikasi dengan aplikasi klien Anda**. Terdapat **tiga jalur komunikasi utama**:

### Jalur 1: Login (OAuth2 Authorization Code Grant)

Ini adalah alur inti yang terjadi setiap kali pengguna login ke aplikasi Anda melalui SSO.

```
┌──────────┐      ┌──────────────────────┐     ┌──────────────────────┐
│ PENGGUNA │      │  APLIKASI KLIEN ANDA │     │   SSO SERVER KPI     │
│ (Browser)│      │  (Service Provider)  │     │  (Identity Provider) │
└────┬─────┘      └──────────┬───────────┘     └──────────┬───────────┘
     │                       │                            │
     │  1. Buka /login        │                            │
     │──────────────────────►│                            │
     │                       │  2. Redirect ke SSO         │
     │                       │    ?client_id=X            │
     │                       │    &redirect_uri=...       │
     │                       │    &response_type=code     │
     │                       │───────────────────────────►│
     │◄──────────────────────────────────────────────────│
     │  3. Tampilkan form login SSO                       │
     │                       │                            │
     │  4. Input email/password                           │
     │──────────────────────────────────────────────────►│
     │◄──────────────────────────────────────────────────│
     │  5. Redirect ke callback?code=[AUTH_CODE]          │
     │                       │                            │
     │  6. GET /callback?code │                            │
     │──────────────────────►│                            │
     │                       │  7. Tukar code dengan token│
     │                       │  POST /oauth/token         │
     │                       │───────────────────────────►│
     │                       │◄──────────────────────────│
     │                       │  { access_token: "..." }   │
     │                       │                            │
     │                       │  8. Ambil profil pengguna  │
     │                       │  GET /api/user?client_id=X │
     │                       │───────────────────────────►│
     │                       │◄──────────────────────────│
     │                       │  { name, email, role,      │
     │                       │    has_access: true/false }│
     │                       │                            │
     │                       │  9. Validasi has_access,   │
     │                       │     Map role, simpan user  │
     │◄──────────────────────│                            │
     │  10. Masuk ke Dashboard│                            │
```

### Jalur 2: Sinkronisasi Berkala (Middleware Polling)

Setiap 15–60 detik, selama pengguna browsing, middleware otomatis menghubungi SSO Server untuk memastikan akses belum dicabut.

```
[Pengguna buka halaman apapun]
        │
        ▼
[Middleware berjalan di setiap request]
        │
        ├─ Cek is_active lokal (DB) ──── false? ──────────► Logout paksa
        │
        └─ Sudah 15 detik sejak sync? ── Ya ─► Panggil /api/user ke SSO
                                                        │
                                               has_access = false? ─► Logout paksa
                                               role berubah? ─────► Update DB lokal
```

### Jalur 3: Notifikasi Real-time (Webhook Push)

Kapanpun admin SSO mengubah akses atau role pengguna, SSO Server langsung mengirimkan notifikasi ke endpoint Webhook aplikasi Anda.

```
[Admin SSO cabut akses pengguna X]
        │
        ▼
[SSO Server: kirim HTTP POST + Header X-SSO-Signature (HMAC-SHA256)]
        │
        ▼
[Aplikasi Klien: POST /api/sso/webhook]
        │
        ├── Verifikasi HMAC Signature
        ├── Update is_active = false di DB lokal
        │
        ▼
[Pengguna X buka halaman berikutnya]
        │
        ▼
[Middleware: is_active = false → Logout paksa]
```

> [!IMPORTANT]
> **Ketiga jalur di atas adalah lapisan keamanan berlapis.** Jika Webhook gagal terkirim, Middleware Polling masih mendeteksi pencabutan akses dalam 15 detik. Jika pengguna mencoba login ulang setelah dicabut, validasi `has_access` di Callback akan menolaknya.

---

## B. 🔀 Skenario Integrasi: Aplikasi Baru vs Aplikasi Lama

### Skenario 1: Aplikasi Baru (Tanpa Database Pengguna yang Ada)

Implementasi paling sederhana. Semua data pengguna akan diambil dari SSO Server dan disimpan di database lokal saat pertama kali login.

✅ Ikuti semua langkah dari **C hingga J**.

### Skenario 2: Aplikasi Lama (Sudah Ada Database Pengguna & Role Sendiri)

Ini skenario yang paling umum. Tujuannya adalah **menghubungkan akun lama dengan SSO** tanpa menghapus data yang sudah ada.

Strategi utamanya adalah **User Binding berdasarkan Email**:
- Email dari SSO **sudah ada** di database lama → **Hubungkan** akun (update token dan role).
- Email dari SSO **belum ada** di database lama → **Buat akun baru** (provisioning otomatis).

✅ Ikuti langkah **C hingga J**, lalu lanjutkan ke **K** (Migrasi Data Pengguna Lama).

Perbedaan utama pada `AuthController` skenario ini akan dijelaskan di **Langkah F**.

---

## C. 📌 Langkah 1: Daftarkan Aplikasi di SSO Admin Portal

**Tujuan:** Mendapatkan kredensial OAuth2 (`Client ID` & `Client Secret`) yang digunakan aplikasi Anda untuk berkomunikasi secara aman dengan SSO Server.

1. Buka browser, akses: **`http://sso-kpi.test/admin/applications`**
2. Login sebagai Admin SSO.
3. Klik tombol **"Tambah Aplikasi"**.
4. Isi formulir:

| Field | Contoh Nilai | Keterangan |
|-------|-------------|------------|
| **Nama Aplikasi** | `Sistem Kepegawaian` | Nama yang tampil di dashboard pengguna SSO |
| **Redirect URI** | `http://nama-apk.test/auth/sso/callback` | URL callback di aplikasi Anda. Harus persis sama |
| **Deskripsi** | `Sistem manajemen data pegawai` | Deskripsi singkat aplikasi |
| **Discovery URL** *(opsional)* | `http://nama-apk.test/api/sso/supported-roles` | URL tempat SSO Server menarik daftar role |
| **Discovery Secret** *(opsional)* | `string-rahasia-acak` | Kunci pengaman untuk Discovery URL |
| **Webhook URL** *(disarankan)* | `http://nama-apk.test/api/sso/webhook` | URL yang dipanggil SSO saat ada perubahan akses |

5. Klik **Simpan & Buat Aplikasi**.
6. **Catat `Client ID` dan `Client Secret`** yang ditampilkan. Secret ini hanya tampil sekali!

> [!CAUTION]
> Jangan pernah menyimpan `Client Secret` di repositori publik (GitHub). Gunakan file `.env` dan pastikan `.env` masuk ke `.gitignore`.

---

## D. 📌 Langkah 2: Konfigurasi Environment (`.env`)

**Tujuan:** Menyimpan kredensial SSO secara aman di file `.env` aplikasi klien Anda.

```env
# ── Konfigurasi SSO Server KPI ────────────────────────────────────────────────
SSO_HOST="http://sso-kpi.test"
SSO_CLIENT_ID="2"                               # ← Salin dari SSO Admin Portal
SSO_CLIENT_SECRET="isi-client-secret-anda"      # ← Salin dari SSO Admin Portal
SSO_REDIRECT_URI="http://nama-apk.test/auth/sso/callback"
SSO_DISCOVERY_SECRET="string-rahasia-discovery" # Harus sama dengan yang diisi di Langkah C
```

Setelah menyimpan `.env`:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## E. 📌 Langkah 3: Konfigurasi Services (`config/services.php`)

**Tujuan:** Membuat konfigurasi SSO dapat diakses di seluruh aplikasi menggunakan `config('services.sso.*')`.

```php
// config/services.php
return [
    // ... konfigurasi lainnya

    // ── SSO Server KPI ─────────────────────────────────────────────
    'sso' => [
        'host'          => env('SSO_HOST', 'http://sso-kpi.test'),
        'client_id'     => env('SSO_CLIENT_ID'),
        'client_secret' => env('SSO_CLIENT_SECRET'),
        'redirect_uri'  => env('SSO_REDIRECT_URI'),
    ],
];
```

---

## F. 📌 Langkah 4: Buat Auth Controller

**Tujuan:** Menangani tiga fungsi utama: (1) redirect ke SSO untuk login, (2) menerima callback dan memproses data pengguna, (3) logout dan Single Logout.

Buat file `app/Http/Controllers/Auth/SsoAuthController.php`:

### Versi A: Untuk Aplikasi Baru

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

class SsoAuthController extends Controller
{
    /**
     * LANGKAH 1: Redirect pengguna ke halaman login SSO Server.
     */
    public function redirect()
    {
        $query = http_build_query([
            'client_id'     => config('services.sso.client_id'),
            'redirect_uri'  => config('services.sso.redirect_uri'),
            'response_type' => 'code',
            'scope'         => '',
        ]);

        return redirect(config('services.sso.host') . '/oauth/authorize?' . $query);
    }

    /**
     * LANGKAH 2: Terima Authorization Code, tukar dengan token,
     * ambil profil pengguna, validasi akses, lalu buat sesi.
     */
    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect('/login')->with('error', 'Login SSO dibatalkan.');
        }

        $code = $request->get('code');
        if (!$code) {
            return redirect('/login')->with('error', 'Kode otorisasi tidak ditemukan.');
        }

        try {
            $ssoHost = config('services.sso.host');

            // ── TAHAP A: Tukar Authorization Code dengan Access Token ──
            $tokenResponse = Http::asForm()->post($ssoHost . '/oauth/token', [
                'grant_type'    => 'authorization_code',
                'client_id'     => config('services.sso.client_id'),
                'client_secret' => config('services.sso.client_secret'),
                'redirect_uri'  => config('services.sso.redirect_uri'),
                'code'          => $code,
            ]);

            if (!$tokenResponse->successful()) {
                return redirect('/login')->with('error', 'Gagal memverifikasi token SSO.');
            }

            $accessToken = $tokenResponse->json('access_token');

            // ── TAHAP B: Ambil Profil + Role + Status Akses Pengguna ──
            // Sertakan client_id agar SSO mengembalikan role SPESIFIK
            // untuk aplikasi ini, bukan role global pengguna.
            $userResponse = Http::withToken($accessToken)
                ->timeout(5)
                ->get($ssoHost . '/api/user', [
                    'client_id' => config('services.sso.client_id'),
                ]);

            if (!$userResponse->successful()) {
                return redirect('/login')->with('error', 'Gagal mengambil data pengguna dari SSO.');
            }

            $ssoUser = $userResponse->json();

            // ── TAHAP C: Validasi Hak Akses ───────────────────────────
            // has_access = false → pengguna belum diberi akses ke aplikasi
            // ini oleh Admin SSO, atau aksesnya sudah dicabut.
            $hasAccess = isset($ssoUser['has_access']) ? (bool) $ssoUser['has_access'] : false;
            if (!$hasAccess || ($ssoUser['role'] ?? '') === 'none') {
                return redirect('/login')
                    ->with('error', 'Anda tidak memiliki hak akses ke aplikasi ini. Hubungi Administrator.');
            }

            // ── TAHAP D: Petakan Role dari SSO ke Role Lokal ──────────
            // !! SESUAIKAN logika ini dengan role di aplikasi Anda !!
            $roleLokal = $this->mapRole($ssoUser['role'] ?? 'user');

            // ── TAHAP E: Simpan/Perbarui Pengguna di Database Lokal ───
            $user = User::updateOrCreate(
                ['email' => $ssoUser['email']],
                [
                    'name'      => $ssoUser['name'],
                    'role'      => $roleLokal,
                    'is_active' => true,
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            // ── TAHAP F: Buat Sesi Lokal ───────────────────────────────
            Auth::login($user);
            $request->session()->put('sso_access_token', $accessToken);
            $request->session()->put('last_sso_sync', now());
            $request->session()->regenerate();

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * LANGKAH 3: Hapus sesi lokal dan redirect ke SSO untuk Single Logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(config('services.sso.host') . '/sso-logout');
    }

    /**
     * Petakan role dari SSO ke role lokal aplikasi.
     * !! SESUAIKAN FUNGSI INI dengan struktur role di aplikasi Anda !!
     */
    public function mapRole(string $ssoRole): string
    {
        $ssoRole = strtolower(trim($ssoRole));

        // Contoh pemetaan — ubah sesuai kebutuhan Anda:
        $peta = [
            'admin'            => 'admin',
            'administrator'    => 'admin',
            'manajerial'       => 'manager',
            'kepegawaian'      => 'staff',
            'humas'            => 'staff',
            'visualisasi data' => 'staff',
            'hukum'            => 'staff',
            'pengawasan siaran'=> 'staff',
        ];

        return $peta[$ssoRole] ?? 'user'; // Default fallback
    }
}
```

---

### Versi B: Untuk Aplikasi Lama (Legacy — Ada Database Pengguna Sendiri)

Perbedaan utama ada di **TAHAP E**. Ganti blok TAHAP E dengan kode berikut:

```php
// ── TAHAP E (Versi Legacy): User Binding berdasarkan Email ──────

$userLama = User::where('email', $ssoUser['email'])->first();

if ($userLama) {
    // Akun SUDAH ADA di database lama → Hubungkan dengan SSO
    // Hanya perbarui data yang relevan, data lain (NIP, departemen, dll.) AMAN
    $userLama->update([
        'name'      => $ssoUser['name'],  // Sync nama terbaru dari SSO
        'role'      => $roleLokal,         // Sync role setelah di-mapping
        'is_active' => true,
        // OPSIONAL: simpan ID pengguna SSO untuk referensi silang
        // 'sso_user_id' => $ssoUser['id'],
    ]);
    $user = $userLama;

} else {
    // Akun BELUM ADA di database lama → Buat akun baru (provisioning)
    // Data transaksi lama aman, hanya akun baru yang dibuat
    $user = User::create([
        'name'      => $ssoUser['name'],
        'email'     => $ssoUser['email'],
        'role'      => $roleLokal,
        'is_active' => true,
        'password'  => Hash::make(Str::random(16)),
        // Tambahkan field default lain yang wajib ada di aplikasi Anda:
        // 'department_id' => null,
        // 'nip'           => null,
    ]);
}

// Lanjutkan dengan membuat sesi (sama seperti Versi A TAHAP F)
Auth::login($user);
```

> [!IMPORTANT]
> **Kunci keamanan:** Selalu gunakan `email` sebagai kunci pencocokan, bukan `name`. Email bersifat unik dan konsisten. Pastikan kolom `email` di tabel `users` memiliki constraint `UNIQUE`.

---

## G. 📌 Langkah 5: Buat Middleware Pengaman Sesi

**Tujuan:** Memeriksa status akses pengguna di setiap request HTTP dengan dua lapis perlindungan.

Buat file `app/Http/Middleware/SsoSessionGuard.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Auth\SsoAuthController;

class SsoSessionGuard
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // ── LAPIS 1: Cek Database Lokal (Instan, tanpa request HTTP) ──
        // Kolom 'is_active' diperbarui oleh Webhook saat admin mencabut akses.
        if (isset($user->is_active) && !$user->is_active) {
            $this->forceLogout($request);
            return redirect('/login')
                ->with('error', 'Akses Anda ke aplikasi ini telah dicabut oleh Administrator.');
        }

        // ── LAPIS 2: Sinkronisasi Berkala dengan SSO Server (15 detik) ─
        $accessToken = session('sso_access_token');
        $lastSync    = session('last_sso_sync');
        $perluSync   = !$lastSync || now()->diffInSeconds($lastSync) > 15;

        if ($accessToken && $perluSync) {
            try {
                $ssoHost  = config('services.sso.host');
                $clientId = config('services.sso.client_id');

                $response = Http::withToken($accessToken)
                    ->timeout(3)
                    ->get($ssoHost . '/api/user', ['client_id' => $clientId]);

                if ($response->successful()) {
                    $ssoData = $response->json();

                    // Cek apakah akses masih valid
                    if (!($ssoData['has_access'] ?? false) || ($ssoData['role'] ?? '') === 'none') {
                        $this->forceLogout($request);
                        return redirect('/login')
                            ->with('error', 'Akses Anda ke aplikasi ini telah dicabut.');
                    }

                    // Sinkronisasi nama dan role jika ada perubahan
                    $controller = new SsoAuthController();
                    $roleLokal  = $controller->mapRole($ssoData['role'] ?? 'user');
                    $updates    = [];

                    if ($user->name !== ($ssoData['name'] ?? $user->name)) {
                        $updates['name'] = $ssoData['name'];
                    }
                    if (isset($user->role) && $user->role !== $roleLokal) {
                        $updates['role'] = $roleLokal;
                    }
                    if (!empty($updates)) {
                        $user->update($updates);
                    }

                    session(['last_sso_sync' => now()]);

                } elseif ($response->status() === 401) {
                    // Token kedaluwarsa
                    $this->forceLogout($request);
                    return redirect('/login')
                        ->with('error', 'Sesi Anda telah berakhir. Silakan login kembali.');
                }

            } catch (\Exception $e) {
                // SSO Server tidak bisa dijangkau → biarkan sesi tetap berjalan.
                // Jangan paksa logout hanya karena koneksi bermasalah sesaat.
            }
        }

        return $next($request);
    }

    private function forceLogout(Request $request): void
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
```

> [!TIP]
> Sesuaikan nilai **15 detik** dengan kebutuhan. Nilai lebih kecil = lebih real-time tapi lebih banyak request ke SSO. Nilai lebih besar = lebih sedikit request tapi jeda deteksi pencabutan lebih lama.

---

## H. 📌 Langkah 6: Daftarkan Middleware & Rute

### H.1 — Daftarkan Alias Middleware (`bootstrap/app.php`)

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'sso.guard'  => \App\Http\Middleware\SsoSessionGuard::class,
        'sso.secret' => \App\Http\Middleware\VerifySsoSecret::class,
    ]);
})
```

### H.2 — Daftarkan Rute (`routes/web.php` dan `routes/api.php`)

```php
// routes/web.php
use App\Http\Controllers\Auth\SsoAuthController;

// ── Rute Publik SSO ───────────────────────────────────────────────────────────
Route::get('/login', [SsoAuthController::class, 'redirect'])->name('login');
Route::get('/auth/sso/callback', [SsoAuthController::class, 'callback'])->name('sso.callback');
Route::post('/logout', [SsoAuthController::class, 'logout'])->name('logout');
Route::get('/access-denied', fn () => view('errors.access_denied'))->name('access.denied');

// ── Rute yang Dilindungi ──────────────────────────────────────────────────────
Route::middleware(['auth', 'sso.guard'])->group(function () {
    Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');
    // ... tambahkan rute lain di sini
});
```

```php
// routes/api.php
use App\Http\Controllers\SsoWebhookController;

// ── Webhook dari SSO Server (TANPA 'auth', TANPA CSRF) ────────────────────────
// URL Webhook lengkap: http://nama-apk.test/api/sso/webhook
Route::post('/sso/webhook', [SsoWebhookController::class, 'handle']);

// ── Discovery API: Daftar role yang didukung aplikasi ini ─────────────────────
// Dilindungi sso.secret — hanya bisa diakses oleh SSO Server
Route::middleware('sso.secret')->get('/sso/supported-roles', function () {
    return response()->json([
        'app_name' => config('app.name'),
        'roles'    => [
            // !! Sesuaikan dengan role yang ada di aplikasi Anda !!
            ['key' => 'admin',   'label' => 'Administrator', 'level' => 3],
            ['key' => 'manager', 'label' => 'Manajer',       'level' => 2],
            ['key' => 'staff',   'label' => 'Staff',         'level' => 1],
            ['key' => 'user',    'label' => 'Pengguna',      'level' => 0],
        ]
    ]);
});
```

---

## I. 📌 Langkah 7: Buat Webhook Controller

**Tujuan:** Menerima dan memproses notifikasi real-time dari SSO Server saat ada perubahan akses atau role.

Buat file `app/Http/Controllers/SsoWebhookController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\SsoAuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SsoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── KEAMANAN: Verifikasi HMAC-SHA256 Signature ───────────────
        // SSO Server menyertakan header X-SSO-Signature berisi hash dari
        // payload JSON menggunakan shared secret (SSO_DISCOVERY_SECRET).
        $secret = env('SSO_DISCOVERY_SECRET');
        if ($secret) {
            $receivedSig = $request->header('X-SSO-Signature');
            $expectedSig = hash_hmac('sha256', $request->getContent(), $secret);

            if (!hash_equals($expectedSig, (string) $receivedSig)) {
                Log::warning('SSO Webhook: Signature tidak valid.', ['ip' => $request->ip()]);
                return response()->json(['success' => false, 'message' => 'Invalid signature.'], 401);
            }
        }

        $event = $request->input('event');
        $data  = $request->input('data');

        Log::info('SSO Webhook diterima:', ['event' => $event, 'email' => $data['email'] ?? null]);

        if (empty($data['email'])) {
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap.'], 400);
        }

        // ── EVENT 1: Akses Pengguna Dicabut ───────────────────────────
        // Terjadi saat admin SSO menonaktifkan akun atau mencabut akses
        // pengguna dari aplikasi ini secara spesifik.
        if ($event === 'user.access_revoked') {
            $user = User::where('email', $data['email'])->first();
            if ($user) {
                $user->update(['is_active' => false]);
                Log::info("Webhook: Pengguna {$data['email']} dinonaktifkan.");
            }
            return response()->json(['success' => true]);
        }

        // ── EVENT 2: Role atau Status Akses Pengguna Diperbarui ───────
        // Terjadi saat admin SSO mengubah role pengguna di aplikasi ini,
        // atau saat memberikan/mencabut akses melalui halaman edit pengguna.
        if ($event === 'user.role_updated') {
            $rawRole      = strtolower(trim($data['role'] ?? 'user'));
            $accessStatus = strtolower(trim($data['access_status'] ?? 'approved'));

            // Pengguna aktif hanya jika access_status bukan 'rejected' atau 'none'
            $isActive = !in_array($accessStatus, ['rejected', 'none', 'revoked']);

            // Petakan role SSO ke role lokal menggunakan fungsi yang sama dengan AuthController
            $controller = new SsoAuthController();
            $roleLokal  = $controller->mapRole($rawRole);

            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name'      => $data['name'] ?? $data['email'],
                    'role'      => $roleLokal,
                    'is_active' => $isActive,
                    'password'  => Hash::make(Str::random(16)),
                ]
            );

            Log::info("Webhook: Role {$data['email']} → '{$roleLokal}' (aktif: " . ($isActive ? 'ya' : 'tidak') . ")");
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Event tidak dikenali, diabaikan.']);
    }
}
```

### Middleware VerifySsoSecret

Buat file `app/Http/Middleware/VerifySsoSecret.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySsoSecret
{
    public function handle(Request $request, Closure $next)
    {
        $secret       = env('SSO_DISCOVERY_SECRET');
        $headerSecret = $request->header('X-SSO-Secret');

        if (!$secret || $headerSecret !== $secret) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 401);
        }

        return $next($request);
    }
}
```

---

## J. 📌 Langkah 8: Daftarkan Peran ke SSO (Role Discovery)

**Tujuan:** Memberitahu SSO Server peran apa saja yang ada di aplikasi Anda, agar Administrator SSO dapat memilihnya saat menetapkan akses pengguna.

### Sinkronisasi Otomatis via Discovery URL

Jika Anda sudah mendaftarkan `Discovery URL` saat registrasi aplikasi di Admin Portal SSO, SSO Server akan menarik daftar peran secara otomatis dari endpoint `/api/sso/supported-roles` yang Anda buat di Langkah H ketika administrator menekan tombol **Sync Peran** di portal SSO.

> [!NOTE]
> Pastikan SSO Server bisa mengakses URL Discovery aplikasi Anda dari jaringannya. Di lingkungan lokal Laragon, pastikan semua domain sudah terdaftar di Virtual Host.

---

## K. 📌 Langkah 9: Migrasi Data Pengguna Lama *(Khusus Skenario Legacy)*

**Tujuan:** Mendaftarkan pengguna yang sudah ada di database lama ke SSO Server.

> [!TIP]
> **Cara termudah:** Tidak perlu migrasi massal. Biarkan pengguna lama login pertama kali via SSO secara alami. Sistem **Versi B** di Langkah F akan otomatis menghubungkan akun mereka berdasarkan email. Administrator SSO hanya perlu memberikan akses ke pengguna tersebut dari dashboard SSO.

Jika tetap ingin migrasi massal, tambahkan kolom `sso_user_id` (nullable) di tabel `users` aplikasi lama untuk menyimpan referensi ID pengguna di SSO Server, lalu jalankan skrip impor melalui fitur **Import Pengguna** di Dashboard Admin SSO atau melalui seeder kustom.

---

## L. ✅ Verifikasi & Pengujian

| No | Skenario | Hasil yang Diharapkan |
|----|----------|-----------------------|
| 1 | Buka `/login` di aplikasi klien | Diarahkan ke halaman login SSO Server |
| 2 | Login dengan akun tanpa akses | Pesan "Anda tidak memiliki hak akses" |
| 3 | Login dengan akun yang diberi akses | Berhasil masuk ke dashboard |
| 4 | Cek kolom `role` di DB lokal | Sesuai dengan role yang ditetapkan di SSO, sudah ter-mapping |
| 5 | Admin SSO cabut akses → Buka halaman baru | Pengguna di-logout dalam ≤15 detik |
| 6 | Webhook `user.access_revoked` masuk | `is_active = false` di DB lokal segera |
| 7 | Pengguna coba login ulang setelah dicabut | Ditolak oleh validasi `has_access` |
| 8 | Klik tombol Logout | Diarahkan ke SSO Server, sesi SSO juga dihapus |
| 9 | Panggil `/api/sso/supported-roles` tanpa secret | Respons `401 Unauthorized` |
| 10 | Panggil `/api/sso/supported-roles` dengan secret | Daftar role dalam format JSON |

---

## M. 🔧 Troubleshooting Umum

### ❌ `redirect_uri does not match`
Nilai `SSO_REDIRECT_URI` di `.env` tidak persis sama dengan yang didaftarkan di SSO Admin Portal. Pastikan identik karakter per karakter, termasuk ada tidaknya `/` di akhir.

### ❌ `invalid_client`
`SSO_CLIENT_ID` atau `SSO_CLIENT_SECRET` salah. Salin ulang dari SSO Admin Portal. Perhatikan spasi tersembunyi di awal/akhir nilai.

### ❌ `has_access` selalu `false`
Administrator belum memberikan akses aplikasi ini kepada pengguna tersebut di dashboard SSO. Login sebagai admin → edit pengguna → centang aplikasi Anda → simpan.

### ❌ Webhook tidak pernah diterima
Pastikan Queue Worker SSO Server berjalan (`php artisan queue:work`) dan URL Webhook bisa diakses dari server SSO (cek firewall dan resolusi domain).

### ❌ Semua pengguna di-logout setiap 15 detik
Pastikan blok `catch` di middleware **tidak** memanggil `forceLogout`. Jika SSO Server sedang offline sementara, sesi pengguna harus tetap berjalan.

---

*Dokumen ini diperbarui terakhir: Agustus 2026 | SSO Server KPI v1.0*

