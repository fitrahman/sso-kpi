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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard SSO
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Manajemen Pengguna
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
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Topbar -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-2 text-sm">
                <a href="{{ route('admin.clients') }}" class="text-slate-500 hover:text-kpi-700 font-medium">Manajemen Aplikasi</a>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-900">{{ $client->name }}</span>
            </div>
            <a href="{{ route('admin.clients') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </header>

        <div class="p-6 max-w-7xl w-full mx-auto space-y-6">



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

            <!-- Users Section Header & Search -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Daftar Pengguna & Role Lokal</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola status akses dan role khusus pengguna di aplikasi {{ $client->name }}.</p>
                    </div>

                    <form id="searchAppUsersForm" method="GET" action="{{ route('admin.clients.users', $client->id) }}" class="flex items-center gap-2">
                        <div class="relative">
                            <input type="text" id="app-user-search" name="search" value="{{ $search }}" placeholder="Cari nama / email / role..."
                                class="w-64 border border-slate-300 rounded-xl pl-9 pr-8 py-2 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            @if (!empty($search))
                                <a href="{{ route('admin.clients.users', $client->id) }}" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-red-500" title="Reset pencarian">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </a>
                            @endif
                        </div>
                        <button type="submit" class="px-3.5 py-2 bg-slate-900 text-white rounded-xl text-xs font-semibold hover:bg-slate-800 transition-colors">
                            Cari
                        </button>
                    </form>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="w-12 px-4 py-3.5 bg-slate-50 text-center">No</th>
                                <th class="px-6 py-3.5 bg-slate-50">Pengguna</th>
                                <th class="px-6 py-3.5 bg-slate-50">Role Global</th>
                                <th class="px-6 py-3.5 bg-slate-50">Akses Portal</th>
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
                                    $currentAccess = $accessMap[$u->id] ?? 'none';
                                    $currentLocalRole = $localRolesMap[$u->id] ?? '';

                                    // Build role list including current role if custom
                                    $roleOptions = $supportedRoles;
                                    if (!empty($currentLocalRole) && !in_array($currentLocalRole, $roleOptions)) {
                                        $roleOptions[] = $currentLocalRole;
                                    }
                                @endphp
                                <tr class="user-app-row hover:bg-slate-50/80 transition-colors">
                                    <!-- No -->
                                    <td class="px-4 py-4 text-center text-xs font-semibold text-slate-400 whitespace-nowrap">
                                        {{ $users->firstItem() + $loop->index }}
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
                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.clients.users.update', ['id' => $client->id, 'userId' => $u->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            @if (!empty($currentLocalRole))
                                                <input type="hidden" name="local_role" value="{{ $currentLocalRole }}">
                                            @endif
                                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                                <input type="checkbox" name="has_access" value="1" {{ $currentAccess === 'approved' ? 'checked' : '' }}
                                                    onchange="this.form.submit()"
                                                    class="h-4 w-4 rounded border-slate-300 text-kpi-600 focus:ring-kpi-500 cursor-pointer">
                                                <span class="text-xs font-semibold {{ $currentAccess === 'approved' ? 'text-emerald-700' : 'text-slate-400' }}">
                                                    {{ $currentAccess === 'approved' ? 'Memiliki akses' : 'Tidak memiliki akses' }}
                                                </span>
                                            </label>
                                        </form>
                                    </td>

                                    <!-- Local Role Dropdown Form -->
                                    <td class="px-6 py-4">
                                        <form action="{{ route('admin.clients.users.update', ['id' => $client->id, 'userId' => $u->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            @if ($currentAccess === 'approved')
                                                <input type="hidden" name="has_access" value="1">
                                            @endif
                                            <select name="local_role" onchange="this.form.submit()" class="w-48 border border-slate-300 rounded-lg px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-kpi-500 font-medium bg-white cursor-pointer">
                                                <option value="">Tidak memiliki role</option>
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
                    <span class="text-xs text-slate-400 font-medium">Scroll untuk melihat riwayat</span>
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
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-lg relative z-10">
            <div class="flex justify-between items-center px-6 py-5 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">Edit Info Aplikasi</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

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
                        <div class="mb-3 p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ Storage::url($client->logo_path) }}" alt="Gambar saat ini" class="h-12 w-20 object-cover rounded-lg border border-slate-200">
                                <div>
                                    <p class="text-xs font-semibold text-slate-700">Gambar Aktif</p>
                                    <p class="text-[11px] text-slate-400">Terpasang sebagai background kartu</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('deleteLogoForm').submit()"
                                class="px-2.5 py-1.5 bg-red-50 text-red-700 border border-red-200 text-xs font-semibold rounded-lg hover:bg-red-100">
                                Hapus
                            </button>
                        </div>
                    @endif

                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg,image/svg+xml"
                        class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-slate-100 file:text-slate-700 file:font-semibold hover:file:bg-slate-200 cursor-pointer">
                    <p class="text-xs text-slate-400 mt-1">Format: PNG, JPG, SVG. Maks. 2MB.</p>
                </div>

                <div class="flex gap-3 pt-2">
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

            @if ($client->logo_path)
                <form id="deleteLogoForm" action="{{ route('admin.clients.logo.delete', $client->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>

    <script>
        function openEditModal() {
            document.getElementById('editModal').classList.add('active');
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
        }

        // Instant Live Search for Application Users
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('app-user-search');
            const rows = document.querySelectorAll('.user-app-row');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();
                    
                    // Instant Client-side Filter
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        if (text.includes(query)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });

                    // Auto submit form to query database if user pauses typing
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        document.getElementById('searchAppUsersForm').submit();
                    }, 600);
                });
            }
        });
    </script>
</body>
</html>
