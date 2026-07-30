<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Aplikasi - KPI SSO</title>
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
        .app-card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            border-color: #fca5a5; /* red-300 */
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Header / Top Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="h-10 flex items-center justify-center hover:opacity-80 transition-opacity">
                    <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-10 w-auto">
                </a>
                <span class="font-bold text-lg tracking-tight text-slate-900 hidden sm:block">KPI SSO Portal</span>
            </div>

            <div class="flex items-center gap-3 sm:gap-6">
                <!-- Admin Menu -->
                @if (auth()->user()->isAdmin())
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('admin.users') }}" class="p-2 text-slate-500 hover:text-kpi-700 hover:bg-slate-100 rounded-xl transition-all" title="Manajemen Pengguna (Admin)">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    </nav>
                @endif
                
                <div class="h-6 w-px bg-slate-200 hidden md:block"></div>

                <!-- User Profile & Logout -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleModal('profileModal')" class="flex items-center gap-3 text-left focus:outline-none hover:opacity-80 transition-opacity" title="Profil Saya">
                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name ?? 'User' }}</div>
                            <div class="text-xs font-medium text-slate-500">{{ auth()->user()->role ?? 'Staff' }}</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-slate-200 border-2 border-white shadow-sm overflow-hidden flex items-center justify-center text-slate-600 font-bold uppercase">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="ml-2">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-kpi-700 p-2 rounded-md hover:bg-slate-100 transition-colors" title="Keluar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <div class="mb-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-2">Aplikasi Tersedia</h1>
            <p class="text-slate-500 text-lg">Pilih aplikasi yang ingin anda akses melalui portal SSO ini.</p>
        </div>

        @if (session('success'))
            <div class="bg-green-50/80 border border-green-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
                <div class="flex-grow">
                    <h3 class="text-sm font-bold text-green-900 leading-tight">Pemberitahuan</h3>
                    <p class="mt-1 text-xs text-green-700 font-medium leading-relaxed">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50/80 border border-red-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                <div class="flex-shrink-0 mt-0.5">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="flex-grow">
                    <h3 class="text-sm font-bold text-red-900 leading-tight">Terdapat Kesalahan</h3>
                    <div class="mt-1 text-xs text-red-700 font-medium leading-relaxed">
                        @if ($errors->count() === 1)
                            {{ $errors->first() }}
                        @else
                            <ul class="list-disc pl-4 space-y-0.5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        @endif


        <!-- Applications Grid -->
        @php
            $colorPalette = [
                ['bg' => 'bg-blue-50',    'text' => 'text-blue-600',    'hover' => 'group-hover:text-blue-700'],
                ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'hover' => 'group-hover:text-emerald-700'],
                ['bg' => 'bg-purple-50',  'text' => 'text-purple-600',  'hover' => 'group-hover:text-purple-700'],
                ['bg' => 'bg-amber-50',   'text' => 'text-amber-600',   'hover' => 'group-hover:text-amber-700'],
                ['bg' => 'bg-rose-50',    'text' => 'text-rose-600',    'hover' => 'group-hover:text-rose-700'],
                ['bg' => 'bg-indigo-50',  'text' => 'text-indigo-600',  'hover' => 'group-hover:text-indigo-700'],
            ];
        @endphp
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($allClients as $i => $client)
                @if (!$client->is_visible) @continue @endif
                @php $c = $colorPalette[$i % count($colorPalette)]; @endphp

                @if ($client->is_maintenance && auth()->user()->role !== 'admin')
                    {{-- Maintenance card (non-clickable) --}}
                    <div class="group block bg-white rounded-2xl border border-amber-200 p-6 shadow-sm relative overflow-hidden opacity-70 cursor-not-allowed select-none">
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-24 h-24 text-amber-400 transform rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div class="w-14 h-14 bg-amber-50 text-amber-400 rounded-xl flex items-center justify-center mb-4 shadow-sm overflow-hidden">
                            @if ($client->logo_path)
                                <img src="{{ Storage::url($client->logo_path) }}" alt="{{ $client->name }}" class="w-14 h-14 object-cover grayscale">
                            @else
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-xl font-bold text-slate-400">{{ $client->name }}</h3>
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span> Maintenance
                            </span>
                        </div>
                        <p class="text-sm text-slate-400 font-medium">Sedang dalam pemeliharaan sistem.</p>
                    </div>
                @else
                    {{-- Normal clickable card --}}
                    <a href="{{ route('app.gateway', ['appName' => $client->name]) }}" class="group block bg-white rounded-2xl border border-slate-200 p-6 shadow-sm transition-all duration-300 app-card-hover relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-24 h-24 {{ $c['text'] }} transform rotate-12" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        </div>
                        <div class="w-14 h-14 {{ $c['bg'] }} {{ $c['text'] }} rounded-xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition-transform duration-300 overflow-hidden">
                            @if ($client->logo_path)
                                <img src="{{ Storage::url($client->logo_path) }}" alt="{{ $client->name }}" class="w-14 h-14 object-cover">
                            @else
                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                            @endif
                        </div>
                        @if ($client->is_maintenance && auth()->user()->role === 'admin')
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200 mb-2">
                                <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Maintenance (Admin View)
                            </span>
                        @endif
                        <h3 class="text-xl font-bold text-slate-900 mb-2 {{ $c['hover'] }} transition-colors">{{ $client->name }}</h3>
                        <p class="text-sm text-slate-500 font-medium">{{ $client->description ?: 'Klik untuk mengakses aplikasi.' }}</p>
                        <div class="mt-6 flex items-center text-sm font-bold {{ $c['text'] }} opacity-0 group-hover:opacity-100 transform translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                            Buka Aplikasi <svg class="w-4 h-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>


    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-center text-sm text-slate-500 font-medium">
            &copy; {{ date('Y') }} Komisi Penyiaran Indonesia
        </div>
    </footer>

    <!-- Profile & Edit Request Modal -->
    <div id="profileModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-slate-500/75 transition-opacity" aria-hidden="true" onclick="toggleModal('profileModal')"></div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <!-- View Profile Mode -->
                <div id="profileViewContainer" class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <h3 class="text-lg font-bold text-slate-900" id="modal-title">Profil Saya</h3>
                        <button onclick="toggleModal('profileModal')" class="text-slate-400 hover:text-slate-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="flex flex-col items-center mb-6">
                        <div class="w-20 h-20 rounded-full bg-kpi-50 text-kpi-600 border border-kpi-100 shadow-sm flex items-center justify-center text-3xl font-bold uppercase mb-3">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <h4 class="text-xl font-bold text-slate-900">{{ auth()->user()->name }}</h4>
                        <span class="text-sm font-semibold text-slate-500">{{ auth()->user()->role }}</span>
                    </div>

                    <div class="border-t border-slate-100 py-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Alamat Email</span>
                            <span class="text-slate-900 font-bold">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Nomor Telepon</span>
                            <span class="text-slate-900 font-bold">{{ auth()->user()->phone ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500 font-medium">Status Akun</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-100">
                                {{ auth()->user()->status === 'approved' ? 'Aktif' : (auth()->user()->status === 'inactive' ? 'Nonaktif' : 'Menunggu Persetujuan') }}
                            </span>
                        </div>
                    </div>

                    <!-- Hak Akses Portal Aplikasi -->
                    <div class="border-t border-slate-100 py-4">
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Akses Portal Aplikasi</span>
                        @if (auth()->user()->isAdmin())
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                Semua Portal (Administrator)
                            </span>
                        @else
                            <div class="flex flex-wrap gap-1.5">
                                @forelse ($approvedApps as $app)
                                    @php
                                        $clientRole = $user->clientRoles->where('oauth_client_id', $app->id)->first()?->role ?? 'pengguna';
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                        {{ $app->name }} ({{ ucfirst($clientRole) }})
                                    </span>
                                @empty
                                    <span class="text-xs font-medium text-slate-400 italic">Belum memiliki akses portal apa pun. Hubungi Administrator.</span>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button onclick="toggleModal('profileModal')" class="px-5 py-2.5 bg-slate-950 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition-colors">Tutup</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }
    </script>

</body>
</html>
