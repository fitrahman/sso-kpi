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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard SSO
                </a>
                
                <a href="{{ route('admin.users') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-kpi-50 text-kpi-700 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-kpi-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    Manajemen Pengguna
                </a>
                
                <a href="{{ route('admin.accessRequests') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Permintaan Akses
                </a>

                <a href="{{ route('admin.profileRequests') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    Edit Profil Requests
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
            <!-- Mobile Menu Dropdown logic could go here -->
            <a href="{{ route('dashboard') }}" class="text-sm text-kpi-700 font-medium">Ke Dashboard</a>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 sm:p-8">
            <div class="max-w-6xl mx-auto">
                
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen Pengguna</h1>
                    <p class="text-slate-500 mt-1">Kelola seluruh pendaftar dan pengguna aktif di dalam sistem.</p>
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

                <!-- Stats/Tabs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <a href="{{ route('admin.users', ['status' => 'pending']) }}" class="bg-white rounded-xl p-5 border shadow-sm flex items-center transition-all hover:shadow-md {{ $status == 'pending' ? 'border-kpi-500 ring-1 ring-kpi-500' : 'border-slate-200' }}">
                        <div class="w-12 h-12 rounded-full {{ $status == 'pending' ? 'bg-kpi-100 text-kpi-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-500">Menunggu Persetujuan</div>
                            <div class="text-2xl font-bold {{ $pendingCount > 0 ? 'text-kpi-700' : 'text-slate-900' }}">{{ $pendingCount }}</div>
                        </div>
                    </a>
                    
                    <a href="{{ route('admin.users', ['status' => 'approved']) }}" class="bg-white rounded-xl p-5 border shadow-sm flex items-center transition-all hover:shadow-md {{ $status == 'approved' ? 'border-green-500 ring-1 ring-green-500' : 'border-slate-200' }}">
                        <div class="w-12 h-12 rounded-full {{ $status == 'approved' ? 'bg-green-100 text-green-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center mr-4">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-slate-500">Pengguna Aktif</div>
                            <div class="text-2xl font-bold text-slate-900">{{ $approvedCount }}</div>
                        </div>
                    </a>
                </div>

                <!-- Main Data Table Area -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    
                    <!-- Table Toolbar -->
                    <div class="p-4 border-b border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-800">
                            Daftar {{ $status == 'pending' ? 'Pendaftar Baru' : 'Pengguna Aktif' }}
                        </h2>
                        
                        <div class="relative w-full sm:w-72">
                            <input type="text" id="live-search" value="{{ request('search') }}" placeholder="Cari nama, email..." class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-kpi-500 focus:border-kpi-500">
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <form action="{{ route('admin.users') }}" method="GET" class="hidden" id="search-form">
                                <input type="hidden" name="status" value="{{ $status }}">
                                <input type="text" name="search" id="form-search-input">
                            </form>
                            @if (request('search'))
                                <a href="{{ route('admin.users', ['status' => $status]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 hover:text-red-500 font-medium">Reset</a>
                            @endif
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse" id="users-table">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                    <th class="px-6 py-4">Pengguna</th>
                                    <th class="px-6 py-4">Divisi / Role</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Bergabung</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($users as $user)
                                    <tr class="user-row hover:bg-slate-50/80 transition-colors">
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
                                            @if ($status === 'pending')
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" 
                                                            class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                            data-name="{{ $user->name }}" data-email="{{ $user->email }}" data-phone="{{ $user->phone }}" data-role="{{ $user->role }}" data-date="{{ $user->created_at->format('d M Y H:i') }}"
                                                            onclick="openDetailModal(this)">
                                                        Detail
                                                    </button>
                                                    <form action="{{ route('admin.users.approve', $user->id) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700 shadow-sm">Setujui</button>
                                                    </form>
                                                    <form action="{{ route('admin.users.reject', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tolak dan hapus user ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-md text-xs font-semibold hover:bg-red-50 hover:border-red-300 shadow-sm">Tolak</button>
                                                    </form>
                                                </div>
                                            @else
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-white border border-slate-300 rounded-md text-xs font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                        Edit
                                                    </a>
                                                    
                                                    @if ($user->role !== 'admin' && $user->id !== Auth::id())
                                                        @if ($user->status === 'approved')
                                                            <form action="{{ route('admin.users.deactivate', $user->id) }}" method="POST" class="inline-block">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-amber-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-amber-700 shadow-sm">
                                                                    Nonaktifkan
                                                                </button>
                                                            </form>
                                                        @elseif ($user->status === 'inactive')
                                                            <form action="{{ route('admin.users.activate', $user->id) }}" method="POST" class="inline-block">
                                                                @csrf
                                                                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700 shadow-sm">
                                                                    Aktifkan
                                                                </button>
                                                            </form>
                                                        @endif

                                                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun ini secara permanen?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-red-700 shadow-sm">
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-slate-500 font-medium">
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
                                    <span class="px-3 py-1.5 border border-slate-200 rounded text-sm text-slate-400 bg-slate-50 cursor-not-allowed">Seb</span>
                                @else
                                    <a href="{{ $users->previousPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">Seb</a>
                                @endif
                                
                                @if ($users->hasMorePages())
                                    <a href="{{ $users->nextPageUrl() }}" class="px-3 py-1.5 border border-slate-300 rounded text-sm font-medium text-slate-700 bg-white hover:bg-slate-50">Lanjut</a>
                                @else
                                    <span class="px-3 py-1.5 border border-slate-200 rounded text-sm text-slate-400 bg-slate-50 cursor-not-allowed">Lanjut</span>
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

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('live-search');
            const formSearchInput = document.getElementById('form-search-input');
            const searchForm = document.getElementById('search-form');
            const rows = document.querySelectorAll('.user-row');

            // Client side quick search
            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                
                rows.forEach(row => {
                    const name = row.querySelector('.user-name').textContent.toLowerCase();
                    const email = row.querySelector('.user-email-cell').textContent.toLowerCase();
                    if (name.includes(term) || email.includes(term)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Server-side fallback on press Enter
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formSearchInput.value = searchInput.value;
                    searchForm.submit();
                }
            });
        });
    </script>
</body>
</html>
