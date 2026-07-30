<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sedang Pemeliharaan - KPI SSO Portal</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: {
                        kpi: {
                            50: '#fff7ed', 100: '#ffedd5', 500: '#f97316', 600: '#ea580c',
                            700: '#c2410c', 800: '#9a3412', 900: '#7c2d12',
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
        @keyframes spin-slow { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        @keyframes bounce-gentle { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-6px)} }
        .spin-slow { animation: spin-slow 6s linear infinite; }
        .bounce-gentle { animation: bounce-gentle 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-pattern text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 sm:p-12 text-center border border-slate-100 relative">
        <!-- Animated Maintenance Icon -->
        <div class="w-24 h-24 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 bounce-gentle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <!-- Spinning ring -->
            <div class="absolute inset-0 rounded-full border-2 border-amber-300 border-dashed spin-slow"></div>
        </div>
        
        <div class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200 mb-4">
            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
            Sedang Dalam Pemeliharaan
        </div>

        <h2 class="text-2xl font-bold text-slate-900 mb-3 tracking-tight">{{ $appName }}</h2>
        
        <p class="text-slate-500 mb-4 leading-relaxed">
            Aplikasi ini sedang dalam proses pemeliharaan sistem dan tidak dapat diakses sementara.
        </p>

        @if (!empty($message))
            <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-6 text-sm text-amber-800 text-left">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <span>{{ $message }}</span>
                </div>
            </div>
        @else
            <p class="text-slate-400 text-sm mb-6">Silakan coba kembali beberapa saat lagi.</p>
        @endif

        <a href="{{ route('dashboard') }}" class="inline-flex justify-center w-full py-3.5 bg-slate-900 text-white rounded-xl font-semibold shadow-sm hover:bg-slate-800 transition-colors">
            Kembali ke Dashboard
        </a>
    </div>

</body>
</html>
