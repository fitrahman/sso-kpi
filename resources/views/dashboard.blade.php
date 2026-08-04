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


        <!-- Applications Carousel -->
        @php
            $gradients = [
                ['from' => '#1e3a5f', 'to' => '#2563eb'],
                ['from' => '#064e3b', 'to' => '#059669'],
                ['from' => '#3b0764', 'to' => '#7c3aed'],
                ['from' => '#78350f', 'to' => '#d97706'],
                ['from' => '#881337', 'to' => '#e11d48'],
                ['from' => '#1e1b4b', 'to' => '#4f46e5'],
            ];
            $visibleClients = $allClients->filter(fn($c) => $c->is_visible)->values();
            $totalVisible   = $visibleClients->count();
        @endphp

        <div id="appCarousel" class="relative">

            {{-- py-4 gives room for the card hover lift so it's not clipped --}}
            <div id="carouselViewport" class="overflow-hidden py-4 -my-4">
                <div id="carouselTrack"
                     class="flex gap-6 transition-transform duration-500 ease-in-out"
                     style="width: max-content;">

                    @foreach ($visibleClients as $idx => $client)
                        @php
                            $g             = $gradients[$idx % count($gradients)];
                            $isMaintenance = $client->is_maintenance && auth()->user()->role !== 'admin';
                            $isAdminMaint  = $client->is_maintenance && auth()->user()->role === 'admin';
                            $appGatewayUrl = route('app.gateway', ['appName' => $client->name]);
                        @endphp

                        @if ($isMaintenance)
                            {{-- ── Maintenance Card (Disabled Click) ── --}}
                            <div class="carousel-card flex-shrink-0 w-72 sm:w-80 flex flex-col rounded-2xl overflow-hidden
                                        bg-white border border-slate-200 shadow-md opacity-70 cursor-not-allowed">

                                {{-- TOP: visual header --}}
                                <div class="relative h-48 overflow-hidden select-none"
                                     style="background: linear-gradient(135deg, {{ $g['from'] }} 0%, {{ $g['to'] }} 100%);">

                                    @if ($client->logo_path)
                                        <img src="{{ Storage::url($client->logo_path) }}"
                                             alt=""
                                             class="absolute inset-0 w-full h-full object-cover grayscale opacity-60">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/10"></div>
                                    @else
                                        <div class="absolute inset-0 opacity-[0.08]"
                                             style="background-image: radial-gradient(white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
                                        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full opacity-10 bg-white"></div>
                                        <div class="absolute -left-6 -bottom-6 w-28 h-28 rounded-full opacity-10 bg-white"></div>
                                    @endif

                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="inline-flex items-center gap-1.5 bg-black/40 backdrop-blur-sm
                                                     text-amber-300 text-[11px] font-semibold
                                                     px-2.5 py-1 rounded-full border border-amber-400/40">
                                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                            Maintenance
                                        </span>
                                    </div>

                                    <div class="absolute bottom-3 left-4 z-10">
                                        <img src="{{ asset('logoKPI.png') }}"
                                             alt="KPI SSO"
                                             class="h-7 w-auto object-contain drop-shadow opacity-40">
                                    </div>

                                    <div class="absolute inset-0 flex items-center justify-center px-6 z-10">
                                        <h3 class="text-xl font-extrabold text-white text-center drop-shadow-lg leading-snug tracking-tight opacity-50">
                                            {{ $client->name }}
                                        </h3>
                                    </div>
                                </div>

                                {{-- BOTTOM: content --}}
                                <div class="flex flex-col flex-grow p-5">
                                    <p class="text-sm text-slate-500 leading-relaxed flex-grow mb-5 line-clamp-3">
                                        Aplikasi sedang dalam pemeliharaan dan tidak dapat diakses sementara.
                                    </p>

                                    <button disabled
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                                                   border border-slate-200 bg-slate-50
                                                   text-sm font-semibold text-slate-400 cursor-not-allowed w-fit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Tidak Tersedia
                                    </button>
                                </div>
                            </div>
                        @else
                            {{-- ── Active Application Card (Clickable Entire Card) ── --}}
                            <a href="{{ $appGatewayUrl }}"
                               class="carousel-card group flex-shrink-0 w-72 sm:w-80 flex flex-col rounded-2xl overflow-hidden
                                      bg-white border border-slate-200 shadow-md cursor-pointer
                                      transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:border-kpi-300">

                                {{-- TOP: visual header --}}
                                <div class="relative h-48 overflow-hidden select-none"
                                     style="background: linear-gradient(135deg, {{ $g['from'] }} 0%, {{ $g['to'] }} 100%);">

                                    @if ($client->logo_path)
                                        <img src="{{ Storage::url($client->logo_path) }}"
                                             alt=""
                                             class="absolute inset-0 w-full h-full object-cover opacity-90 group-hover:scale-105 transition-transform duration-500">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-black/10"></div>
                                    @else
                                        <div class="absolute inset-0 opacity-[0.08]"
                                             style="background-image: radial-gradient(white 1.5px, transparent 1.5px); background-size: 28px 28px;"></div>
                                        <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full opacity-10 bg-white group-hover:scale-110 transition-transform duration-500"></div>
                                        <div class="absolute -left-6 -bottom-6 w-28 h-28 rounded-full opacity-10 bg-white"></div>
                                    @endif

                                    <div class="absolute top-3 left-3 z-10">
                                        @if ($isAdminMaint)
                                            <span class="inline-flex items-center gap-1.5 bg-black/40 backdrop-blur-sm
                                                         text-amber-300 text-[11px] font-semibold
                                                         px-2.5 py-1 rounded-full border border-amber-400/40">
                                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></span>
                                                Maintenance (Admin)
                                            </span>
                                        @else
                                            <span class="bg-black/25 backdrop-blur-sm text-white/90
                                                         text-[11px] font-semibold px-2.5 py-1 rounded-full">
                                                Aplikasi
                                            </span>
                                        @endif
                                    </div>

                                    <div class="absolute bottom-3 left-4 z-10">
                                        <img src="{{ asset('logoKPI.png') }}"
                                             alt="KPI SSO"
                                             class="h-7 w-auto object-contain drop-shadow opacity-90">
                                    </div>

                                    <div class="absolute inset-0 flex items-center justify-center px-6 z-10">
                                        <h3 class="text-xl font-extrabold text-white text-center drop-shadow-lg leading-snug tracking-tight group-hover:scale-105 transition-transform duration-300">
                                            {{ $client->name }}
                                        </h3>
                                    </div>
                                </div>

                                {{-- BOTTOM: content --}}
                                <div class="flex flex-col flex-grow p-5">
                                    <p class="text-sm text-slate-500 leading-relaxed flex-grow mb-5 line-clamp-3">
                                        {{ $client->description ?: 'Portal akses terintegrasi melalui KPI SSO.' }}
                                    </p>

                                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-lg
                                                border border-slate-300 bg-white
                                                text-sm font-semibold text-slate-700
                                                group-hover:bg-kpi-700 group-hover:text-white group-hover:border-kpi-700
                                                transition-all duration-200 w-fit">
                                        @if ($isAdminMaint)
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full"></span>
                                        @endif
                                        Buka Aplikasi
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200"
                                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        @endif
                    @endforeach

                </div>{{-- /#carouselTrack --}}
            </div>{{-- /#carouselViewport --}}

            {{-- ── Dot indicators (only when > 3 apps) ─────────────── --}}
            @if ($totalVisible > 3)
                <div id="carouselDots" class="flex justify-center gap-2 mt-5">
                    @php $totalPages = (int) ceil($totalVisible / 3); @endphp
                    @for ($d = 0; $d < $totalPages; $d++)
                        <button data-dot="{{ $d }}"
                                onclick="appCarousel.goTo({{ $d }})"
                                class="carousel-dot w-2 h-2 rounded-full transition-all duration-300
                                       {{ $d === 0 ? 'bg-slate-700 w-5' : 'bg-slate-300' }}">
                        </button>
                    @endfor
                </div>
            @endif

        </div>{{-- /#appCarousel --}}

        {{-- ── Carousel JS (auto-slide, dot indicators) ─────────────── --}}
        <script>
        (function () {
            const PER_PAGE   = 3;
            const total      = {{ $totalVisible }};
            const totalPages = Math.ceil(total / PER_PAGE);
            let   page       = 0;
            let   autoTimer  = null;

            const track = document.getElementById('carouselTrack');
            const dots  = document.querySelectorAll('.carousel-dot');

            function cardSlotWidth() {
                const card = document.querySelector('.carousel-card');
                return card ? card.getBoundingClientRect().width + 24 : 344;
            }

            function render() {
                if (!track) return;
                track.style.transform = `translateX(-${page * PER_PAGE * cardSlotWidth()}px)`;

                dots.forEach((dot, i) => {
                    if (i === page) {
                        dot.classList.add('bg-slate-700', 'w-5');
                        dot.classList.remove('bg-slate-300', 'w-2');
                    } else {
                        dot.classList.add('bg-slate-300', 'w-2');
                        dot.classList.remove('bg-slate-700', 'w-5');
                    }
                });
            }

            function next() {
                page = (page + 1) % totalPages;
                render();
            }

            function startAuto() {
                if (total <= PER_PAGE) return;          // no auto-slide if ≤3
                autoTimer = setInterval(next, 4000);    // advance every 4 s
            }

            function stopAuto() {
                clearInterval(autoTimer);
            }

            window.appCarousel = {
                goTo(p) { stopAuto(); page = p; render(); startAuto(); },
            };

            // Pause on hover
            const viewport = document.getElementById('carouselViewport');
            if (viewport) {
                viewport.addEventListener('mouseenter', stopAuto);
                viewport.addEventListener('mouseleave', startAuto);
            }

            // Touch/swipe support
            let touchStartX = 0;
            if (viewport) {
                viewport.addEventListener('touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
                viewport.addEventListener('touchend', e => {
                    const dx = e.changedTouches[0].clientX - touchStartX;
                    if (Math.abs(dx) > 40) {
                        stopAuto();
                        page = dx < 0
                            ? Math.min(page + 1, totalPages - 1)
                            : Math.max(page - 1, 0);
                        render();
                        startAuto();
                    }
                }, { passive: true });
            }

            window.addEventListener('resize', render, { passive: true });

            render();
            startAuto();
        })();
        </script>


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
