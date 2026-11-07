@extends('layouts.app')
@section('title', 'Kasir / POS')
@section('eyebrow', 'TRANSAKSI PENJUALAN')
@section('page-title', 'Kasir / POS')
@section('content')
@php($categories = $categories ?? collect())
@php($products = $products ?? collect())
@php($cartItems = $cartItems ?? collect())
<div class="section-heading"><div><h2>Transaksi Penjualan</h2><p>Pilih produk yang dibeli pelanggan, atur jumlah, lalu proses pembayaran.</p></div><span class="badge-soft success"><i class="bi bi-circle-fill" style="font-size:6px"></i> Kasir aktif</span></div>
<div class="pos-layout">
  <section class="pos-products-panel">
    <form id="posSearchForm" class="pos-toolbar" method="GET" action="{{ url('/kasir') }}"><div class="search-box"><i class="bi bi-search"></i><input name="q" class="form-control" placeholder="Cari produk atau scan kode..." value="{{ request('q') }}"></div><button class="btn btn-primary" type="submit">Cari</button></form>
    <div class="category-chips"><a class="category-chip {{ !request('category') ? 'active' : '' }}" href="{{ url('/kasir') }}" data-pos-filter>Semua produk</a>@foreach($categories as $category)<a class="category-chip {{ request('category') == $category->id ? 'active' : '' }}" href="{{ url('/kasir') }}?category={{ $category->id }}" data-pos-filter>{{ $category->name }}</a>@endforeach</div>
    <div class="d-flex justify-content-between align-items-center mb-3"><span class="muted-note">Pilih item untuk menambahkan ke keranjang</span><span class="muted-note" id="productCount">{{ $products->count() }} item</span></div>
    <div class="product-grid" id="productGrid">
      @forelse($products as $product)
      <form class="pos-product {{ $product->stock <= 0 ? 'out-of-stock' : '' }}" method="POST" action="{{ url('/kasir/keranjang') }}" data-cart-add>@csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <button type="submit" class="border-0 bg-transparent p-0 w-100 text-start" {{ $product->stock <= 0 ? 'disabled' : '' }}>
          <div class="product-art tone-blue"><i class="bi bi-box-seam"></i></div><div class="product-name">{{ $product->name }}</div><div class="product-code">{{ $product->code }}</div><div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div><div class="stock-small"><span>Stok {{ $product->stock }}</span>@if($product->stock <= $product->min_stock)<span class="text-warning"><i class="bi bi-exclamation-circle"></i></span>@endif</div><span class="add-plus"><i class="bi bi-plus-lg"></i></span>
        </button>
      </form>
      @empty <div class="empty-state empty-filter"><i class="bi bi-search"></i><strong>Produk tidak ditemukan</strong><span>Produk akan ditampilkan setelah data tersedia.</span></div> @endforelse
    </div>
  </section>
  <aside class="card-panel cart-card">
    <div class="cart-header"><h3><i class="bi bi-basket2 me-2 text-primary"></i>Keranjang <span class="cart-count" id="cartCount">{{ $cartItems->sum('quantity') }}</span></h3>
      <form method="POST" action="{{ url('/kasir/keranjang/kosongkan') }}" data-cart-clear>@csrf @method('DELETE')<button class="btn btn-sm btn-light" type="submit"><i class="bi bi-trash3 me-1"></i>Kosongkan</button></form>
    </div>
    <div class="cart-body" id="cartBody">
      @forelse($cartItems as $item)
      <div class="cart-item"><div class="table-avatar tone-blue"><i class="bi bi-box-seam"></i></div><div class="cart-item-main"><div class="cart-item-name">{{ $item->product->name ?? $item['name'] ?? 'Produk' }}</div><div class="cart-item-price">Rp {{ number_format($item->price ?? 0, 0, ',', '.') }}</div>
      <div class="cart-item-bottom"><form class="qty-control" method="POST" action="{{ url('/kasir/keranjang/kuantitas') }}" data-cart-quantity>@csrf<input type="hidden" name="product_id" value="{{ $item->product_id }}"><button name="change" value="-1">−</button><span>{{ $item->quantity }}</span><button name="change" value="1">+</button></form><span class="line-total">Rp {{ number_format(($item->price ?? 0) * ($item->quantity ?? 0), 0, ',', '.') }}</span></div></div></div>
      @empty <div class="empty-cart"><i class="bi bi-basket"></i><strong>Keranjang masih kosong</strong><span>Pilih produk untuk memulai transaksi.</span></div> @endforelse
    </div>
    <div class="cart-summary">
      <div class="summary-row"><span>Subtotal</span><strong id="cartSubtotal">Rp {{ number_format($subtotal ?? 0, 0, ',', '.') }}</strong></div><div class="summary-row"><span>Diskon</span><strong id="cartDiscount">Rp {{ number_format($discount ?? 0, 0, ',', '.') }}</strong></div>
      <div class="summary-row total"><span>Total bayar</span><strong id="cartTotal">Rp {{ number_format($total ?? $subtotal ?? 0, 0, ',', '.') }}</strong></div>
      <form id="checkoutForm" method="POST" action="{{ url('/kasir/checkout') }}">@csrf
        <label class="payment-label" for="paymentAmount">Nominal uang diterima</label><div class="payment-input-wrap"><span>Rp</span><input id="paymentAmount" name="payment_amount" class="form-control" type="number" min="0" step="500" placeholder="Masukkan nominal pembayaran" required></div>
        <button type="submit" class="btn btn-primary checkout-btn mt-3" {{ $cartItems->isEmpty() ? 'disabled' : '' }}>Selesaikan pesanan <i class="bi bi-arrow-right"></i></button>
      </form>
      <div class="low-stock-note"><i class="bi bi-info-circle me-1"></i>Validasi pembayaran dan pengurangan stok diproses oleh backend.</div>
    </div>
  </aside>
</div>
@endsection
