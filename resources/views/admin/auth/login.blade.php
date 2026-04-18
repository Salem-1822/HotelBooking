<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | HOTELIA Admin</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --brand-primary: #f97316;
            --brand-primary-dark: #ea580c;
            --bg-body: #f8fafc;
        }

        body {
            background-color: var(--bg-body);
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .login-header {
            background: #0f172a;
            padding: 3rem 2rem;
            text-align: center;
            color: white;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 0.5rem;
        }

        .login-body {
            padding: 2.5rem;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.85rem;
            color: #475569;
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-control:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
            background-color: white;
        }

        .btn-primary {
            background-color: var(--brand-primary);
            border: none;
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            background-color: var(--brand-primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(249, 115, 22, 0.3);
        }

        .alert {
            border-radius: 0.75rem;
            font-size: 0.85rem;
            border: none;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">HOTEL<span style="color: var(--brand-primary);">IA</span></div>
            <div class="small opacity-75 fw-bold text-uppercase ls-wider" style="font-size: 0.7rem; letter-spacing: 0.1em;">Admin Control Center</div>
        </div>
        
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger shadow-sm mb-4">
                    <ul class="mb-0 list-unstyled">
                        @foreach($errors->all() as $error)
                            <li><i class="bi bi-exclamation-circle-fill me-2"></i> {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="admin@hotelia.com" value="{{ old('email') }}" required autofocus>
                </div>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between">
                        <label for="password" class="form-label">Password</label>
                        <a href="#" class="text-decoration-none small fw-bold" style="color: var(--brand-primary);">Forgot?</a>
                    </div>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-muted" for="remember">
                            Remember this device
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    Sign In to Portal
                </button>
                
                <div class="text-center">
                    <span class="small text-muted">Secured by Hotelia IDP</span>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
