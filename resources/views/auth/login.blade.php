<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Purbalingga Mart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
<main class="auth-shell">
    <section class="auth-card">
        <a class="auth-brand" href="{{ route('home') }}"><span class="brand-mark"><i class="bi bi-cart-check-fill"></i></span><span><strong>Purbalingga Mart</strong><small>Point of Sale</small></span></a>
        <div class="auth-heading"><span class="eyebrow">SELAMAT DATANG KEMBALI</span><h1>Masuk ke akun Anda</h1><p>Kelola transaksi dan operasional toko dengan mudah.</p></div>
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <form method="POST" action="{{ route('login.store') }}" class="auth-form">
            @csrf
            <div><label class="form-label" for="email">Email</label><input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div><label class="form-label" for="password">Password</label><input id="password" name="password" type="password" class="form-control @error('password') is-invalid @enderror" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <label class="form-check"><input class="form-check-input" type="checkbox" name="remember" value="1"><span class="form-check-label">Ingat saya</span></label>
            <button class="btn btn-primary w-100" type="submit">Masuk <i class="bi bi-arrow-right ms-1"></i></button>
        </form>
        <p class="auth-switch">Akun dikelola oleh administrator toko.</p>
    </section>
</main>
</body>
</html>
