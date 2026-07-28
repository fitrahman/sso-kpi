<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Otorisasi Aplikasi - KPI SSO</title>
    <link rel="icon" type="image/png" href="{{ asset('logoKPI.png') }}">
    
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
        <!-- Logo KPI -->
        <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-20 w-auto mx-auto mb-6">
        
        <h2 class="text-2xl font-bold text-slate-900 mb-2 tracking-tight">Permintaan Otorisasi</h2>
        <p class="text-slate-500 mb-6 leading-relaxed font-medium">
            Aplikasi <strong class="text-slate-950 font-bold">{{ $client->name }}</strong> meminta izin untuk mengakses akun Anda.
        </p>

        <!-- Scope List -->
        @if (count($scopes) > 0)
            <div class="bg-slate-50 rounded-2xl p-5 mb-8 text-left border border-slate-100">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Aplikasi ini akan dapat:</p>
                <ul class="space-y-2.5">
                    @foreach ($scopes as $scope)
                        <li class="flex items-start gap-2.5 text-sm text-slate-600 font-medium">
                            <svg class="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $scope->description }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="flex gap-4">
            <!-- Cancel Button -->
            <form method="post" action="{{ route('passport.authorizations.deny') }}" class="w-1/2">
                @csrf
                @method('DELETE')

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="w-full py-3.5 border-2 border-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                    Batal
                </button>
            </form>

            <!-- Authorize Button -->
            <form method="post" action="{{ route('passport.authorizations.approve') }}" class="w-1/2">
                @csrf

                <input type="hidden" name="state" value="{{ $request->state }}">
                <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
                <input type="hidden" name="auth_token" value="{{ $authToken }}">
                <button type="submit" class="w-full py-3.5 bg-kpi-700 text-white rounded-xl font-semibold shadow-sm hover:bg-kpi-800 transition-colors">
                    Izinkan
                </button>
            </form>
        </div>
    </div>

</body>
</html>
