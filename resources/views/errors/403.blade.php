<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | Hotelia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }
        .error-card {
            max-width: 500px;
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .error-icon {
            font-size: 5rem;
            color: #ef4444;
            margin-bottom: 1.5rem;
        }
        .btn-primary {
            background-color: #f97316;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 600;
        }
        .btn-primary:hover {
            background-color: #ea580c;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-icon">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1 class="fw-bold mb-3">403</h1>
        <h4 class="text-dark mb-3">Access Denied</h4>
        <p class="text-muted mb-4">
            Sorry, you do not have permission to access this page. This area is reserved for <strong>Super Administrators</strong> only.
        </p>
        <div class="d-grid gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary rounded-3">Go Back</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary rounded-3 text-white">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
