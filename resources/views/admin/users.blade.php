<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Pengguna - Admin SSO KPI</title>
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
        
        /* Custom Scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        
        /* Modal Transition */
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
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">
        
        <!-- Mobile Header -->
        <header class="md:hidden bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 shrink-0">
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileSidebar()" class="p-2 -ml-2 text-slate-600 hover:text-kpi-700 focus:outline-none" title="Buka Menu">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="font-bold text-lg text-slate-900">Admin Portal</span>
            </div>
            <a href="{{ route('dashboard') }}" class="text-sm text-kpi-700 font-semibold bg-kpi-50 px-3 py-1.5 rounded-lg hover:bg-kpi-100 transition-colors">Dashboard</a>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-6xl mx-auto">
                
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                    <p class="text-slate-500 mt-1 text-sm">Kelola seluruh pendaftar dan pengguna aktif di dalam sistem.</p>
                </div>



                <!-- Stats Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <!-- Total Users -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-4 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</div>
                            <div class="text-2xl font-bold text-slate-900 mt-1">{{ $totalCount }}</div>
                            <p class="text-xs text-slate-400 mt-0.5">Pengguna terdaftar</p>
                        </div>
                    </div>

                    <!-- Pending Approval -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center">
                        <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mr-4 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Persetujuan</div>
                            <div class="text-2xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</div>
                            <p class="text-xs text-slate-400 mt-0.5">Perlu verifikasi admin</p>
                        </div>
                    </div>

                    <!-- Inactive Users -->
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex items-center">
                        <div class="w-12 h-12 rounded-full bg-red-50 text-red-600 flex items-center justify-center mr-4 shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Pengguna Nonaktif</div>
                            <div class="text-2xl font-bold text-red-600 mt-1">{{ $inactiveCount }}</div>
                            <p class="text-xs text-slate-400 mt-0.5">Akses dinonaktifkan</p>
                        </div>
                    </div>
                </div>

                <!-- Filter & Search Card Panel -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4 pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">Daftar Pengguna</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh pengguna terdaftar, perizinan role, dan status keaktifan akun.</p>
                        </div>

                        @if (request('search') || request('role') || request('status'))
                            <a href="{{ route('admin.users') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 border border-red-200 rounded-xl transition-colors self-start md:self-auto">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reset Filter
                            </a>
                        @endif
                    </div>

                    <form action="{{ route('admin.users') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        <!-- Filter Role -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Divisi / Role Global</label>
                            <select name="role" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50 cursor-pointer">
                                <option value="">Semua Role / Divisi</option>
                                @foreach ($rolesList as $r)
                                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Status -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status Akun</label>
                            <select name="status" onchange="this.form.submit()" class="w-full border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50 cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aktif (Approved)</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu (Pending)</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Non-aktif (Inactive)</option>
                            </select>
                        </div>

                        <!-- Search Input & Button -->
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari Nama / Email</label>
                            <div class="flex gap-2">
                                <div class="relative flex-1">
                                    <input type="text" id="live-search" name="search" value="{{ request('search') }}" placeholder="Kata kunci..." class="w-full border border-slate-300 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-slate-50/50">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors shadow-sm">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Main Data Table Area -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="users-table">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr class="text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                    <th class="w-12 px-4 py-4 bg-slate-50 text-center">No</th>
                                    <th class="px-6 py-4 bg-slate-50">Pengguna</th>
                                    <th class="px-6 py-4 bg-slate-50">Divisi / Role</th>
                                    <th class="px-6 py-4 bg-slate-50">Status</th>
                                    <th class="px-6 py-4 bg-slate-50">Bergabung</th>
                                    <th class="px-6 py-4 text-right bg-slate-50">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($users as $user)
                                    <tr class="user-row hover:bg-slate-100/80 transition-colors cursor-pointer"
                                        onclick="if (!event.target.closest('button, form, a, input, select')) { window.location.href = '{{ route('admin.users.edit', $user->id) }}'; }">
                                        <td class="px-4 py-4 text-center text-xs font-semibold text-slate-400 whitespace-nowrap">
                                            {{ $users->firstItem() + $loop->index }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-600 border border-slate-200">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-slate-900 user-name">{{ $user->name }}</div>
                                                    <div class="text-sm text-slate-500 user-email-cell">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($user->role)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                    {{ $user->role }}
                                                </span>
                                            @else
                                                <span class="text-sm text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->status === 'pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                                    Menunggu
                                                </span>
                                            @elseif($user->status === 'inactive')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100">
                                                    Nonaktif
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                                    Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $user->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if ($user->status === 'pending')
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                            data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone }}" data-role="{{ $user->role }}" data-date="{{ $user->created_at->format('d M Y H:i') }}"
                                                            onclick="openDetailModal(this)">
                                                         Detail
                                                     </button>
                                                     <button type="button"
                                                             onclick="triggerConfirm('Setujui Pengguna', 'Apakah Anda yakin ingin menyetujui akun <strong>{{ addslashes($user->name) }}</strong> ({{ $user->email }})?', '{{ route('admin.users.approve', $user->id) }}', 'POST', 'green')"
                                                             class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700 shadow-sm">
                                                         Setujui
                                                     </button>
                                                     <button type="button"
                                                             onclick="triggerConfirm('Tolak Pengguna', 'Apakah Anda yakin ingin menolak dan menghapus akun pendaftaran <strong>{{ addslashes($user->name) }}</strong> ({{ $user->email }})?', '{{ route('admin.users.reject', $user->id) }}', 'DELETE', 'reject')"
                                                             class="inline-flex items-center px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-md text-xs font-semibold hover:bg-red-50 hover:border-red-300 shadow-sm">
                                                         Tolak
                                                     </button>
                                                </div>
                                            @else
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                        Edit
                                                    </a>
                                                    
                                                    @if ($user->role !== 'admin' && $user->id !== Auth::id())
                                                        <button type="button"
                                                                onclick="triggerConfirm('Hapus Pengguna', 'Apakah Anda yakin ingin menghapus akun <strong>{{ addslashes($user->name) }}</strong> ({{ $user->email }}) secara permanen?', '{{ route('admin.users.delete', $user->id) }}', 'DELETE', 'delete')"
                                                                class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-red-700 shadow-sm">
                                                            Hapus
                                                        </button>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 font-medium">
                                            Tidak ada data pengguna yang ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if ($users->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                            <div class="text-sm text-slate-500">
                                Menampilkan <span class="font-medium">{{ $users->firstItem() }}</span> - <span class="font-medium">{{ $users->lastItem() }}</span> dari <span class="font-medium">{{ $users->total() }}</span>
                            </div>
                            <div class="flex space-x-2">
                                @if ($users->onFirstPage())
                                    <span class="px-3 py-1.5 border border-slate-200 rounded text-sm text-slate-400 bg-slate-50 cursor-not-allowed"><</span>
                                @else
                                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-700 bg-white hover:bg-slate-50"><</a>
                                @endif
                                
                                @if ($users->hasMorePages())
                                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">></a>
                                @else
                                    <span class="px-3 py-1.5 border border-slate-200 rounded text-sm text-slate-400 bg-slate-50 cursor-not-allowed">></span>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </main>

    <!-- Detail Modal -->
    <div id="detailModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDetailModal()"></div>
        
        <!-- Modal Content -->
        <div class="modal-content relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Detail Pendaftar</h3>
                <button type="button" class="text-slate-400 hover:text-slate-600 focus:outline-none" onclick="closeDetailModal()">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="px-6 py-6 space-y-4">
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider col-span-1">Nama Lengkap</div>
                    <div class="text-sm font-semibold text-slate-900 col-span-2" id="modal-name">-</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider col-span-1">Alamat Email</div>
                    <div class="text-sm font-semibold text-slate-900 col-span-2" id="modal-email">-</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider col-span-1">Nomor Telepon</div>
                    <div class="text-sm font-semibold text-slate-900 col-span-2" id="modal-phone">-</div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider col-span-1">Divisi / Role</div>
                    <div class="text-sm font-semibold text-slate-900 col-span-2">
                        <span id="modal-role" class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 text-slate-700">-</span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider col-span-1">Tanggal Daftar</div>
                    <div class="text-sm font-semibold text-slate-900 col-span-2" id="modal-date">-</div>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                <button type="button" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm" onclick="closeDetailModal()">Tutup</button>
            </div>
        </div>
    </div>

    <!-- Reusable Action Confirmation Modal -->
    <div id="actionConfirmModal" class="modal fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
        
        <!-- Modal Content -->
        <div class="modal-content relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col p-6 text-center">
            <div id="confirmIconContainer" class="w-14 h-14 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <!-- Icon dynamically injected -->
            </div>
            
            <h3 class="text-lg font-bold text-slate-900 mb-1" id="confirmTitle">Konfirmasi</h3>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed px-2" id="confirmDescription">
                Apakah Anda yakin ingin melanjutkan tindakan ini?
            </p>
            
            <form id="confirmActionForm" method="POST" action="">
                @csrf
                <div id="confirmFormMethod"></div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeConfirmModal()"
                        class="flex-1 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="confirmSubmitBtn"
                        class="flex-1 py-2.5 text-white rounded-xl text-xs font-semibold transition-colors shadow-sm">
                        Ya, Lanjutkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Search & Modal Script -->
    <script>
        function openDetailModal(btn) {
            document.getElementById('modal-name').textContent = btn.getAttribute('data-name');
            document.getElementById('modal-email').textContent = btn.getAttribute('data-email');
            document.getElementById('modal-phone').textContent = btn.getAttribute('data-phone') || '-';
            document.getElementById('modal-role').textContent = btn.getAttribute('data-role') || '-';
            document.getElementById('modal-date').textContent = btn.getAttribute('data-date');
            
            document.getElementById('detailModal').classList.add('active');
        }

        function closeDetailModal() {
            document.getElementById('detailModal').classList.remove('active');
        }

        function closeConfirmModal() {
            document.getElementById('actionConfirmModal').classList.remove('active');
        }

        function triggerConfirm(title, description, actionUrl, method, theme = 'green') {
            document.getElementById('confirmTitle').textContent = title;
            document.getElementById('confirmDescription').innerHTML = description;
            
            const form = document.getElementById('confirmActionForm');
            form.action = actionUrl;
            
            const methodContainer = document.getElementById('confirmFormMethod');
            if (method.toUpperCase() === 'DELETE') {
                methodContainer.innerHTML = '<input type="hidden" name="_method" value="DELETE">';
            } else if (method.toUpperCase() === 'PUT') {
                methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            } else {
                methodContainer.innerHTML = '';
            }
            
            const iconContainer = document.getElementById('confirmIconContainer');
            const submitBtn = document.getElementById('confirmSubmitBtn');
            
            if (theme === 'green') {
                iconContainer.className = 'w-14 h-14 rounded-2xl bg-green-50 border border-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 shadow-sm';
                iconContainer.innerHTML = `<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
                submitBtn.className = 'flex-1 py-2.5 bg-green-600 text-white rounded-xl text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm';
                submitBtn.textContent = 'Ya, Setujui';
            } else {
                iconContainer.className = 'w-14 h-14 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 shadow-sm';
                iconContainer.innerHTML = `<svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>`;
                submitBtn.className = 'flex-1 py-2.5 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm';
                submitBtn.textContent = theme === 'reject' ? 'Ya, Tolak' : 'Ya, Hapus';
            }
            
            document.getElementById('actionConfirmModal').classList.add('active');
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
