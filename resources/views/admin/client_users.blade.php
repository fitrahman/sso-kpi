<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail {{ $client->name }} - Admin SSO KPI</title>
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
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .modal { opacity: 0; pointer-events: none; transition: all 0.3s ease; }
        .modal.active { opacity: 1; pointer-events: auto; }
        .modal-content { transform: scale(0.95) translateY(20px); transition: all 0.3s ease; }
        .modal.active .modal-content { transform: scale(1) translateY(0); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 h-screen flex overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col flex-shrink-0">
        <div class="h-16 flex items-center px-6 border-b border-slate-200">
            <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-8 w-auto mr-3">
            <span class="font-bold text-lg text-slate-900 tracking-tight">Admin Portal</span>
        </div>

        <div class="p-4 flex-grow sidebar-scroll overflow-y-auto">
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2 px-3">Menu Utama</div>
            <nav class="space-y-1">
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('dashboard') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard SSO
                </a>
                
                <a href="{{ route('admin.stats') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.stats*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.stats*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Statistik
                </a>

                @php
                    $pendingBadgeCount = \App\Models\User::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users*', 'admin.edit*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users*', 'admin.edit*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Manajemen Pengguna</span>
                    @if($pendingBadgeCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingBadgeCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.clients') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.clients*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.clients*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Manajemen Aplikasi
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4 px-2">
                <div class="w-8 h-8 rounded-full bg-kpi-100 text-kpi-700 flex items-center justify-center font-bold text-sm mr-3">
                    {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="text-xs text-slate-500 truncate">Administrator</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex justify-center items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Mobile Sidebar Drawer -->
    <div id="mobileSidebar" class="fixed inset-0 z-40 md:hidden hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity animate-fade-in" onclick="toggleMobileSidebar()"></div>
        <div class="fixed inset-y-0 left-0 flex max-w-xs w-full bg-white shadow-xl transition-transform transform -translate-x-full duration-300 ease-in-out flex-col" id="mobileSidebarContent">
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200 shrink-0">
                <div class="flex items-center">
                    <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-8 w-auto mr-3">
                    <span class="font-bold text-lg text-slate-900 tracking-tight">Admin Portal</span>
                </div>
                <button onclick="toggleMobileSidebar()" class="p-2 -mr-2 text-slate-500 hover:text-slate-700 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-4 flex-grow sidebar-scroll overflow-y-auto">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2 px-3">Menu Utama</div>
                <nav class="space-y-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                        <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        Dashboard SSO
                    </a>
                    <a href="{{ route('admin.stats') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.stats*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.stats*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        Statistik
                    </a>
                    <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.users*', 'admin.edit*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.users*', 'admin.edit*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        <span>Manajemen Pengguna</span>
                        @if($pendingBadgeCount > 0)
                            <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingBadgeCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.clients') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg {{ request()->routeIs('admin.clients*') ? 'bg-kpi-50 text-kpi-700' : 'text-slate-600 hover:text-kpi-700 hover:bg-slate-50' }} transition-colors">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.clients*') ? 'text-kpi-600' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                        Manajemen Aplikasi
                    </a>
                </nav>
            </div>
            <div class="p-4 border-t border-slate-200 shrink-0">
                <div class="flex items-center mb-4 px-2">
                    <div class="w-8 h-8 rounded-full bg-kpi-100 text-kpi-700 flex items-center justify-center font-bold text-sm mr-3">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-xs text-slate-500 truncate">Administrator</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-20 shadow-sm shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileSidebar()" class="md:hidden p-2 -ml-2 text-slate-600 hover:text-kpi-700 focus:outline-none" title="Buka Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="flex items-center gap-2 text-sm">
                    <a href="{{ route('admin.clients') }}" class="text-slate-500 hover:text-kpi-700 font-medium hidden sm:inline">Manajemen Aplikasi</a>
                    <span class="text-slate-300 hidden sm:inline">/</span>
                    <span class="font-bold text-slate-900 truncate max-w-[150px] sm:max-w-none">{{ $client->name }}</span>
                </div>
            </div>
            <a href="{{ route('admin.clients') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">Kembali</span>
            </a>
        </header>

        <div class="p-4 sm:p-8 max-w-6xl w-full mx-auto space-y-6">

            @if (session('success') && !str_contains(session('success'), 'secara massal'))
                <div class="bg-green-50 border border-green-200 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-green-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 rounded-xl flex items-start gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                </div>
            @endif



            <!-- Application Banner / Overview Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-slate-800 to-slate-900 text-white flex items-center justify-center shadow-md overflow-hidden flex-shrink-0">
                        @if ($client->logo_path)
                            <img src="{{ Storage::url($client->logo_path) }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl font-extrabold text-slate-900">{{ $client->name }}</h1>
                            @if ($client->is_maintenance)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 border border-amber-200">
                                    Maintenance
                                </span>
                            @endif
                            @if (!$client->is_visible)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    Tersembunyi
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 mt-1 max-w-2xl">{{ $client->description ?: 'Belum ada deskripsi aplikasi.' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 flex-shrink-0">
                    <button onclick="openEditModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-kpi-600 text-white rounded-xl text-sm font-semibold hover:bg-kpi-700 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Info Aplikasi
                    </button>
                </div>
            </div>

            <!-- Filter & Search Card Panel -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Daftar Pengguna & Role Lokal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola status akses dan role khusus pengguna di aplikasi {{ $client->name }}.</p>
                    </div>

                    <!-- Mode Toggle (Cari vs Setting) -->
                    <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl border border-slate-200">
                        <button type="button" id="btn-mode-cari" onclick="switchMode('cari')" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm bg-white text-slate-800">
                            Cari & Filter
                        </button>
                        <button type="button" id="btn-mode-setting" onclick="switchMode('setting')" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-800">
                            Pengaturan
                        </button>
                    </div>

                    @if (!empty($search) || !empty(request('role')) || !empty(request('access')) || !empty(request('local_role')))
                        <a href="{{ route('admin.clients.users', $client->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors self-start md:self-auto">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Reset Filter
                        </a>
                    @endif
                </div>

                <!-- Form Mode Cari -->
                <form id="searchAppUsersForm" method="GET" action="{{ route('admin.clients.users', $client->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Filter Role Global -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Role Global SSO</label>
                        <select name="role" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50 cursor-pointer">
                            <option value="">Semua Role Global</option>
                            @foreach ($rolesList as $r)
                                <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Akses Portal -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Akses Portal</label>
                        <select name="access" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50 cursor-pointer">
                            <option value="">Semua Status Akses</option>
                            <option value="approved" {{ request('access') === 'approved' ? 'selected' : '' }}>Memiliki Akses</option>
                            <option value="no_access" {{ request('access') === 'no_access' ? 'selected' : '' }}>Tidak Memiliki Akses</option>
                        </select>
                    </div>

                    <!-- Filter Role Lokal -->
                    @php
                        $supportedRolesList = [];
                        if (!empty($client->supported_roles)) {
                            $supportedRolesList = json_decode($client->supported_roles, true);
                        }
                        if (empty($supportedRolesList) || !is_array($supportedRolesList)) {
                            $supportedRolesList = ['pengguna', 'admin', 'editor', 'superadmin', 'atasan', 'pegawai', 'view'];
                        }
                    @endphp
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Role Lokal Aplikasi</label>
                        <select name="local_role" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50 cursor-pointer">
                            <option value="">Semua Role Lokal</option>
                            @foreach ($supportedRolesList as $lRole)
                                <option value="{{ $lRole }}" {{ request('local_role') === $lRole ? 'selected' : '' }}>{{ ucfirst($lRole) }}</option>
                            @endforeach
                            <option value="none" {{ request('local_role') === 'none' ? 'selected' : '' }}>Tidak Memiliki Role</option>
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Nama / Email</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="text" id="app-user-search" name="search" value="{{ $search }}" placeholder="Kata kunci..."
                                    class="w-full border border-slate-300 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors shadow-sm">
                                Cari
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Form Mode Setting (Bulk Action) -->
                <form id="bulkActionForm" method="POST" action="{{ route('admin.clients.users.bulk', $client->id) }}" class="hidden">
                    @csrf
                    <!-- Pass search/filter parameters to query all users when selecting all pages -->
                    <input type="hidden" name="search" value="{{ $search }}">
                    <input type="hidden" name="role" value="{{ request('role') }}">
                    <input type="hidden" name="access" value="{{ request('access') }}">
                    <input type="hidden" name="local_role" value="{{ request('local_role') }}">
                    <input type="hidden" name="select_all_pages" id="select-all-pages-input" value="0">

                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 shadow-inner">
                        <!-- Select All Pages Banner -->
                        <div id="select-all-pages-banner" class="hidden mb-3 p-2.5 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 flex items-center justify-between">
                            <span>
                                Terpilih <strong><span id="selected-count">{{ $users->count() }}</span></strong> pengguna di halaman ini. 
                                <button type="button" id="btn-select-all-pages" onclick="setSelectAllPages(true)" class="font-bold underline text-blue-900 hover:text-blue-950">
                                    Pilih seluruh {{ $users->total() }} pengguna di aplikasi ini.
                                </button>
                                <span id="all-pages-selected-msg" class="hidden font-bold">Seluruh {{ $users->total() }} pengguna terpilih.</span>
                            </span>
                            <button type="button" id="btn-clear-select-all-pages" onclick="setSelectAllPages(false)" class="hidden text-[11px] font-bold text-red-600 hover:underline">
                                Batalkan Pilihan Semua Halaman
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Tindakan Massal</label>
                                <select id="bulk-action-select" name="bulk_action" onchange="toggleBulkRoleDropdown()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-white cursor-pointer" required>
                                    <option value="">-- Pilih Tindakan --</option>
                                    <option value="enable_access">Aktifkan Akses Portal</option>
                                    <option value="disable_access">Nonaktifkan Akses Portal</option>
                                    <option value="change_role">Ubah Role Lokal</option>
                                </select>
                            </div>

                            <div id="bulk-local-role-container" class="hidden">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Role Lokal</label>
                                <select name="bulk_local_role" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-white cursor-pointer">
                                    @foreach ($supportedRolesList as $lRole)
                                        <option value="{{ $lRole }}">{{ ucfirst($lRole) }}</option>
                                    @endforeach
                                    <option value="user">User (Default)</option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" onclick="return confirmBulkAction(event)" class="px-4 py-2 bg-kpi-600 hover:bg-kpi-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors flex-1 text-center">
                                    Simpan Perubahan Massal
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="w-12 px-4 py-3.5 bg-slate-50 text-center col-header-no">No</th>
                                <th class="w-12 px-4 py-3.5 bg-slate-50 text-center col-header-checkbox hidden">
                                    <label class="inline-flex items-center justify-center cursor-pointer select-none">
                                        <input type="checkbox" id="select-all-users" onchange="toggleSelectAll(this)" class="h-4 w-4 rounded border-slate-300 text-kpi-600 focus:ring-kpi-500 cursor-pointer">
                                    </label>
                                </th>
                                <th class="px-6 py-3.5 bg-slate-50">Pengguna</th>
                                <th class="px-6 py-3.5 bg-slate-50">Role Global</th>
                                <th class="px-6 py-3.5 bg-slate-50 text-center">Akses Portal</th>
                                <th class="px-6 py-3.5 bg-slate-50">Role Lokal ({{ $client->name }})</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="app-users-tbody">
                            @php
                                $supportedRoles = [];
                                if (!empty($client->supported_roles)) {
                                    $supportedRoles = json_decode($client->supported_roles, true);
                                }
                                if (empty($supportedRoles) || !is_array($supportedRoles)) {
                                    $supportedRoles = ['pengguna', 'admin', 'editor', 'superadmin', 'atasan', 'pegawai', 'view'];
                                }
                            @endphp
                            @forelse ($users as $u)
                                @php
                                    $hasAccess = (bool) ($accessMap[$u->id] ?? false);
                                    $currentLocalRole = $localRolesMap[$u->id] ?? 'user';

                                    // Build role list including 'user' and current role if custom
                                    $roleOptions = $supportedRoles;
                                    if (!in_array('user', $roleOptions)) {
                                        $roleOptions[] = 'user';
                                    }
                                    if (!empty($currentLocalRole) && !in_array($currentLocalRole, $roleOptions)) {
                                        $roleOptions[] = $currentLocalRole;
                                    }
                                @endphp
                                <tr class="user-app-row hover:bg-slate-50/80 transition-colors">
                                    <!-- No / Checkbox -->
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-slate-400 whitespace-nowrap col-cell-no">
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-slate-400 whitespace-nowrap col-cell-checkbox hidden">
                                        <label class="inline-flex items-center justify-center cursor-pointer select-none">
                                            <input type="checkbox" name="user_ids[]" value="{{ $u->id }}" form="bulkActionForm" class="user-checkbox h-4 w-4 rounded border-slate-300 text-kpi-600 focus:ring-kpi-500 cursor-pointer">
                                        </label>
                                    </td>

                                    <!-- User Info -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 font-bold flex items-center justify-center text-xs uppercase flex-shrink-0">
                                                {{ substr($u->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-sm leading-tight user-app-name">{{ $u->name }}</p>
                                                <p class="text-xs text-slate-500 user-app-email">{{ $u->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Global Role -->
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $u->role === 'admin' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                            {{ ucfirst($u->role) }}
                                        </span>
                                    </td>

                                    <!-- Access Checkbox Form -->
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('admin.clients.users.update', ['id' => $client->id, 'userId' => $u->id]) }}" method="POST" class="flex justify-center">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="access_submitted" value="1">
                                            @if (!empty($currentLocalRole))
                                                <input type="hidden" name="local_role" value="{{ $currentLocalRole }}">
                                            @endif
                                            <label class="inline-flex items-center justify-center cursor-pointer select-none">
                                                <input type="checkbox" name="has_access" value="1" {{ $hasAccess ? 'checked' : '' }}
                                                    onchange="this.form.submit()"
                                                    class="h-4 w-4 rounded border-slate-300 text-kpi-600 focus:ring-kpi-500 cursor-pointer">
                                            </label>
                                        </form>
                                    </td>

                                    <!-- Local Role Dropdown Form -->
                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.clients.users.update', ['id' => $client->id, 'userId' => $u->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            @if ($hasAccess)
                                                <input type="hidden" name="has_access" value="1">
                                                <input type="hidden" name="access_submitted" value="1">
                                            @endif
                                            <select name="local_role" onchange="this.form.submit()" class="w-48 border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 font-medium bg-white {{ !$hasAccess ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer' }}" {{ !$hasAccess ? 'disabled' : '' }}>
                                                @foreach ($roleOptions as $rOpt)
                                                    <option value="{{ $rOpt }}" {{ $currentLocalRole === $rOpt ? 'selected' : '' }}>
                                                        {{ ucfirst($rOpt) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400 text-sm">Tidak ada pengguna ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($users->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>

            <!-- Application Activity Log -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900 text-sm">Riwayat Aktivitas Aplikasi {{ $client->name }}</h2>
                </div>
                @if ($logs->isEmpty())
                    <div class="px-6 py-8 text-center text-slate-400 text-xs">Belum ada riwayat aktivitas untuk aplikasi ini.</div>
                @else
                    <div class="overflow-x-auto max-h-[250px] overflow-y-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-slate-50 border-b border-slate-100 uppercase tracking-wider text-slate-400 font-semibold sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 bg-slate-50">Admin</th>
                                    <th class="px-6 py-3 bg-slate-50">Aksi</th>
                                    <th class="px-6 py-3 bg-slate-50">Deskripsi</th>
                                    <th class="px-6 py-3 bg-slate-50">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($logs as $log)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-6 py-3 font-semibold text-slate-800">{{ $log->admin->name ?? 'Sistem' }}</td>
                                        <td class="px-6 py-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-slate-600">{{ $log->description }}</td>
                                        <td class="px-6 py-3 text-slate-400 whitespace-nowrap">{{ $log->created_at ? $log->created_at->format('d M Y, H:i') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- Edit Application Modal -->
    <div id="editModal" class="modal fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60" onclick="closeEditModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 my-8">
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100 sticky top-0 bg-white rounded-t-2xl z-20">
                <h3 class="text-lg font-bold text-slate-900">Edit Info Aplikasi</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="max-h-[calc(100vh-8rem)] overflow-y-auto">
                <form action="{{ route('admin.clients.update', $client->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Aplikasi <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $client->name }}" required
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                        <textarea name="description" rows="2"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 resize-none"
                            placeholder="Deskripsi singkat aplikasi...">{{ $client->description }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pesan Maintenance (opsional)</label>
                        <textarea name="maintenance_message" rows="2"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 resize-none"
                            placeholder="Contoh: Sedang upgrade server...">{{ $client->maintenance_message }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Urutan Tampil</label>
                            <input type="number" name="display_order" value="{{ $client->display_order }}" min="0"
                                class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tampil di Dashboard</label>
                            <select name="is_visible"
                                class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                                <option value="1" {{ $client->is_visible ? 'selected' : '' }}>Ya (Tampil)</option>
                                <option value="0" {{ !$client->is_visible ? 'selected' : '' }}>Tidak (Sembunyikan)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Gambar Kartu Aplikasi</label>

                        @if ($client->logo_path)
                            <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ Storage::url($client->logo_path) }}" alt="Gambar saat ini" class="h-12 w-20 object-cover rounded-lg border border-slate-200">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-700">Gambar Aktif</p>
                                        <p class="text-[11px] text-slate-400">Terpasang sebagai background kartu</p>
                                    </div>
                                </div>
                                <button type="button" onclick="document.getElementById('deleteLogoForm').submit()"
                                    class="w-full sm:w-auto px-2.5 py-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-semibold rounded-lg hover:bg-red-100">
                                    Hapus
                                </button>
                            </div>
                        @endif

                        <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                        <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, SVG. Maks. 2MB.</p>
                    </div>

                    <div class="flex gap-3 pt-4 border-t border-slate-100 sticky bottom-0 bg-white py-4 rounded-b-2xl z-20">
                        <button type="button" onclick="closeEditModal()"
                            class="flex-1 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 py-2.5 bg-kpi-600 text-white rounded-xl text-sm font-semibold hover:bg-kpi-700 transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

            @if ($client->logo_path)
                <form id="deleteLogoForm" action="{{ route('admin.clients.logo.delete', $client->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>

    <!-- Reusable Action Confirmation Modal (SSO Style) -->
    <div id="actionConfirmModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        
        <!-- Modal Content -->
        <div class="modal-content relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col p-6 text-center z-10">
            <!-- Normal Content -->
            <div id="confirm-normal-content">
                <div id="confirmIconContainer" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <!-- Icon dynamically injected -->
                </div>
                
                <h3 class="text-lg font-bold text-slate-900 mb-1" id="confirmTitle">Konfirmasi</h3>
                <p class="text-sm text-slate-500 mb-6 leading-relaxed px-2" id="confirmDescription">
                    Apakah Anda yakin ingin melanjutkan tindakan ini?
                </p>
                
                <div class="flex gap-3">
                    <button type="button" id="confirmCancelBtn" onclick="closeConfirmModal()"
                        class="flex-1 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="button" id="confirmSubmitBtn" onclick="submitBulkActionForm()"
                        class="flex-1 py-2.5 text-white rounded-xl text-xs font-semibold transition-colors shadow-sm bg-kpi-600 hover:bg-kpi-700">
                        Ya, Lanjutkan
                    </button>
                </div>
            </div>

            <!-- Loading Content -->
            <div id="confirm-loading-content" class="hidden py-8">
                <div class="flex flex-col items-center justify-center">
                    <!-- Elegant Spinner -->
                    <div class="relative w-16 h-16 mb-4">
                        <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
                        <div class="absolute inset-0 rounded-full border-4 border-t-kpi-600 animate-spin"></div>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Memproses Tindakan Massal</h4>
                    <p class="text-xs text-slate-500 max-w-xs px-4">Harap tunggu, sistem sedang memperbarui status akses dan role pengguna. Jangan menutup halaman ini.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchMode(mode) {
            const btnCari = document.getElementById('btn-mode-cari');
            const btnSetting = document.getElementById('btn-mode-setting');
            const formCari = document.getElementById('searchAppUsersForm');
            const formSetting = document.getElementById('bulkActionForm');

            // Columns headers and cells
            const colHeadersNo = document.querySelectorAll('.col-header-no');
            const colHeadersCheckbox = document.querySelectorAll('.col-header-checkbox');
            const colCellsNo = document.querySelectorAll('.col-cell-no');
            const colCellsCheckbox = document.querySelectorAll('.col-cell-checkbox');

            if (mode === 'cari') {
                btnCari.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm bg-white text-slate-800";
                btnSetting.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-800";
                formCari.classList.remove('hidden');
                formSetting.classList.add('hidden');

                colHeadersNo.forEach(el => el.classList.remove('hidden'));
                colHeadersCheckbox.forEach(el => el.classList.add('hidden'));
                colCellsNo.forEach(el => el.classList.remove('hidden'));
                colCellsCheckbox.forEach(el => el.classList.add('hidden'));
            } else {
                btnSetting.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm bg-white text-slate-800";
                btnCari.className = "px-4 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 hover:text-slate-800";
                formCari.classList.add('hidden');
                formSetting.classList.remove('hidden');

                colHeadersNo.forEach(el => el.classList.add('hidden'));
                colHeadersCheckbox.forEach(el => el.classList.remove('hidden'));
                colCellsNo.forEach(el => el.classList.add('hidden'));
                colCellsCheckbox.forEach(el => el.classList.remove('hidden'));
            }
        }

        function toggleBulkRoleDropdown() {
            const actionSelect = document.getElementById('bulk-action-select');
            const roleContainer = document.getElementById('bulk-local-role-container');
            if (actionSelect.value === 'change_role') {
                roleContainer.classList.remove('hidden');
            } else {
                roleContainer.classList.add('hidden');
            }
        }

        function toggleSelectAll(masterCheckbox) {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = masterCheckbox.checked;
            });
            updateSelectAllPagesBanner();
        }

        function updateSelectAllPagesBanner() {
            const totalUsersTotal = {{ $users->total() }};
            const currentPageCount = {{ $users->count() }};
            const banner = document.getElementById('select-all-pages-banner');
            const selectedCountSpan = document.getElementById('selected-count');
            const btnSelectAllPages = document.getElementById('btn-select-all-pages');
            const allPagesSelectedMsg = document.getElementById('all-pages-selected-msg');
            const btnClearSelectAllPages = document.getElementById('btn-clear-select-all-pages');
            const selectAllPagesInput = document.getElementById('select-all-pages-input');

            const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');

            if (selectedCheckboxes.length === currentPageCount && totalUsersTotal > currentPageCount) {
                banner.classList.remove('hidden');
                selectedCountSpan.textContent = selectedCheckboxes.length;
                if (selectAllPagesInput.value === '1') {
                    btnSelectAllPages.classList.add('hidden');
                    allPagesSelectedMsg.classList.remove('hidden');
                    btnClearSelectAllPages.classList.remove('hidden');
                } else {
                    btnSelectAllPages.classList.remove('hidden');
                    allPagesSelectedMsg.classList.add('hidden');
                    btnClearSelectAllPages.classList.add('hidden');
                }
            } else {
                banner.classList.add('hidden');
                setSelectAllPages(false);
            }
        }

        function setSelectAllPages(value) {
            const selectAllPagesInput = document.getElementById('select-all-pages-input');
            const btnSelectAllPages = document.getElementById('btn-select-all-pages');
            const allPagesSelectedMsg = document.getElementById('all-pages-selected-msg');
            const btnClearSelectAllPages = document.getElementById('btn-clear-select-all-pages');

            if (value) {
                selectAllPagesInput.value = '1';
                btnSelectAllPages.classList.add('hidden');
                allPagesSelectedMsg.classList.remove('hidden');
                btnClearSelectAllPages.classList.remove('hidden');
            } else {
                selectAllPagesInput.value = '0';
                btnSelectAllPages.classList.remove('hidden');
                allPagesSelectedMsg.classList.add('hidden');
                btnClearSelectAllPages.classList.add('hidden');
            }
        }

        // Add change listener to checkboxes to dynamically update banner
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectAllPagesBanner);
            });
        });

        function showStyledAlert(title, message) {
            const iconContainer = document.getElementById('confirmIconContainer');
            const submitBtn = document.getElementById('confirmSubmitBtn');
            const cancelBtn = document.getElementById('confirmCancelBtn');

            // Hide cancel button since it's just an alert info/error, not a confirmation
            cancelBtn.classList.add('hidden');

            // Styled error icon (Red) for alert
            iconContainer.className = 'w-14 h-14 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 shadow-sm';
            iconContainer.innerHTML = `<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            
            // Adjust submit button to act as OK / Close button
            submitBtn.className = 'flex-1 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors shadow-sm';
            submitBtn.textContent = 'OK';
            submitBtn.onclick = closeConfirmModal;

            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDescription').textContent = message;
            document.getElementById('actionConfirmModal').classList.add('active');
        }

        function closeConfirmModal() {
            document.getElementById('actionConfirmModal').classList.remove('active');
            // Reset modal styles and bindings back to normal confirmation state
            setTimeout(() => {
                const cancelBtn = document.getElementById('confirmCancelBtn');
                const submitBtn = document.getElementById('confirmSubmitBtn');
                cancelBtn.classList.remove('hidden');
                submitBtn.onclick = submitBulkActionForm;
            }, 300);
        }

        function submitBulkActionForm() {
            const normalContent = document.getElementById('confirm-normal-content');
            const loadingContent = document.getElementById('confirm-loading-content');

            // Hide normal content and show elegant full-modal loading state
            normalContent.classList.add('hidden');
            loadingContent.classList.remove('hidden');

            document.getElementById('bulkActionForm').submit();
        }

        function confirmBulkAction(event) {
            const actionSelect = document.getElementById('bulk-action-select');
            if (!actionSelect.value) {
                showStyledAlert('Pilih Tindakan', 'Silakan pilih tindakan massal terlebih dahulu.');
                event.preventDefault();
                return false;
            }

            const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
            if (selectedUsers.length === 0) {
                showStyledAlert('Pilih Pengguna', 'Silakan pilih minimal satu pengguna untuk melakukan tindakan massal.');
                event.preventDefault();
                return false;
            }

            const isAllPages = document.getElementById('select-all-pages-input').value === '1';
            let confirmMessage = '';
            
            if (isAllPages) {
                confirmMessage = `PERINGATAN: Anda memilih seluruh ${ {{ $users->total() }} } pengguna di semua halaman. Apakah Anda yakin ingin melakukan tindakan massal ini?`;
            } else {
                confirmMessage = `Apakah Anda yakin ingin melakukan tindakan massal ini pada ${selectedUsers.length} pengguna terpilih?`;
            }

            const iconContainer = document.getElementById('confirmIconContainer');
            const submitBtn = document.getElementById('confirmSubmitBtn');

            // Styled warning icon (Orange/Amber) for bulk confirmation
            iconContainer.className = 'w-14 h-14 rounded-2xl bg-amber-50 border border-amber-100 text-amber-600 flex items-center justify-center mx-auto mb-4 shadow-sm';
            iconContainer.innerHTML = `<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>`;
            submitBtn.className = 'flex-1 py-2.5 bg-kpi-600 text-white rounded-xl text-xs font-semibold hover:bg-kpi-700 transition-colors shadow-sm';
            submitBtn.textContent = 'Ya, Lanjutkan';

            document.getElementById('confirmTitle').textContent = 'Konfirmasi Tindakan Massal';
            document.getElementById('confirmDescription').textContent = confirmMessage;
            document.getElementById('actionConfirmModal').classList.add('active');

            event.preventDefault();
            return false;
        }

        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        function toggleMobileSidebar() {
            const sidebar = document.getElementById('mobileSidebar');
            const content = document.getElementById('mobileSidebarContent');
            if (sidebar.classList.contains('hidden')) {
                sidebar.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('-translate-x-full');
                }, 10);
            } else {
                content.classList.add('-translate-x-full');
                setTimeout(() => {
                    sidebar.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>
