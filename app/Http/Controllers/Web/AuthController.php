<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show login/register form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $credentials = $request->only('email', 'password');

            if (! Auth::attempt($credentials)) {
                return back()
                    ->withErrors(['email' => 'Email atau kata sandi yang Anda masukkan salah.'])
                    ->withInput();
            }

            $user = Auth::user();
            if ($user->status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun Anda masih menunggu persetujuan dari Administrator.'])
                    ->withInput();
            } elseif ($user->status === 'inactive') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()
                    ->withErrors(['email' => 'Akun Anda telah dinonaktifkan oleh Administrator.'])
                    ->withInput();
            }

            $request->session()->regenerate();

            return redirect()->intended('dashboard');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Gagal masuk: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'string', \Illuminate\Validation\Rules\Password::min(8)->letters()->mixedCase()->numbers()->symbols(), 'confirmed'],
                'phone' => 'nullable|string|max:20',
                'role' => 'required|string|in:'.implode(',', User::ROLES),
                'email_verified_at' => 'nullable|date',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => 'pending',
                'email_verified_at' => $request->email_verified_at ? Carbon::parse($request->email_verified_at) : now(),
            ]);

            return redirect()->route('register.pending')
                ->with('success', 'Registration successful! Waiting for approval.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Registration failed: '.$e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->tokens()->each(function ($token) {
                    $token->revoke();
                });
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', 'Logged out successfully!');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Logout failed: '.$e->getMessage()]);
        }
    }

    /**
     * Handle SSO Logout request (from client applications)
     */
    public function ssoLogout(Request $request)
    {
        try {
            $user = Auth::user();
            if ($user) {
                $user->tokens()->each(function ($token) {
                    $token->revoke();
                });
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $redirectUrl = $request->query('redirect', route('login'));

            return redirect($redirectUrl)->with('success', 'Berhasil logout dari sistem SSO.');
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Logout gagal: '.$e->getMessage()]);
        }
    }
}
