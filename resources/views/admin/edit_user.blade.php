<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Pengguna - Admin SSO KPI</title>
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
            <div class="max-w-2xl mx-auto">
                
                <div class="mb-8 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Edit Pengguna</h1>
                        <p class="text-slate-500 mt-1">Ubah data profil dan hak akses dari pengguna.</p>
                    </div>
                    <a href="{{ route('admin.users') }}" class="hidden sm:inline-flex items-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-lg text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                        Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50/80 border border-red-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
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

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-6 sm:p-8">
                        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
            
                            <div>
                                <label for="name" class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 transition-shadow">
                            </div>
            
                            <div>
                                <label for="email" class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 transition-shadow">
                            </div>
            
                            <div>
                                <label for="phone" class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                    class="w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 transition-shadow">
                            </div>
            
                            <div>
                                <label for="role" class="block text-sm font-bold text-slate-700 mb-2">Divisi / Role Global (SSO)</label>
                                <div class="relative">
                                    <select name="role" id="role" required class="appearance-none w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 transition-shadow bg-white">
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>
                                                {{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Hak Akses Portal Aplikasi -->
                            @if ($user->role !== 'admin')
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Hak Akses & Role Portal Aplikasi</label>
                                <p class="text-xs text-slate-500 mb-3">Pilih portal aplikasi lokal server yang boleh diakses beserta peran (role) pengguna di aplikasi tersebut.</p>
                                <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200">
                                    @forelse ($clients as $client)
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-3 bg-white rounded-xl border border-slate-200 shadow-sm">
                                            <label class="flex items-start gap-3 cursor-pointer flex-grow">
                                                <input type="checkbox" name="clients[]" value="{{ $client->id }}" 
                                                    {{ in_array($client->id, $userAccessIds) ? 'checked' : '' }}
                                                    class="mt-1 h-4 w-4 rounded border-slate-300 text-kpi-600 focus:ring-kpi-500">
                                                <div>
                                                    <span class="block text-sm font-semibold text-slate-800">{{ $client->name }}</span>
                                                    <span class="block text-xs text-slate-500">{{ $client->redirect }}</span>
                                                </div>
                                            </label>
                                            <div class="relative min-w-[130px]">
                                                 @php
                                                     $supportedRoles = [];
                                                     if (!empty($client->supported_roles)) {
                                                         $supportedRoles = json_decode($client->supported_roles, true);
                                                     }
                                                     if (empty($supportedRoles) || !is_array($supportedRoles)) {
                                                         $supportedRoles = ['admin', 'pengguna'];
                                                     }
                                                 @endphp
                                                 <select name="client_roles[{{ $client->id }}]" class="appearance-none w-full px-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-kpi-500 bg-white text-slate-750 font-medium pr-8">
                                                     @foreach ($supportedRoles as $role)
                                                         <option value="{{ $role }}" {{ ($userClientRoles[$client->id] ?? $supportedRoles[0]) === $role ? 'selected' : '' }}>
                                                             {{ ucfirst($role) }}
                                                         </option>
                                                     @endforeach
                                                 </select>
                                                 <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
                                                     <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                                 </div>
                                             </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-slate-500">Tidak ada aplikasi klien yang terdaftar.</p>
                                    @endforelse
                                </div>
                            </div>
                            @endif
             
                            @if ($user->role !== 'admin' && $user->id !== Auth::id())
                            <div>
                                <label for="status" class="block text-sm font-bold text-slate-700 mb-2">Status Akun</label>
                                <div class="relative">
                                    <select name="status" id="status" required class="appearance-none w-full px-4 py-2.5 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500 transition-shadow bg-white">
                                        <option value="approved" {{ old('status', $user->status) === 'approved' ? 'selected' : '' }}>Aktif</option>
                                        <option value="inactive" {{ old('status', $user->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                    </div>
                                </div>
                            </div>
                            @endif
            
                            <div class="pt-4 flex gap-4">
                                <a href="{{ route('admin.users') }}" class="w-1/3 flex justify-center py-3 border border-slate-300 rounded-xl shadow-sm text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-colors">
                                    Batal
                                </a>
                                <button type="submit" class="w-2/3 flex justify-center py-3 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-kpi-700 hover:bg-kpi-800 transition-colors">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script>
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
