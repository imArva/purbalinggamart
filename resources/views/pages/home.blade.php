<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Purbalingga Mart - Belanja kebutuhan harian dengan mudah.">
    <title>Purbalingga Mart - Belanja Mudah Setiap Hari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .home-page { background: #f4f8fd; color: #172b4d; min-height: 100vh; }
        .home-nav { background: rgba(255,255,255,.94); border-bottom: 1px solid #e5edf7; }
        .home-brand { color: #172b4d; font: 800 20px 'Manrope', sans-serif; }
        .home-brand:hover { color: #0758c9; }
        .home-brand-mark { display: inline-grid; place-items: center; width: 40px; height: 40px; margin-right: 9px; color: #fff; background: linear-gradient(145deg,#2680ee,#0758c9); border-radius: 12px; }
        .home-nav-link { color: #718096; font-size: 13px; font-weight: 600; }
        .home-nav-link:hover { color: #0758c9; }
        .hero { position: relative; overflow: hidden; min-height: 490px; display: flex; align-items: center; background: linear-gradient(90deg, rgba(6,59,131,.97) 0%, rgba(7,88,201,.88) 48%, rgba(7,88,201,.25) 100%), url('/img/Purbalingga_Mart_Hero.png') center/cover; }
        .hero::after { content: ''; position: absolute; width: 360px; height: 360px; right: 7%; bottom: -180px; border: 1px solid rgba(255,255,255,.18); border-radius: 50%; box-shadow: 0 0 0 55px rgba(255,255,255,.04), 0 0 0 110px rgba(255,255,255,.03); }
        .hero-content { position: relative; z-index: 1; max-width: 650px; color: #fff; }
        .hero-label { display: inline-flex; align-items: center; gap: 8px; padding: 7px 12px; margin-bottom: 18px; color: #ffe083; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.18); border-radius: 999px; font-size: 11px; font-weight: 700; letter-spacing: 1px; }
        .hero h1 { font: 800 clamp(38px, 6vw, 68px)/1.04 'Manrope', sans-serif; letter-spacing: -2.5px; margin: 0 0 20px; }
        .hero p { max-width: 520px; color: #dceaff; font-size: 16px; line-height: 1.7; margin-bottom: 28px; }
        .hero .btn-warning { color: #17345b; background: #ffc928; border-color: #ffc928; font-weight: 700; }
        .hero .btn-warning:hover { background: #ffda5c; border-color: #ffda5c; }
        .home-section { padding: 76px 0; }
        .home-section-title { max-width: 540px; margin: 0 auto 38px; text-align: center; }
        .home-section-title small { color: #0758c9; font-size: 10px; font-weight: 800; letter-spacing: 1.5px; }
        .home-section-title h2 { margin: 8px 0 10px; font: 800 29px 'Manrope', sans-serif; letter-spacing: -.8px; }
        .home-section-title p { color: #718096; margin: 0; }
        .feature-card { height: 100%; padding: 25px; background: #fff; border: 1px solid #edf0f6; border-radius: 16px; box-shadow: 0 8px 24px rgba(40,60,91,.04); }
        .feature-icon { display: grid; place-items: center; width: 45px; height: 45px; margin-bottom: 18px; color: #0758c9; background: #e8f3ff; border-radius: 13px; font-size: 20px; }
        .feature-card:nth-child(2) .feature-icon { color: #159b72; background: #e5f8f0; }
        .feature-card:nth-child(3) .feature-icon { color: #d88a17; background: #fff4e2; }
        .feature-card h3 { font: 700 16px 'Manrope', sans-serif; margin-bottom: 9px; }
        .feature-card p { color: #718096; font-size: 13px; line-height: 1.65; margin: 0; }
        .home-cta { padding: 38px 42px; color: #fff; background: linear-gradient(115deg,#063b83,#0758c9); border-radius: 20px; }
        .home-cta h2 { font: 800 25px 'Manrope', sans-serif; margin-bottom: 8px; }
        .home-cta p { color: #dceaff; margin: 0; }
        .home-footer { padding: 24px 0; color: #8b95a5; border-top: 1px solid #e5edf7; font-size: 12px; }
        @media (max-width: 767px) { .hero { min-height: 570px; } .hero::after { right: -120px; } .home-cta { padding: 28px 24px; } }
    </style>
</head>
<body class="home-page">
    <nav class="navbar navbar-expand-lg home-nav sticky-top">
        <div class="container py-2">
            <a class="navbar-brand home-brand" href="{{ route('home') }}">
                <span class="home-brand-mark"><i class="bi bi-cart-check-fill"></i></span>Purbalingga Mart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#homeNav" aria-label="Buka navigasi">
                <i class="bi bi-list"></i>
            </button>
            <div class="collapse navbar-collapse" id="homeNav">
                <div class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                    <a class="nav-link home-nav-link" href="#keunggulan">Keunggulan</a>
                    <a class="nav-link home-nav-link" href="#tentang">Tentang kami</a>
                    <a class="btn btn-primary btn-sm px-3" href="{{ route('kasir') }}">Buka Kasir <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="hero-content">
                <span class="hero-label"><i class="bi bi-stars"></i> BELANJA LEBIH PRAKTIS</span>
                <h1>Kebutuhan harian, selalu dekat.</h1>
                <p>Temukan produk pilihan untuk rumah dan keluarga di Purbalingga Mart. Segar, lengkap, dan siap menemani hari-hari Anda.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-warning px-4 py-3" href="{{ route('kasir') }}">Mulai transaksi <i class="bi bi-arrow-up-right ms-2"></i></a>
                    <a class="btn btn-outline-light px-4 py-3" href="#keunggulan">Lihat layanan</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="home-section" id="keunggulan">
            <div class="container">
                <div class="home-section-title">
                    <small>KENAPA PURBALINGGA MART?</small>
                    <h2>Belanja nyaman dalam satu tempat</h2>
                    <p>Kami hadir untuk membuat kebutuhan sehari-hari terasa lebih mudah.</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4"><div class="feature-card"><div class="feature-icon"><i class="bi bi-basket2"></i></div><h3>Produk pilihan</h3><p>Beragam kebutuhan harian tersusun rapi dan mudah ditemukan.</p></div></div>
                    <div class="col-md-4"><div class="feature-card"><div class="feature-icon"><i class="bi bi-lightning-charge"></i></div><h3>Proses cepat</h3><p>Transaksi praktis dengan sistem kasir yang membantu pelayanan lebih cepat.</p></div></div>
                    <div class="col-md-4"><div class="feature-card"><div class="feature-icon"><i class="bi bi-heart"></i></div><h3>Melayani sepenuh hati</h3><p>Kenyamanan pelanggan menjadi bagian penting dari setiap kunjungan.</p></div></div>
                </div>
            </div>
        </section>
        <section class="pb-5" id="tentang">
            <div class="container">
                <div class="home-cta d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
                    <div><h2>Siap melayani lebih baik?</h2><p>Kelola transaksi toko dengan mudah bersama Purbalingga Mart.</p></div>
                    <a class="btn btn-light px-4 py-3 flex-shrink-0" href="{{ route('dashboard') }}">Masuk dashboard <i class="bi bi-arrow-right ms-2"></i></a>
                </div>
            </div>
        </section>
    </main>
    <footer class="home-footer"><div class="container d-flex flex-column flex-sm-row justify-content-between gap-2"><span>© {{ date('Y') }} Purbalingga Mart</span><span>Belanja mudah setiap hari.</span></div></footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
