<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Akses - KPI SSO Portal</title>
    
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
    </style>
</head>
<body class="bg-pattern text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl overflow-hidden p-8 sm:p-12 text-center border border-slate-100 relative">
        <!-- Request Illustration -->
        <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-slate-900 mb-4 tracking-tight">Akses Ditolak</h2>
        
        <p class="text-slate-500 mb-8 leading-relaxed">
            Anda belum memiliki izin untuk mengakses aplikasi <strong class="text-slate-900 font-semibold">{{ $appName }}</strong>. Silakan ajukan permohonan akses kepada Administrator.
        </p>

        <form action="{{ route('app.requestAccess') }}" method="POST" class="mb-4">
            @csrf
            <input type="hidden" name="client_id" value="{{ $clientId }}">
            <button type="submit" class="inline-flex justify-center w-full py-3.5 bg-kpi-700 text-white rounded-xl font-semibold shadow-sm hover:bg-kpi-800 transition-colors">
                Ajukan Akses Sekarang
            </button>
        </form>
        
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            Kembali ke Dashboard
        </a>
    </div>

</body>
</html>
