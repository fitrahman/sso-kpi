<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KPI SSO Portal</title>
    
    <!-- Fonts: Inter for modern SaaS look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        kpi: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c', // Primary Corporate Red
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Smooth scrolling and basic resets */
        html { scroll-behavior: smooth; }
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }
        /* Micro-interactions */
        .btn-hover-effect { transition: all 0.3s ease; }
        .btn-hover-effect:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(185, 28, 28, 0.3); }
        
        /* Subtle background pattern */
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 32px 32px;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="w-full bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-10 w-auto">
                <span class="font-bold text-xl tracking-tight text-slate-900">KPI SSO</span>
            </div>
            <nav class="flex items-center gap-4">
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-kpi-700 transition-colors">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-kpi-700 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-kpi-700 text-white px-5 py-2 rounded-lg hover:bg-kpi-800 transition-colors shadow-sm btn-hover-effect">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main Content: Split Layout -->
    <main class="flex-grow flex items-center bg-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-24 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                
                <!-- Left: Typography & CTA -->
                <div class="max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-kpi-50 text-kpi-700 text-sm font-medium mb-6 border border-kpi-100">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-kpi-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-kpi-600"></span>
                        </span>
                        Sistem Autentikasi Terpusat
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.1] mb-6">
                        Akses seluruh aplikasi <br class="hidden sm:block"> dengan <span class="text-kpi-700">satu akun</span>.
                    </h1>
                    
                    <p class="text-lg text-slate-600 mb-10 leading-relaxed max-w-xl">
                        Single Sign-On (SSO) Portal untuk mengelola identitas, keamanan, dan akses ke berbagai layanan internal secara terpusat, aman, dan efisien.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-white bg-kpi-700 hover:bg-kpi-800 shadow-md btn-hover-effect">
                                Buka Dashboard
                                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-transparent text-base font-medium rounded-xl text-white bg-kpi-700 hover:bg-kpi-800 shadow-md btn-hover-effect">
                                Masuk ke Portal
                            </a>
                            <a href="{{ route('register') }}" class="inline-flex justify-center items-center px-8 py-3.5 border border-slate-300 text-base font-medium rounded-xl text-slate-700 bg-white hover:bg-slate-50 shadow-sm transition-all hover:border-slate-400">
                                Buat Akun
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Right: Illustration / Visuals -->
                <div class="hidden lg:flex justify-center relative">
                    <!-- Abstract decorative elements -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-[32rem] h-[32rem] bg-kpi-100 rounded-full mix-blend-multiply filter blur-3xl opacity-50 animate-blob"></div>
                    </div>
                    
                    <div class="relative bg-white rounded-2xl shadow-xl border border-slate-100 p-8 w-full max-w-md transform hover:-translate-y-1 transition-transform duration-500">
                        <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-4">
                            <h3 class="font-semibold text-slate-800">Aplikasi Terhubung</h3>
                            <span class="text-xs font-medium bg-green-100 text-green-700 px-2 py-1 rounded-full">Secure</span>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-default">
                                <div class="w-10 h-10 rounded-md bg-blue-100 text-blue-600 flex items-center justify-center font-bold">S1</div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-slate-800">Sistem 1</p>
                                    <p class="text-xs text-slate-500">Portal Kepegawaian</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-default">
                                <div class="w-10 h-10 rounded-md bg-purple-100 text-purple-600 flex items-center justify-center font-bold">S2</div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-slate-800">Sistem 2</p>
                                    <p class="text-xs text-slate-500">Sistem Informasi Akademik</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 rounded-lg hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-colors cursor-default">
                                <div class="w-10 h-10 rounded-md bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">GC</div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-slate-800">Go Client</p>
                                    <p class="text-xs text-slate-500">Layanan Eksternal</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex flex-col md:flex-row items-center justify-between">
            <p class="text-sm text-slate-500">&copy; {{ date('Y') }} Komisi Penyiaran Indonesia. All rights reserved.</p>
            <div class="flex items-center gap-4 mt-4 md:mt-0 text-sm text-slate-500">
                <a href="#" class="hover:text-slate-900 transition-colors">Bantuan</a>
                <span>&middot;</span>
                <a href="#" class="hover:text-slate-900 transition-colors">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>

</body>
</html>
