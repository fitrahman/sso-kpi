<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Atur Ulang Kata Sandi - KPI</title>
    <link rel="icon" type="image/png" href="{{ asset('logoKPI.png') }}">
    <!-- Load Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Serif+Malayalam:wght@600&family=Open+Sans:wght@600;700&family=Poppins:wght@700&display=swap" rel="stylesheet">

    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            position: relative;
            min-height: 100vh;
            background: #8E8E93;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            font-family: 'Open Sans', sans-serif;
        }

        /* Background image styled and positioned */
        .bg-container {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .bg-image {
            position: absolute;
            width: 3083px;
            height: 1067px;
            left: calc(50% - 2195px);
            top: -43px;
            background: url('/profilkpi.png') no-repeat;
            background-size: cover;
            transform: matrix(-1, 0, 0, 1, 0, 0);
            opacity: 0.85;
            filter: blur(5px);
        }

        .bg-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, rgba(192, 39, 45, 0.1) 25.03%, rgba(190, 18, 7, 0.5) 85.63%);
            z-index: 2;
        }

        /* Content layout wrapper */
        .page-content {
            position: relative;
            z-index: 5;
            width: 100%;
            max-width: 1440px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        /* Card Section (Center) */
        .card-container {
            width: 513px;
            min-height: 672px;
            background: #FFFFFF;
            border-radius: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 45px 30px;
            position: relative;
            transition: all 0.4s ease;
        }

        .card-container:hover {
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
        }

        /* Title */
        .card-title {
            font-family: 'Noto Serif Malayalam', serif;
            font-style: normal;
            font-weight: 600;
            font-size: 32px;
            line-height: 48px;
            color: #E0070B;
            margin-bottom: 25px;
            text-align: center;
        }

        /* logoKPI */
        .logo-kpi {
            width: 120px;
            height: 120px;
            background: url('/logoKPI.png') no-repeat center;
            background-size: contain;
            margin-bottom: 20px;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.06));
        }

        .logo-kpi:hover {
            transform: scale(1.05);
        }

        /* Notification Messages */
        .message-wrapper {
            width: 400px;
            margin-bottom: 20px;
        }

        .error-message {
            background: rgba(253, 243, 243, 0.8);
            border: 1px solid rgba(231, 76, 60, 0.3);
            color: #C0392B;
            padding: 14px 18px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.04);
            display: flex;
            flex-direction: column;
            gap: 4px;
            width: 100%;
        }

        /* Forms Layout */
        .form-wrapper {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
            width: 400px;
            justify-items: center;
            margin: 0 auto;
        }

        /* Input styling */
        .input-wrapper {
            display: flex;
            flex-direction: column;
            width: 400px;
        }

        .form-group {
            position: relative;
            width: 100%;
            height: 48px;
        }

        .form-group input {
            width: 100%;
            height: 100%;
            background: #EF8A83;
            border: 2px solid transparent;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            border-radius: 12px;
            padding: 0 16px;
            padding-right: 48px;
            color: #2C3D4F;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            outline: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-group input:hover {
            opacity: 0.95;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
        }

        .form-group input:focus {
            border-color: #EF8A83;
            background: #FFFFFF;
            color: #1A202C;
            box-shadow: 0 0 0 4px rgba(239, 138, 131, 0.25);
        }

        .form-group label {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 15px;
            color: #FFFFFF;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .form-group input:focus ~ label,
        .form-group input:not(:placeholder-shown) ~ label {
            top: -10px;
            left: 12px;
            transform: translateY(-50%) scale(0.85);
            color: #E0070B;
            font-weight: 700;
            background: #FFFFFF;
            padding: 0 4px;
        }

        .input-error {
            color: #E74C3C;
            font-size: 12px;
            font-weight: 700;
            margin-top: 4px;
            align-self: flex-start;
            padding-left: 12px;
        }

        /* Password visibility toggle */
        .password-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #FFFFFF;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 50%;
            transition: all 0.25s ease;
            z-index: 10;
        }

        .form-group input:focus ~ .password-toggle,
        .form-group input:not(:placeholder-shown) ~ .password-toggle {
            color: #2C3D4F;
        }

        .password-toggle:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .form-group input:focus ~ .password-toggle:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        /* Submit Button */
        .btn-submit {
            width: 294px;
            height: 54px;
            background: linear-gradient(180deg, #FF1C20 0%, #E0070B 100%);
            border-radius: 30px;
            border: none;
            color: #FFFFFF;
            font-family: 'Open Sans', sans-serif;
            font-weight: 600;
            font-size: 18px;
            line-height: 27px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(224, 7, 11, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(224, 7, 11, 0.4);
            background: linear-gradient(180deg, #FF3D40 0%, #E0070B 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        @media (max-width: 580px) {
            .card-container {
                width: 100%;
                max-width: 480px;
                padding: 30px 16px;
            }
            .form-grid {
                width: 100% !important;
            }
            .input-wrapper {
                width: 100% !important;
            }
        }
    </style>
</head>

<body>
    <div class="bg-container">
        <div class="bg-image"></div>
        <div class="bg-overlay"></div>
    </div>

    <div class="page-content">
        <div class="card-container">
            <div class="form-wrapper">
                <div class="logo-kpi"></div>
                <h2 class="card-title">Atur Ulang Kata Sandi</h2>

                @if ($errors->any())
                    <div class="message-wrapper">
                        <div class="error-message">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <form action="{{ route('password.update') }}" method="POST" style="width: 100%; display: flex; flex-direction: column; align-items: center;">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-grid">
                        <div class="input-wrapper">
                            <div class="form-group">
                                <input type="email" id="email" name="email" value="{{ old('email', $request->email) }}"
                                    class="{{ $errors->has('email') ? 'error' : '' }}" placeholder=" " required autofocus>
                                <label for="email">Alamat Email</label>
                            </div>
                            @error('email')
                                <span class="input-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-wrapper">
                            <div class="form-group">
                                <input type="password" id="password" name="password"
                                    class="{{ $errors->has('password') ? 'error' : '' }}" placeholder=" " required minlength="8" autocomplete="new-password">
                                <label for="password">Kata Sandi Baru</label>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password', this)" aria-label="Tampilkan kata sandi">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="input-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="input-wrapper">
                            <div class="form-group">
                                <input type="password" id="password-confirm" name="password_confirmation" placeholder=" " required minlength="8" autocomplete="new-password">
                                <label for="password-confirm">Konfirmasi Kata Sandi</label>
                                <button type="button" class="password-toggle" onclick="togglePasswordVisibility('password-confirm', this)" aria-label="Tampilkan kata sandi">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">Simpan Kata Sandi</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('svg');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }
    </script>
</body>

</html>
