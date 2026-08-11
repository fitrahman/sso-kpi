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

        /* CSS Variables for profile modal theme */
        :root {
            --profile-brand-gradient: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            --profile-focus-ring: 0 0 0 3px rgba(220, 38, 38, 0.4);
            --profile-chip-shadow-hover: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        /* Profile Modal Animasi & Transisi */
        #profileModal {
            transition: opacity 0.2s ease-out, visibility 0.2s ease-out;
        }
        #profileModal.modal-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        #profileModal .modal-content-panel {
            transform: scale(0.95) translateY(15px);
            transition: transform 0.2s ease-out;
        }
        #profileModal.modal-visible .modal-content-panel {
            transform: scale(1) translateY(0);
        }

        /* Hover state badge portal chip */
        .portal-chip {
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .portal-chip:hover {
            transform: translateY(-1px);
            box-shadow: var(--profile-chip-shadow-hover);
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
                    <nav class="flex items-center gap-1">
                        <a href="{{ route('admin.stats') }}" class="p-2 text-slate-500 hover:text-kpi-700 hover:bg-slate-100 rounded-xl transition-all" title="Portal Admin (Statistik)">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </a>
                    </nav>
                    <div class="h-6 w-px bg-slate-200"></div>
                @endif

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
                                            Pemeliharaan
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
                                                Pemeliharaan (Admin)
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
    @php
        $lastLoginLog = auth()->user()->activityLogs()->where('activity', 'login_success')->latest()->first();
        $lastLoginTime = $lastLoginLog ? $lastLoginLog->created_at->translatedFormat('d M Y, H:i') . ' WIB' : '-';
        $photoUrl = $user->photo_url ?? null;
        $initials = strtoupper(substr($user->name ?? 'U', 0, 1));
    @endphp
    <div id="profileModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 opacity-0 pointer-events-none transition-all duration-200 ease-out hidden" role="dialog" aria-modal="true" aria-labelledby="profile-modal-title">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="toggleModal('profileModal')"></div>

        <!-- Modal panel -->
        <div class="modal-content-panel bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden transform scale-95 transition-all duration-200 ease-out" style="max-height: 90vh; display: flex; flex-direction: column;">
            
            <!-- Non-scrollable Header (Cover Banner + Avatar + User Info) -->
            <div class="relative shrink-0 bg-white">
                <!-- Cover Banner (Gradient) -->
                <div class="h-24 bg-gradient-to-r from-red-600 to-red-500 w-full relative">
                    <!-- Title -->
                    <div class="absolute top-4 left-4 z-20">
                        <h3 class="text-white text-base font-bold tracking-tight" id="profile-modal-title">Profil Saya</h3>
                    </div>
                    <!-- Close Button (X) -->
                    <div class="absolute top-4 right-4 z-20">
                        <button onclick="toggleModal('profileModal')" class="text-white/85 hover:text-white bg-slate-900/20 hover:bg-slate-900/40 rounded-full p-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-white" aria-label="Tutup detail profil">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <!-- Avatar and User Info (Overlapping the banner) -->
                <div class="flex flex-col items-center -mt-10 pb-4 relative z-10">
                    @if (!empty($photoUrl))
                        <img src="{{ $photoUrl }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full border-4 border-white shadow-md object-cover">
                    @else
                        <div class="w-20 h-20 rounded-full bg-red-100 text-red-600 border-4 border-white shadow-md flex items-center justify-center text-3xl font-extrabold uppercase">
                            {{ $initials }}
                        </div>
                    @endif
                    <h4 class="text-xl font-extrabold text-slate-900 mt-3 leading-tight text-center px-4">{{ $user->name }}</h4>
                    <span class="text-sm font-semibold text-slate-500 mt-1">{{ $user->role }}</span>
                </div>
            </div>

            <!-- Scrollable Content -->
            <div class="overflow-y-auto px-6 pb-6 pt-2 flex-grow" id="profileModalScrollable">
                <!-- Info Kontak Section -->
                <div class="space-y-4">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Kontak</h5>
                    
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 space-y-3.5">
                        <!-- Email -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3 text-slate-600 min-w-0">
                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="font-medium truncate" id="profileEmail">{{ $user->email }}</span>
                            </div>
                        </div>

                        <!-- No Telp -->
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-3 text-slate-600 min-w-0">
                                <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span class="font-medium truncate" id="profilePhone">{{ $user->phone ?? '-' }}</span>
                            </div>
                        </div>

                        <!-- ID Pegawai / NIP -->
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 014 0m-9 3h14m-11 4h2m-2 4h5" /></svg>
                            <div class="flex-1 flex justify-between">
                                <span class="font-medium">NIP / ID Pegawai</span>
                                <span class="font-bold text-slate-900">KPI-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>

                        <!-- Status Akun -->
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                            <div class="flex-1 flex justify-between items-center">
                                <span class="font-medium">Status Akun</span>
                                @if($user->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100 shadow-sm">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100 shadow-sm">Nonaktif</span>
                                @endif
                            </div>
                        </div>

                        <!-- Login Terakhir -->
                        <div class="flex items-center gap-3 text-sm text-slate-600">
                            <svg class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <div class="flex-1 flex justify-between">
                                <span class="font-medium">Login Terakhir</span>
                                <span class="font-semibold text-slate-700">{{ $lastLoginTime }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Akses Portal Aplikasi Section -->
                <div class="mt-6">
                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Akses Portal Aplikasi</h5>
                    @if ($user->isAdmin())
                        <div class="p-3 bg-red-50 border border-red-100 rounded-2xl flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600 shrink-0 animate-pulse"></span>
                            <span class="text-xs font-bold text-red-800">Semua Portal Terbuka (Administrator Utama)</span>
                        </div>
                    @else
                        <div class="flex flex-wrap gap-2.5">
                            @forelse ($approvedApps as $app)
                                @php
                                    $clientRole = $user->clientRoles->where('oauth_client_id', $app->id)->first()?->role ?? 'pengguna';
                                    
                                    // Generate different colored tags based on application ID for nice aesthetics
                                    $colorClasses = match($app->id % 4) {
                                        0 => 'bg-indigo-50 text-indigo-700 border-indigo-150 hover:bg-indigo-100',
                                        1 => 'bg-teal-50 text-teal-700 border-teal-150 hover:bg-teal-100',
                                        2 => 'bg-amber-50 text-amber-700 border-amber-150 hover:bg-amber-100',
                                        default => 'bg-rose-50 text-rose-700 border-rose-150 hover:bg-rose-100'
                                    };
                                @endphp
                                <a href="{{ $app->redirect }}" target="_blank" rel="noopener noreferrer" 
                                   class="portal-chip inline-flex flex-col px-3.5 py-2 rounded-xl text-xs font-semibold border transition-all duration-150 {{ $colorClasses }}" 
                                   aria-label="Buka aplikasi {{ $app->name }}">
                                    <span class="font-extrabold text-slate-900">{{ $app->name }}</span>
                                    <span class="text-[10px] opacity-75 mt-0.5">Role: {{ ucfirst($clientRole) }}</span>
                                </a>
                            @empty
                                <span class="text-xs text-slate-400 italic font-medium">Belum memiliki akses ke sistem apa pun. Hubungi Administrator.</span>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-6 border-t border-slate-100 bg-slate-50 shrink-0">
                <button onclick="toggleModal('profileModal')" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-md focus:outline-none focus:ring-2 focus:ring-red-400" aria-label="Tutup modal profil">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        let previouslyFocusedElement = null;

        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (!modal) return;
            
            if (modal.classList.contains('hidden') || !modal.classList.contains('modal-visible')) {
                // Open modal
                previouslyFocusedElement = document.activeElement;
                modal.classList.remove('hidden');
                
                // Allow browser to register class removal before starting animation
                setTimeout(() => {
                    modal.classList.add('modal-visible');
                    setupFocusTrap(id);
                    // Focus on the first close button or primary button
                    const focusable = modal.querySelectorAll('button, [href]');
                    if (focusable.length > 0) {
                        focusable[0].focus();
                    }
                }, 10);
            } else {
                // Close modal
                modal.classList.remove('modal-visible');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    if (previouslyFocusedElement) {
                        previouslyFocusedElement.focus();
                    }
                }, 200);
            }
        }

        // Close modal when Escape key is pressed
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('profileModal');
                if (modal && modal.classList.contains('modal-visible')) {
                    toggleModal('profileModal');
                }
            }
        });

        // Focus trap helper
        function setupFocusTrap(modalId) {
            const modal = document.getElementById(modalId);
            if (!modal) return;
            
            const focusableElements = modal.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            // Remove existing listener if any
            if (modal._focusTrapListener) {
                modal.removeEventListener('keydown', modal._focusTrapListener);
            }

            modal._focusTrapListener = function(e) {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        lastElement.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        firstElement.focus();
                        e.preventDefault();
                    }
                }
            };

            modal.addEventListener('keydown', modal._focusTrapListener);
        }

        // Copy value to clipboard helper
        function copyToClipboardText(elementId, btn) {
            const text = document.getElementById(elementId).innerText.trim();
            
            const animateSuccess = () => {
                const originalSvg = btn.innerHTML;
                btn.innerHTML = `<svg class="h-4 w-4 text-green-600 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>`;
                setTimeout(() => {
                    btn.innerHTML = originalSvg;
                }, 2000);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text)
                    .then(animateSuccess)
                    .catch(err => {
                        console.error('Failed to copy using clipboard API: ', err);
                        fallbackCopyToClipboard(text, animateSuccess);
                    });
            } else {
                fallbackCopyToClipboard(text, animateSuccess);
            }
        }

        function fallbackCopyToClipboard(text, callback) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                callback();
            } catch (err) {
                console.error('Fallback copy failed: ', err);
            }
            document.body.removeChild(textArea);
        }
    </script>
</body>
</html>
