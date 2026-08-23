<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Assertiv Logix Invoice System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1rem;
            padding: 2.5rem;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.2);
        }
        .brand-logo {
            width: 56px;
            height: 56px;
            background: #eff6ff;
            color: #2563eb;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin: 0 auto 1.25rem;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="fa-solid fa-file-invoice-dollar"></i>
    </div>
    <h4 class="fw-bold text-center text-dark mb-1">Assertiv Logix</h4>
    <p class="text-muted text-center fs-14 mb-4">Client & Invoice Management System</p>

    @if($errors->any())
        <div class="alert alert-danger py-2 fs-14 rounded-3 mb-3">
            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-medium fs-14 text-secondary">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" name="email" class="form-class form-control border-start-0 ps-0" placeholder="admin@assertivlogix.com" value="{{ old('email') }}" required autofocus>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium fs-14 text-secondary">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check fs-14">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-muted" for="remember">Remember me</label>
            </div>
            <a href="#" class="fs-14 text-primary text-decoration-none fw-medium">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3">
            Sign In <i class="fa-solid fa-arrow-right ms-2"></i>
        </button>
    </form>

    <div class="mt-4 p-3 bg-light rounded-3 text-center fs-13 text-muted">
        <strong>Demo Admin Credentials:</strong><br>
        Email: <code>admin@assertivlogix.com</code> | Password: <code>password</code>
    </div>
</div>

</body>
</html>
