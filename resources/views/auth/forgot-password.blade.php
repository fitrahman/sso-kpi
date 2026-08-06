<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Kata Sandi - KPI SSO Portal</title>
    <link rel="icon" type="image/png" href="{{ asset('logoKPI.png') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        kpi: {
                            50: '#fef2f2', 100: '#fee2e2', 500: '#ef4444', 600: '#dc2626',
                            700: '#b91c1c', 800: '#991b1b', 900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .peer:focus ~ .peer-focus\:scale-75,
        .peer:not(:placeholder-shown) ~ .peer-focus\:scale-75 {
            transform: scale(0.75) translateY(-1.5rem);
            color: #b91c1c;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left: Illustration Panel -->
        <div class="hidden md:flex md:w-1/2 bg-kpi-700 bg-pattern flex-col justify-center items-center p-12 relative overflow-hidden text-white text-center">
            <div class="absolute inset-0 bg-kpi-800/90 mix-blend-multiply"></div>
            <div class="relative z-10 w-full max-w-sm flex flex-col items-center">
                <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-24 w-auto mb-8">
                <h2 class="text-3xl font-bold mb-4 tracking-tight">Atur Ulang Kata Sandi</h2>
                <p class="text-kpi-100 text-base leading-relaxed">
                    Kami akan membantu memulihkan akses akun SSO Anda secara aman dan cepat.
                </p>
            </div>
            
            <!-- Decorative circles -->
            <div class="absolute -bottom-24 -left-24 w-64 h-64 border-4 border-white/10 rounded-full"></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 border-4 border-white/10 rounded-full"></div>
        </div>

        <!-- Right: Forgot Password Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center">
            
            <div class="mb-8 text-center md:text-left">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center h-12 mb-6 md:hidden">
                    <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-12 w-auto">
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Lupa Kata Sandi?</h1>
                <p class="text-slate-500 font-medium">Masukkan email terdaftar Anda, kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
            </div>

            <!-- Alerts -->
            @if ($errors->any())
                <div class="bg-red-50/80 border border-red-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-grow text-xs text-red-700 font-medium">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="bg-green-50/80 border border-green-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-sm font-bold text-green-900 leading-tight">Link Berhasil Dikirim</h3>
                        <p class="mt-1 text-xs text-green-700 font-medium leading-relaxed">{{ session('status') }}</p>
                    </div>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('password.email') }}" class="space-y-6" id="forgotForm">
                @csrf

                <!-- Email Input -->
                <div class="relative">
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder=" "
                        class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border border-slate-300 appearance-none focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 peer transition-colors" />
                    <label for="email" 
                        class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">
                        Alamat Email
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="submitBtn" class="w-full flex justify-center items-center py-3 px-4 bg-kpi-700 hover:bg-kpi-800 text-white font-semibold rounded-xl transition-colors shadow-sm cursor-pointer">
                    <span id="btnText">Kirim Link Reset Password</span>
                    <svg id="btnSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500 font-medium">
                Ingat kata sandi Anda? 
                <a href="{{ route('login') }}" class="font-bold text-kpi-600 hover:text-kpi-800 transition-colors">Kembali ke Halaman Masuk</a>
            </div>
        </div>
    </div>

    <!-- Script -->
    <script>
        const form = document.getElementById('forgotForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');

        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            btnText.textContent = 'Mengirimkan...';
            btnSpinner.classList.remove('hidden');
        });
    </script>
</body>
</html>
