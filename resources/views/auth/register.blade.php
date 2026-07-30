<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - KPI SSO Portal</title>
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
        .bg-pattern {
            background-color: #f8fafc;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 32px 32px;
        }
        .peer:focus ~ .peer-focus\:scale-75,
        .peer:not(:placeholder-shown) ~ .peer-focus\:scale-75 {
            transform: scale(0.75) translateY(-1.5rem);
            color: #b91c1c;
        }
        .form-step { display: none; opacity: 0; transition: opacity 0.3s ease; }
        .form-step.active { display: block; opacity: 1; }
        input[type="radio"]:checked + div {
            border-color: #b91c1c;
            background-color: #fef2f2;
            color: #b91c1c;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-5xl w-full bg-white rounded-3xl shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left: Illustration -->
        <div class="hidden md:flex md:w-1/2 bg-slate-900 bg-pattern flex-col justify-center items-center p-12 relative overflow-hidden text-white text-center">
            <div class="absolute inset-0 bg-slate-800/90 mix-blend-multiply"></div>
            <div class="relative z-10 w-full max-w-sm flex flex-col items-center">
                <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-24 w-auto mb-8">
                <h2 class="text-3xl font-bold mb-4 tracking-tight">Bergabung dengan KPI</h2>
                <p class="text-slate-300 text-lg leading-relaxed">
                    Daftarkan diri anda untuk mendapatkan akses ke berbagai layanan aplikasi internal secara aman.
                </p>
            </div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 border-4 border-slate-700 rounded-full"></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 border-4 border-slate-700 rounded-full"></div>
        </div>

        <!-- Right: Register Form -->
        <div class="w-full md:w-1/2 p-8 sm:p-12 lg:p-16 flex flex-col justify-center">
            
            <div class="mb-8 text-center md:text-left">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center h-12 mb-6 md:hidden">
                    <img src="{{ asset('logoKPI.png') }}" alt="KPI Logo" class="h-12 w-auto">
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">Buat Akun</h1>
                <p class="text-slate-500 font-medium">Lengkapi data diri anda di bawah ini.</p>
            </div>

            <!-- Stepper Indicator -->
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center">
                    <div id="ind-1" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-kpi-700 text-white transition-colors">1</div>
                    <div class="w-16 h-1 bg-slate-200 mx-2 rounded relative">
                        <div id="ind-line" class="absolute top-0 left-0 h-full bg-kpi-700 rounded w-0 transition-all duration-300"></div>
                    </div>
                    <div id="ind-2" class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-slate-200 text-slate-500 transition-colors">2</div>
                </div>
            </div>

            @if ($errors->any())
                <div class="bg-red-50/80 border border-red-200/60 p-4 mb-6 rounded-2xl flex items-start gap-3 shadow-sm">
                    <div class="flex-shrink-0 mt-0.5">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-sm font-bold text-red-900 leading-tight">Registrasi Gagal</h3>
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

            <form method="POST" action="{{ route('register.post') }}" class="w-full" id="registerForm">
                @csrf
                
                <!-- STEP 1: Data Diri -->
                <div id="step1" class="form-step active space-y-5">
                    
                    <div class="relative">
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder=" "
                            class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border-2 border-slate-200 appearance-none focus:outline-none focus:border-kpi-600 peer transition-colors" />
                        <label for="name" class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">Nama Lengkap</label>
                    </div>

                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder=" "
                            class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border-2 border-slate-200 appearance-none focus:outline-none focus:border-kpi-600 peer transition-colors" />
                        <label for="email" class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">Alamat Email</label>
                    </div>

                    <div class="relative">
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required placeholder=" "
                            class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border-2 border-slate-200 appearance-none focus:outline-none focus:border-kpi-600 peer transition-colors" />
                        <label for="phone" class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">Nomor Telepon</label>
                    </div>

                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="8" placeholder=" "
                            class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border-2 border-slate-200 appearance-none focus:outline-none focus:border-kpi-600 peer transition-colors pr-12" />
                        <label for="password" class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">Kata Sandi</label>
                        <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePass('password', this)">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </button>
                    </div>

                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder=" "
                            class="block px-4 pb-2.5 pt-6 w-full text-base text-slate-900 bg-transparent rounded-xl border-2 border-slate-200 appearance-none focus:outline-none focus:border-kpi-600 peer transition-colors pr-12" />
                        <label for="password_confirmation" class="absolute text-base text-slate-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 peer-focus:text-kpi-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 bg-white px-1">Konfirmasi Kata Sandi</label>
                    </div>
                    
                    <button type="button" onclick="nextStep()" class="mt-6 w-full py-3.5 bg-slate-900 text-white rounded-xl font-semibold shadow-sm hover:bg-slate-800 transition-colors">
                        Lanjutkan
                    </button>
                </div>

                <!-- STEP 2: Pilih Divisi -->
                <div id="step2" class="form-step">
                    <p class="text-sm font-semibold text-slate-700 mb-3">Pilih Divisi/Peran anda:</p>
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        @php
                            $roles = ['Humas', 'Kepegawaian', 'Manajerial', 'Hukum', 'Visualisasi Data', 'Pengawasan Siaran'];
                        @endphp
                        @foreach($roles as $role)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="{{ $role }}" class="peer sr-only" {{ old('role') == $role ? 'checked' : '' }} required>
                            <div class="px-4 py-3 border-2 border-slate-200 rounded-xl text-center text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                {{ $role }}
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="flex gap-4">
                        <button type="button" onclick="prevStep()" class="w-1/3 py-3.5 border-2 border-slate-200 text-slate-700 rounded-xl font-semibold hover:bg-slate-50 transition-colors">
                            Kembali
                        </button>
                        <button type="submit" id="submitBtn" class="w-2/3 flex justify-center py-3.5 bg-kpi-700 text-white rounded-xl font-semibold shadow-sm hover:bg-kpi-800 transition-colors">
                            <span id="btnText">Daftar Akun</span>
                            <svg id="btnSpinner" class="hidden animate-spin ml-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-8 text-center text-sm text-slate-500 font-medium">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="font-bold text-kpi-600 hover:text-kpi-800 transition-colors">Masuk di sini</a>
            </div>
        </div>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        function nextStep() {
            const inputs = document.getElementById('step1').querySelectorAll('input');
            let isValid = true;
            inputs.forEach(input => {
                if (!input.checkValidity()) { input.reportValidity(); isValid = false; }
            });

            const pass = document.getElementById('password');
            const conf = document.getElementById('password_confirmation');
            if (isValid && pass.value !== conf.value) {
                conf.setCustomValidity('Konfirmasi tidak cocok.');
                conf.reportValidity();
                isValid = false;
            } else { conf.setCustomValidity(''); }

            if (isValid) {
                document.getElementById('step1').classList.remove('active');
                document.getElementById('step2').classList.add('active');
                
                document.getElementById('ind-line').style.width = '100%';
                document.getElementById('ind-1').className = "w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-slate-900 text-white transition-colors";
                document.getElementById('ind-2').className = "w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-kpi-700 text-white transition-colors";
            }
        }

        function prevStep() {
            document.getElementById('step2').classList.remove('active');
            document.getElementById('step1').classList.add('active');
            
            document.getElementById('ind-line').style.width = '0%';
            document.getElementById('ind-1').className = "w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-kpi-700 text-white transition-colors";
            document.getElementById('ind-2').className = "w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm bg-slate-200 text-slate-500 transition-colors";
        }

        document.getElementById('registerForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-75');
            document.getElementById('btnText').textContent = 'Memproses...';
            document.getElementById('btnSpinner').classList.remove('hidden');
        });
    </script>
</body>
</html>
