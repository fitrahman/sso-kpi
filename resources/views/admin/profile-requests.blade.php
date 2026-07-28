<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Permintaan Edit Profil - Admin SSO KPI</title>
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
                
                <a href="{{ route('admin.accessRequests') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                    Permintaan Akses
                </a>

                <a href="{{ route('admin.profileRequests') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-kpi-50 text-kpi-700 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-kpi-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
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
        <header class="md:hidden bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 flex-shrink-0">
            <div class="font-bold text-lg text-slate-900">Admin Portal</div>
            <a href="{{ route('dashboard') }}" class="text-sm text-kpi-700 font-medium">Ke Dashboard</a>
        </header>

        <!-- Page Content -->
        <div class="flex-grow overflow-y-auto p-4 sm:p-8">
            <div class="max-w-6xl mx-auto">
                
                <div class="mb-8">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Permintaan Perubahan Profil</h1>
                    <p class="text-slate-500 mt-1">Setujui atau tolak permintaan pembaruan data profil yang diajukan oleh pengguna.</p>
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

                <!-- Data Table Area -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 bg-slate-50/50">
                        <h2 class="text-lg font-bold text-slate-800">Permintaan Edit Profil (Pending)</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                    <th class="px-6 py-4">Pengguna Saat Ini</th>
                                    <th class="px-6 py-4">Data Yang Diajukan (Perubahan)</th>
                                    <th class="px-6 py-4">Tanggal Pengajuan</th>
                                    <th class="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($requests as $req)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        
                                        <!-- Current User Info -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-900">{{ $req->user->name }}</div>
                                            <div class="text-xs text-slate-500">Email: {{ $req->user->email }}</div>
                                            <div class="text-xs text-slate-500">Telp: {{ $req->user->phone ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">Divisi: {{ $req->user->role }}</div>
                                        </td>

                                        <!-- Proposed Changes -->
                                        <td class="px-6 py-4">
                                            <div class="space-y-1 text-xs font-medium">
                                                <div>
                                                    <span class="text-slate-500">Nama:</span>
                                                    <span class="{{ $req->name !== $req->user->name ? 'text-kpi-700 font-bold bg-red-50 px-1 rounded' : 'text-slate-900' }}">
                                                        {{ $req->name }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-slate-500">Email:</span>
                                                    <span class="{{ $req->email !== $req->user->email ? 'text-kpi-700 font-bold bg-red-50 px-1 rounded' : 'text-slate-900' }}">
                                                        {{ $req->email }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-slate-500">Telepon:</span>
                                                    <span class="{{ $req->phone !== $req->user->phone ? 'text-kpi-700 font-bold bg-red-50 px-1 rounded' : 'text-slate-900' }}">
                                                        {{ $req->phone ?? '-' }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-slate-500">Divisi:</span>
                                                    <span class="{{ $req->role !== $req->user->role ? 'text-kpi-700 font-bold bg-red-50 px-1 rounded' : 'text-slate-900' }}">
                                                        {{ $req->role }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Date Submitted -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                            {{ $req->created_at->format('d M Y H:i') }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <form action="{{ route('admin.profileRequests.approve', $req->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Setujui perubahan profil ini? Data profil pengguna akan langsung diperbarui.');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 border border-transparent rounded-md text-xs font-semibold text-white hover:bg-green-700 shadow-sm transition-colors">Setujui</button>
                                                </form>
                                                <form action="{{ route('admin.profileRequests.reject', $req->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Tolak perubahan profil ini?');">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-md text-xs font-semibold hover:bg-red-50 hover:border-red-300 shadow-sm transition-colors">Tolak</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-slate-500 font-medium">
                                            Tidak ada permintaan perubahan profil yang tertunda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($requests->hasPages())
                        <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between">
                            <div class="text-sm text-slate-500">
                                Menampilkan <span class="font-medium">{{ $requests->firstItem() }}</span> - <span class="font-medium">{{ $requests->lastItem() }}</span> dari <span class="font-medium">{{ $requests->total() }}</span>
                            </div>
                            <div>
                                {{ $requests->links() }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </main>

</body>
</html>
