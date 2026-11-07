<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Purbalingga Mart - Sistem Point of Sale dan manajemen toko.">
  <title>@yield('title', 'Dashboard') - Purbalingga Mart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  @stack('styles')
</head>
<body>
<div class="menu-overlay" id="menuOverlay"></div>
<div class="app-shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="{{ url('/dashboard') }}">
      <span class="brand-mark"><i class="bi bi-cart-check-fill"></i></span>
      <span><strong>Purbalingga Mart</strong><small>Point of Sale</small></span>
    </a>
    <div class="sidebar-label">MENU UTAMA</div>
    <nav class="nav flex-column app-nav" aria-label="Menu utama">
      @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manajer')
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ url('/dashboard') }}"><i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span></a>
      @endif
      <a class="nav-link {{ request()->routeIs('pos') ? 'active' : '' }}" href="{{ url('/kasir') }}"><i class="bi bi-cart3"></i><span>Kasir / POS</span><span class="nav-pill">POS</span></a>
      @if(auth()->user()->role === 'admin' || auth()->user()->role === 'manajer')
      <div class="sidebar-label mt-4">MANAJEMEN</div>
        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ url('/produk') }}"><i class="bi bi-box-seam"></i><span>Produk</span></a>
        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ url('/kategori') }}"><i class="bi bi-tags"></i><span>Kategori</span></a>
        <a class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}" href="{{ url('/stok') }}"><i class="bi bi-clipboard2-data"></i><span>Stok Barang</span></a>
        <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ url('/transaksi') }}"><i class="bi bi-receipt"></i><span>Riwayat Transaksi</span></a>
      @endif
      @if(auth()->user()->role === 'admin')
      <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}"><i class="bi bi-people"></i><span>Kelola Akun</span></a>
      @endif
    </nav>
    <div class="sidebar-bottom">
      <div class="help-card">
        <div class="help-icon"><i class="bi bi-lightbulb"></i></div>
        <strong>Selamat berbelanja!</strong>
        <p>Belanja hemat setiap hari bersama Purbalingga Mart.</p>
      </div>
      <div class="sidebar-foot"><span class="status-dot"></span> Sistem siap digunakan</div>
    </div>
  </aside>

  <div class="main-area">
    <header class="topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn icon-button d-lg-none" id="menuToggle" aria-label="Buka menu"><i class="bi bi-list"></i></button>
        <div><div class="eyebrow">@yield('eyebrow', 'OVERVIEW')</div><h1 class="top-title">@yield('page-title', 'Dashboard')</h1></div>
      </div>
      <div class="topbar-actions">
        <div class="date-chip d-none d-md-flex"><i class="bi bi-calendar3"></i><span>{{ now()->translatedFormat('D, d M Y') }}</span></div>
        <div class="dropdown">
          <button class="profile-button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</span>
            <span class="profile-copy"><strong>{{ auth()->user()->name ?? 'Administrator' }}</strong><small>{{ auth()->user()->role ?? 'Administrator' }}</small></span>
            <i class="bi bi-chevron-down small"></i>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-2">
            <li><span class="dropdown-item-text small text-muted">Akun aktif</span></li>
            <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ url('/logout') }}">@csrf
                <button class="dropdown-item" type="submit"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
              </form>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <main class="page-content" id="pageContent" tabindex="-1">
      @yield('content')
    </main>
    <footer class="main-footer"><span>© {{ date('Y') }} Purbalingga Mart</span><span>Sistem Point of Sale · Laravel</span></footer>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script src="{{ asset('/js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
