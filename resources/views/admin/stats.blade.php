<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistik & Audit - Admin SSO KPI</title>
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
                <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                    Dashboard SSO
                </a>
                
                <a href="{{ route('admin.stats') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg bg-kpi-50 text-kpi-700 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-kpi-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                    Statistik & Audit
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
                <a href="{{ route('admin.clients') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-slate-600 hover:text-kpi-700 hover:bg-slate-50 transition-colors">
                    <svg class="w-5 h-5 mr-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
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

    <!-- Main Content -->
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
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900">Statistik & Audit</h1>
                <p class="text-slate-500 mt-1">Pantau total pengguna, volume aktivitas login harian, dan rekaman audit trails login terbaru.</p>
            </div>

            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <!-- Total Users Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Pengguna</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalUsers }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5"><span class="text-amber-500 font-semibold">{{ $pendingUsers }}</span> pending</p>
                    </div>
                </div>

                <!-- Today Logins Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Login Hari Ini</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $todayLogins }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sesi login sukses</p>
                    </div>
                </div>

                <!-- Active Clients Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aplikasi Klien</p>
                        <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $activeClientsCount }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sistem klien aktif</p>
                    </div>
                </div>

                <!-- System Status Card -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <span class="relative flex h-3.5 w-3.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Sistem</p>
                        <h3 class="text-lg font-bold text-emerald-600 mt-1">Normal</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Semua integrasi aman</p>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="mb-8">
                <!-- Users Distribution Chart -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-col">
                    <h3 class="text-sm font-bold text-slate-800 tracking-wide mb-4">Tren Aktivitas Login Aplikasi Klien (7 Hari Terakhir)</h3>
                    <div class="relative w-full h-80">
                        <canvas id="appsDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- User Activity Log -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col mb-10">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-bold text-slate-800 text-sm tracking-wide">Log Aktivitas Pengguna</h2>
                </div>
                @if ($recentActivities->isEmpty())
                    <div class="px-6 py-10 text-center text-slate-400 text-sm">Belum ada aktivitas pengguna.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                                <tr>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Pengguna</th>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Aktivitas</th>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Alamat IP</th>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Browser / User Agent</th>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Detail Tambahan</th>
                                    <th class="px-6 py-3.5 text-left bg-slate-50">Waktu</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @foreach ($recentActivities as $log)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $log->user->name ?? 'Tamu/Anonim' }}</div>
                                            <div class="text-xs text-slate-500">{{ $log->user->email ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $actColor = match($log->activity) {
                                                    'login_success' => 'bg-green-50 text-green-700 border border-green-200',
                                                    'login_failed'  => 'bg-red-50 text-red-700 border border-red-200',
                                                    'logout'        => 'bg-slate-50 text-slate-600 border border-slate-200',
                                                    'password_changed' => 'bg-amber-50 text-amber-700 border border-amber-200',
                                                    'profile_updated'  => 'bg-blue-50 text-blue-700 border border-blue-200',
                                                    default         => 'bg-slate-50 text-slate-600 border border-slate-200',
                                                };
                                                $actLabel = match($log->activity) {
                                                    'login_success' => 'Login Sukses',
                                                    'login_failed'  => 'Login Gagal',
                                                    'logout'        => 'Logout',
                                                    'password_changed' => 'Ubah Password',
                                                    'profile_updated'  => 'Ubah Profil',
                                                    default         => $log->activity,
                                                };
                                            @endphp
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $actColor }}">{{ $actLabel }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-700 font-mono text-xs">{{ $log->ip_address }}</td>
                                        <td class="px-6 py-4 text-xs text-slate-600 max-w-xs truncate" title="{{ $log->user_agent }}">
                                            {{ $log->user_agent }}
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-500">
                                            {{ $log->details ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-400">
                                            {{ $log->created_at->format('d M Y, H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </main>

    <!-- Chart JS and Logic -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Apps Distribution Line Chart
            const chartDays = @json($chartDays);
            const appsData = @json($appsChartData);
            
            const lineColors = [
                '#dc2626', // Merah KPI
                '#3b82f6', // Biru
                '#10b981', // Emerald
                '#f59e0b', // Amber
                '#8b5cf6', // Ungu
                '#ec4899', // Pink
                '#f97316'  // Orange
            ];

            const datasets = appsData.map((item, index) => {
                const color = lineColors[index % lineColors.length];
                return {
                    label: item.name,
                    data: item.data,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    borderWidth: 2.5,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: color
                };
            });

            new Chart(document.getElementById('appsDistributionChart'), {
                type: 'line',
                data: {
                    labels: chartDays,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { family: 'Inter', weight: '500' } }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>
