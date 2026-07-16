<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HotelBooking Admin</title>
    <meta name="description" content="HotelBooking Admin Control Center — Secure Login">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --brand-primary:      #1E3A8A;
            --brand-primary-dark: #1D4ED8;
            --brand-accent:       #D4AF37;
            --sidebar-bg:         #0F172A;
            --bg-body:            #F0F5FF;
            --border-color:       #E5E7EB;
            --text-primary:       #1F2937;
        }

        * { box-sizing: border-box; }

        body {
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 40%, #1E293B 100%);
            font-family: 'Poppins', 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Decorative background shapes */
        body::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -15%;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.08) 0%, transparent 70%);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30, 58, 138, 0.4) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Login Card */
        .login-wrapper {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
            animation: cardEntry 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }

        @keyframes cardEntry {
            0%   { opacity: 0; transform: translateY(24px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 1.5rem;
            overflow: hidden;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.3), 0 8px 16px rgba(0, 0, 0, 0.15);
        }

        /* Header */
        .login-header {
            padding: 2.5rem 2.5rem 2rem;
            background: linear-gradient(135deg, #0F172A 0%, #1E3A8A 100%);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-header::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
        }

        .login-brand {
            font-size: 1.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #D4AF37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
            position: relative;
        }

        .login-brand-sub {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.18em;
            color: rgba(255, 255, 255, 0.4);
            font-weight: 600;
        }

        .login-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(212, 175, 55, 0.15);
            border: 1px solid rgba(212, 175, 55, 0.3);
            color: #D4AF37;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.3rem 0.85rem;
            border-radius: 50px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 1rem;
        }

        /* Body */
        .login-body {
            padding: 2.25rem 2.5rem 2.5rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #374151;
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .input-group-premium {
            position: relative;
        }

        .input-group-premium .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9CA3AF;
            font-size: 1rem;
            z-index: 2;
            pointer-events: none;
        }

        .form-control {
            padding: 0.75rem 1rem 0.75rem 2.75rem;
            border-radius: 0.75rem;
            border: 1.5px solid var(--border-color);
            background-color: #F9FAFB;
            font-size: 0.875rem;
            font-family: 'Poppins', sans-serif;
            color: var(--text-primary);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(30, 58, 138, 0.1);
            background-color: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--brand-primary) 0%, var(--brand-primary-dark) 100%);
            color: #fff;
            border: none;
            padding: 0.8rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            width: 100%;
            transition: all 0.2s ease;
            box-shadow: 0 4px 16px rgba(30, 58, 138, 0.3);
            letter-spacing: 0.01em;
            font-family: 'Poppins', sans-serif;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(30, 58, 138, 0.4);
            filter: brightness(1.05);
        }

        .btn-login:active { transform: translateY(0); }

        .alert {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            border: none;
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }

        .form-check-input:checked {
            background-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .forgot-link {
            color: var(--brand-primary);
            font-weight: 600;
            text-decoration: none;
            font-size: 0.8rem;
        }
        .forgot-link:hover { color: var(--brand-primary-dark); }

        .security-note {
            text-align: center;
            font-size: 0.72rem;
            color: #9CA3AF;
            margin-top: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-brand">HotelBooking</div>
                <div class="login-brand-sub">Management Platform</div>
                <div class="login-badge">
                    <i class="bi bi-shield-lock-fill"></i>
                    Admin Control Center
                </div>
            </div>

            <div class="login-body">
                @if($errors->any())
                    <div class="alert mb-4">
                        <ul class="mb-0 list-unstyled">
                            @foreach($errors->all() as $error)
                                <li><i class="bi bi-exclamation-circle-fill me-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('super_admin.login') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group-premium">
                            <i class="bi bi-envelope input-icon"></i>
                            <input type="email" name="email" id="email" class="form-control"
                                placeholder="admin@hotelbooking.com"
                                value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">Password</label>
                            <a href="#" class="forgot-link">Forgot password?</a>
                        </div>
                        <div class="input-group-premium">
                            <i class="bi bi-lock input-icon"></i>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="••••••••" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label small text-muted" for="remember" style="font-size:0.82rem;">
                                Keep me signed in
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Portal
                    </button>

                    <div class="security-note">
                        <i class="bi bi-shield-check text-success"></i>
                        Secured by HotelBooking IDP &mdash; All sessions are encrypted
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
