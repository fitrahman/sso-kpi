<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manajemen Aplikasi - Admin SSO KPI</title>
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
        .toggle-checkbox:checked + .toggle-label { background-color: #dc2626; }
        .toggle-checkbox:checked + .toggle-label::after { transform: translateX(100%); }
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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard SSO
                </a>
                @php
                    $pendingBadgeCount = \App\Models\User::where('status', 'pending')->count();
                @endphp
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    <span>Manajemen Pengguna</span>
                    @if($pendingBadgeCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $pendingBadgeCount }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.clients') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-kpi-50 text-kpi-700 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-kpi-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    Manajemen Aplikasi
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-slate-200">
            <div class="flex items-center mb-4 px-2">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center font-bold text-slate-600 mr-3">
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
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-50/50">

        <!-- Mobile Header -->
        <header class="md:hidden bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4">
            <div class="font-bold text-lg text-slate-900">Admin Portal</div>
            <a href="{{ route('dashboard') }}" class="text-sm text-kpi-700 font-medium">Ke Dashboard</a>
        </header>

        <!-- Scrollable content -->
        <div class="flex-1 overflow-y-auto p-6 lg:p-8">



            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 p-4 mb-6 rounded-xl flex items-start gap-3 shadow-sm">
                    <svg class="h-5 w-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <p class="text-sm font-medium text-red-800">{{ $errors->first() }}</p>
                </div>
            @endif

            <!-- Page Header -->
            <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Manajemen Aplikasi</h1>
                    <p class="text-slate-500 mt-1">Kelola nama, deskripsi, logo, status maintenance, dan visibilitas setiap aplikasi.</p>
                </div>
                <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-kpi-700 hover:bg-kpi-800 text-white rounded-xl font-semibold text-sm transition-colors shadow-sm cursor-pointer w-fit">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Aplikasi
                </button>
            </div>

            <!-- Apps Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-5 mb-10">
                @foreach ($clients as $client)
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col {{ $client->is_maintenance ? 'border-amber-200' : '' }} {{ !$client->is_visible ? 'opacity-60' : '' }}">
                        <!-- Card Header (Clickable to detail users) -->
                        <a href="{{ route('admin.clients.users', $client->id) }}" class="p-5 flex items-start gap-4 border-b border-slate-100 group hover:bg-slate-50/80 transition-colors">
                            <!-- Logo -->
                            <div class="w-14 h-14 rounded-xl bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden group-hover:scale-105 transition-transform">
                                @if ($client->logo_path)
                                    <img src="{{ Storage::url($client->logo_path) }}" alt="{{ $client->name }}" class="w-14 h-14 object-cover">
                                @else
                                    <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="font-bold text-slate-900 text-base truncate group-hover:text-kpi-700 transition-colors">{{ $client->name }}</h3>
                                    @if ($client->is_maintenance)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span> Maintenance
                                        </span>
                                    @endif
                                    @if (!$client->is_visible)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                            Disembunyikan
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-slate-500 mt-0.5 truncate">{{ $client->description ?: 'Tidak ada deskripsi' }}</p>
                            </div>
                        </a>
                        
                        <!-- Stats -->
                        <div class="px-5 py-3 grid grid-cols-2 gap-3 border-b border-slate-100">
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Pengguna Aktif</p>
                                <p class="text-lg font-bold text-slate-800">{{ $client->user_count }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Urutan Tampil</p>
                                <p class="text-lg font-bold text-slate-800">{{ $client->display_order ?: '-' }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="p-4 flex items-center gap-2 flex-wrap mt-auto">
                            <!-- Detail Users & Roles Button -->
                            <a href="{{ route('admin.clients.users', $client->id) }}"
                                class="flex-1 inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-slate-900 text-white rounded-lg text-xs font-semibold hover:bg-slate-800 transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                Pengguna & Role
                            </a>

                            <!-- Edit Button -->
                            @php
                                $rolesStr = '';
                                if (!empty($client->supported_roles)) {
                                    $rArr = json_decode($client->supported_roles, true);
                                    if (is_array($rArr)) {
                                        $rolesStr = implode(', ', $rArr);
                                    }
                                }
                            @endphp
                            <button onclick="openEditModal({{ $client->id }}, '{{ addslashes($client->name) }}', '{{ addslashes($client->redirect) }}', '{{ addslashes($client->description ?? '') }}', '{{ addslashes($rolesStr) }}', '{{ addslashes($client->maintenance_message ?? '') }}', {{ $client->display_order }}, {{ $client->is_visible ? 1 : 0 }})"
                                class="inline-flex justify-center items-center gap-1.5 px-3 py-2 bg-white border border-slate-300 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-50 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </button>

                            <!-- Toggle Maintenance -->
                            <form action="{{ route('admin.clients.maintenance', $client->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold border transition-colors
                                    {{ $client->is_maintenance ? 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $client->is_maintenance ? 'Nonaktifkan' : 'Maintenance' }}
                                </button>
                            </form>

                            <!-- Toggle Visibility -->
                            <form action="{{ route('admin.clients.visibility', $client->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold border transition-colors
                                    {{ !$client->is_visible ? 'bg-slate-100 border-slate-300 text-slate-600 hover:bg-slate-200' : 'bg-white border-slate-300 text-slate-700 hover:bg-slate-50' }}"
                                    title="{{ $client->is_visible ? 'Sembunyikan dari dashboard' : 'Tampilkan di dashboard' }}">
                                    @if ($client->is_visible)
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                                    @endif
                                </button>
                            </form>

                            <!-- Delete Application -->
                            <button type="button" onclick="openDeleteModal({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                class="inline-flex justify-center items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 transition-colors" title="Hapus Aplikasi">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Activity Log -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">Log Aktivitas</h2>
                </div>
                @if ($activityLogs->isEmpty())
                    <div class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada aktivitas tercatat.</div>
                @else
                    <div class="overflow-x-auto max-h-[250px] overflow-y-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase tracking-wider text-slate-500 font-semibold sticky top-0 z-10">
                                <tr>
                                    <th class="px-6 py-3 text-left bg-slate-50">Admin</th>
                                    <th class="px-6 py-3 text-left bg-slate-50">Aksi</th>
                                    <th class="px-6 py-3 text-left bg-slate-50">Detail</th>
                                    <th class="px-6 py-3 text-left bg-slate-50">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($activityLogs as $log)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-3 font-medium text-slate-800">{{ $log->admin->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-3">
                                            @php
                                                $actionColor = match($log->action) {
                                                    'maintenance_on'  => 'bg-amber-100 text-amber-700',
                                                    'maintenance_off' => 'bg-green-100 text-green-700',
                                                    'visibility_on'   => 'bg-blue-100 text-blue-700',
                                                    'visibility_off'  => 'bg-slate-100 text-slate-600',
                                                    default           => 'bg-slate-100 text-slate-600',
                                                };
                                            @endphp
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $actionColor }}">{{ $log->action }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-slate-500">{{ $log->description }}</td>
                                        <td class="px-6 py-3 text-slate-400 whitespace-nowrap">{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- Create Application Modal -->
    <div id="createModal" class="modal fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60" onclick="closeCreateModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10">
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Tambah Aplikasi</h3>
                <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.clients.store') }}" method="POST" enctype="multipart/form-data" class="p-6 pt-4 space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Aplikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Sistem Absensi"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Redirect URI / Callback URL <span class="text-red-500">*</span></label>
                    <input type="url" name="redirect" required placeholder="Contoh: http://nama-aplikasi.com/auth/sso/callback"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    <p class="text-xs text-slate-400 mt-1">URL Callback tempat server SSO mengirimkan OAuth authorization code.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi Aplikasi</label>
                    <textarea name="description" rows="2"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent resize-none"
                        placeholder="Deskripsi singkat modul/fitur aplikasi ini..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role Lokal yang Didukung (Pisahkan dengan koma)</label>
                    <input type="text" name="supported_roles" placeholder="Contoh: Admin, Supervisor, Operator"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    <p class="text-xs text-slate-400 mt-1">Role spesifik aplikasi yang bisa dipilih Admin saat pemetaan peran pengguna.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Urutan Tampil</label>
                        <input type="number" name="display_order" value="0" min="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tampil di Dashboard</label>
                        <select name="is_visible"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                            <option value="1">Ya (Tampil)</option>
                            <option value="0">Tidak (Sembunyikan)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Logo Aplikasi (Opsional)</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, SVG. Maks. 2MB.</p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closeCreateModal()"
                        class="flex-1 py-2.5 border border-slate-300 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-kpi-700 text-white rounded-xl text-sm font-semibold hover:bg-kpi-800 transition-colors shadow-sm">
                        Simpan & Buat Aplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Application Modal -->
    <div id="editModal" class="modal fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60" onclick="closeEditModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10">
            <div class="flex justify-between items-center px-6 py-4 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Edit Aplikasi</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 18 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6 pt-4 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Nama Aplikasi <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-name" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Redirect URI / Callback URL <span class="text-red-500">*</span></label>
                    <input type="url" name="redirect" id="edit-redirect" required
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    <p class="text-xs text-slate-400 mt-1">URL Callback tempat server SSO mengirimkan OAuth authorization code.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" id="edit-description" rows="2"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent resize-none"
                        placeholder="Deskripsi singkat aplikasi..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Role Lokal yang Didukung (Pisahkan dengan koma)</label>
                    <input type="text" name="supported_roles" id="edit-supported-roles" placeholder="Contoh: Admin, Staff, pengguna"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    <p class="text-xs text-slate-400 mt-1">Role spesifik aplikasi yang bisa dipilih Admin saat pemetaan peran pengguna.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Pesan Maintenance (opsional)</label>
                    <textarea name="maintenance_message" id="edit-maintenance-message" rows="2"
                        class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent resize-none"
                        placeholder="Contoh: Sedang upgrade server, estimasi selesai pukul 17.00 WIB."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Urutan Tampil</label>
                        <input type="number" name="display_order" id="edit-display-order" min="0"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5">Tampil di Dashboard</label>
                        <select name="is_visible" id="edit-is-visible"
                            class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                            <option value="1">Ya (Tampil)</option>
                            <option value="0">Tidak (Sembunyikan)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Logo Aplikasi</label>
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, SVG. Maks. 2MB.</p>
                </div>

                <div class="flex items-center justify-between gap-3 pt-3 border-t border-slate-100">
                    <button type="button" onclick="deleteCurrentEditApp()"
                        class="px-3.5 py-2.5 bg-white border border-red-200 text-red-600 rounded-xl text-xs font-semibold hover:bg-red-50 transition-colors">
                        Hapus Aplikasi
                    </button>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2.5 bg-kpi-600 text-white rounded-xl text-xs font-semibold hover:bg-kpi-700 transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal Card -->
    <div id="deleteModal" class="modal fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6 text-center">
            <div class="w-14 h-14 rounded-2xl bg-red-50 border border-red-100 text-red-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900 mb-1">Hapus Aplikasi?</h3>
            <p class="text-sm text-slate-500 mb-6 leading-relaxed">
                Apakah Anda yakin ingin menghapus aplikasi <span id="deleteModalAppName" class="font-bold text-slate-900"></span> secara permanen? Seluruh riwayat akses dan role pengguna terkait akan terhapus.
            </p>
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()"
                        class="flex-1 py-2.5 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        Ya, Hapus Aplikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('new_client_secret'))
    <!-- Client Secret Display Modal -->
    <div id="secretModal" class="modal fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 active" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeSecretModal()"></div>
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10 p-6">
            <div class="w-14 h-14 rounded-2xl bg-green-50 border border-green-100 text-green-600 flex items-center justify-center mx-auto mb-4 shadow-sm">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-slate-900 text-center mb-1">Aplikasi Baru Berhasil Dibuat!</h3>
            <p class="text-xs text-slate-500 text-center mb-6 leading-relaxed">
                Salin Client ID dan Client Secret di bawah ini untuk konfigurasi aplikasi <strong>{{ session('new_client_name') }}</strong>.
                <span class="block text-red-500 font-bold mt-1">⚠️ Client Secret hanya akan ditampilkan sekali ini saja dan tidak dapat dipulihkan kembali!</span>
            </p>

            <div class="space-y-4 mb-6">
                <!-- Client ID -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">CLIENT ID</label>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5">
                        <code class="flex-1 text-xs font-semibold text-slate-800 break-all select-all font-mono" id="clientIdVal">{{ session('new_client_id') }}</code>
                        <button onclick="copyToClipboard('clientIdVal', this)" class="text-slate-400 hover:text-kpi-700 transition-colors p-1" title="Salin Client ID">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 5h6m-6 4h6m-6 4h5" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Client Secret -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">CLIENT SECRET</label>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5">
                        <code class="flex-1 text-xs font-semibold text-slate-800 break-all select-all font-mono" id="clientSecretVal">{{ session('new_client_secret') }}</code>
                        <button onclick="copyToClipboard('clientSecretVal', this)" class="text-slate-400 hover:text-kpi-700 transition-colors p-1" title="Salin Client Secret">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m-5 5h6m-6 4h6m-6 4h5" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" onclick="closeSecretModal()"
                class="w-full py-2.5 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors shadow-sm text-center">
                Saya Sudah Menyimpan Kredensial Ini
            </button>
        </div>
    </div>
    @endif

    <script>
        let currentEditAppId = null;
        let currentEditAppName = '';

        function copyToClipboard(elementId, btn) {
            const text = document.getElementById(elementId).innerText.trim();
            
            const animateSuccess = () => {
                const originalSvg = btn.innerHTML;
                btn.innerHTML = `<svg class="w-4 h-4 text-green-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
                setTimeout(() => {
                    btn.innerHTML = originalSvg;
                }, 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(animateSuccess)
                    .catch(err => {
                        console.error('Failed to copy using navigator.clipboard: ', err);
                        fallbackCopyToClipboard(text, animateSuccess);
                    });
            } else {
                fallbackCopyToClipboard(text, animateSuccess);
            }
        }

        function fallbackCopyToClipboard(text, callback) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    callback();
                } else {
                    console.error('Fallback copy command was unsuccessful');
                }
            } catch (err) {
                console.error('Fallback copy failed: ', err);
            }

            document.body.removeChild(textArea);
        }

        function closeSecretModal() {
            const secretModal = document.getElementById('secretModal');
            if (secretModal) {
                secretModal.classList.remove('active');
            }
        }

        function openCreateModal() {
            document.getElementById('createModal').classList.add('active');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.remove('active');
        }

        function openEditModal(id, name, redirect, description, supportedRoles, maintenanceMsg, displayOrder, isVisible) {
            currentEditAppId = id;
            currentEditAppName = name;
            document.getElementById('editForm').action = '/admin/applications/' + id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-redirect').value = redirect;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-supported-roles').value = supportedRoles;
            document.getElementById('edit-maintenance-message').value = maintenanceMsg;
            document.getElementById('edit-display-order').value = displayOrder;
            document.getElementById('edit-is-visible').value = isVisible;
            document.getElementById('editModal').classList.add('active');
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = '/admin/applications/' + id;
            document.getElementById('deleteModalAppName').textContent = '\'' + name + '\'';
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        function deleteCurrentEditApp() {
            closeEditModal();
            openDeleteModal(currentEditAppId, currentEditAppName);
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }
    </script>
</body>
</html>
