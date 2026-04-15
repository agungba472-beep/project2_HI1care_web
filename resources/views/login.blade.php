<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Login Admin HI!-CARE" />
    <meta name="author" content="Team JAS" />
    <title>Login Administrator - HI!-CARE</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary: #0ea5e9; /* Light blue */
            --primary-dark: #0284c7;
            --brand-bg: #0f172a; /* Sangat gelap, hampir hitam biru */
            --text-main: #334155;
            --text-light: #64748b;
            --border: #e2e8f0;
            --bg-light: #f8fafc;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background-color: #ffffff;
            overflow-x: hidden;
        }

        /* ── Split Layout ── */
        .login-wrapper {
            display: flex;
            width: 100%;
        }

        /* Bagian Kiri: Branding */
        .brand-section {
            flex: 1.2;
            background: var(--brand-bg);
            color: white;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        /* Pattern / Dekorasi di Background Kiri */
        .brand-section::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .brand-section::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
            z-index: 10;
        }
        
        .brand-logo i {
            color: var(--primary);
            font-size: 36px;
        }

        .brand-content {
            z-index: 10;
        }

        .brand-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            color: #ffffff;
        }

        .brand-content h1 span {
            color: var(--primary);
        }

        .brand-content p {
            font-size: 1.125rem;
            color: #94a3b8;
            max-width: 480px;
            line-height: 1.6;
        }

        .brand-footer {
            z-index: 10;
            color: #64748b;
            font-size: 0.875rem;
        }

        /* Bagian Kanan: Form Login */
        .form-section {
            flex: 1;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
        }

        .form-header {
            margin-bottom: 2.5rem;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-bg);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .form-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        /* ── Alert Error (Beda style, lebih modern) ── */
        .alert-error {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .alert-error i.icon-err {
            color: #ef4444;
            font-size: 1.25rem;
            margin-top: 2px;
        }

        .alert-error .err-text {
            flex: 1;
        }

        .alert-error strong {
            display: block;
            color: #991b1b;
            font-size: 0.875rem;
            margin-bottom: 2px;
        }

        .alert-error span {
            color: #b91c1c;
            font-size: 0.875rem;
        }

        .close-alert {
            background: none;
            border: none;
            color: #ef4444;
            cursor: pointer;
            opacity: 0.5;
        }
        .close-alert:hover { opacity: 1; }

        /* ── Input Groups ── */
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i.leading-icon {
            position: absolute;
            left: 1rem;
            color: #94a3b8;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            font-size: 1rem;
            color: var(--brand-bg);
            background-color: var(--bg-light);
            border: 1px solid var(--border);
            border-radius: 8px;
            transition: all 0.2s ease;
            outline: none;
        }

        .form-control::placeholder {
            color: #cbd5e1;
        }

        .form-control:hover {
            border-color: #cbd5e1;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        }

        .input-wrapper:focus-within i.leading-icon {
            color: var(--primary);
        }

        .form-control.is-invalid {
            border-color: #ef4444;
            background-color: #fef2f2;
        }

        /* Toggle Password */
        .toggle-btn {
            position: absolute;
            right: 1rem;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 0;
            font-size: 1rem;
            transition: color 0.2s;
        }
        
        .toggle-btn:hover, .toggle-btn:focus {
            color: var(--primary);
            outline: none;
        }

        /* ── Submit Button ── */
        .btn-submit {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            background-color: var(--primary-dark);
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ── Responsive Mobile ── */
        @media (max-width: 900px) {
            .login-wrapper {
                flex-direction: column;
            }
            .brand-section {
                flex: none;
                padding: 2rem;
                min-height: auto;
            }
            .brand-content h1 {
                font-size: 2rem;
                margin-top: 1rem;
            }
            .brand-content p {
                font-size: 1rem;
            }
            .brand-footer {
                display: none; /* Hide footer on mobile left section */
            }
            .form-section {
                padding: 3rem 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        
        <div class="brand-section">
            <div class="brand-logo">
                <i class="fa-solid fa-hand-holding-medical"></i>
                HI!-CARE
            </div>

            <div class="brand-content">
                <h1>Sistem Monitoring<br>Pasien <span>HIV</span></h1>
                <p>Platform administrasi dan pendampingan kesehatan berbasis ekosistem terpadu untuk efisiensi terapi ARV di Puskesmas Pamanukan.</p>
            </div>

            <div class="brand-footer">
                &copy; 2026 JAS COMPANY. All rights reserved.
            </div>
        </div>

        <div class="form-section">
            <div class="form-container">
                
                <div class="form-header">
                    <h2>Selamat Datang</h2>
                    <p>Masuk ke akun Anda untuk mengelola sistem.</p>
                </div>

                @if ($errors->any())
                    <div class="alert-error" id="errorAlert">
                        <i class="fa-solid fa-circle-exclamation icon-err"></i>
                        <div class="err-text">
                            <strong>Login Gagal</strong>
                            <span>{{ $errors->first() }}</span>
                        </div>
                        <button type="button" class="close-alert" onclick="document.getElementById('errorAlert').style.display='none'">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="input-group">
                        <label for="username" class="input-label">Username</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-user leading-icon"></i>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   class="form-control @error('username') is-invalid @enderror" 
                                   placeholder="Masukkan username" 
                                   value="{{ old('username') }}" 
                                   required 
                                   autofocus
                                   autocomplete="username">
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="password" class="input-label">Password</label>
                        <div class="input-wrapper">
                            <i class="fa-solid fa-lock leading-icon"></i>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Masukkan password" 
                                   required
                                   style="padding-right: 3rem;"
                                   autocomplete="current-password">
                            
                            <button type="button" class="toggle-btn" id="togglePassword">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        Masuk
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                    </button>
                </form>

            </div>
        </div>

    </div>

    <script>
        // Script untuk Toggle Password Visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        toggleBtn.addEventListener('click', function () {
            // Ubah tipe input
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Ubah ikon
            const icon = this.querySelector('i');
            if(type === 'text') {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>

</body>
</html>