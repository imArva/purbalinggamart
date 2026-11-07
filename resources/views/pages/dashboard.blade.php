@extends('layouts.app')
@section('title', 'Dashboard')
@section('eyebrow', 'OVERVIEW')
@section('page-title', 'Dashboard')
@section('content')
@php
  $stats = $stats ?? [];
  $recentTransactions = $recentTransactions ?? collect();
  $lowStockProducts = $lowStockProducts ?? collect();
@endphp
<div class="section-heading">
  <div><h2>Selamat datang, {{ auth()->user()->name ?? 'Admin' }} 👋</h2><p>Berikut ringkasan kondisi Purbalingga Mart hari ini.</p></div>
  <a class="btn btn-primary" href="{{ url('/kasir') }}"><i class="bi bi-plus-lg me-2"></i>Transaksi baru</a>
</div>
<div class="stats-grid">
  <div class="stat-card"><div class="stat-top"><span>Penjualan hari ini</span><span class="stat-icon blue"><i class="bi bi-wallet2"></i></span></div><div class="stat-value">Rp {{ number_format($stats['sales_today'] ?? 0, 0, ',', '.') }}</div><div class="stat-foot">{{ number_format($stats['transactions_today'] ?? 0) }} transaksi tercatat hari ini</div></div>
  <div class="stat-card"><div class="stat-top"><span>Total transaksi</span><span class="stat-icon green"><i class="bi bi-receipt-cutoff"></i></span></div><div class="stat-value">{{ number_format($stats['transactions_total'] ?? 0) }}</div><div class="stat-foot">Seluruh transaksi tersimpan</div></div>
  <div class="stat-card"><div class="stat-top"><span>Produk terdaftar</span><span class="stat-icon blue"><i class="bi bi-box-seam"></i></span></div><div class="stat-value">{{ number_format($stats['products_total'] ?? 0) }}</div><div class="stat-foot">Dalam {{ number_format($stats['categories_total'] ?? 0) }} kategori</div></div>
  <div class="stat-card"><div class="stat-top"><span>Stok perlu perhatian</span><span class="stat-icon orange"><i class="bi bi-exclamation-triangle"></i></span></div><div class="stat-value">{{ number_format($stats['low_stock'] ?? 0) }}</div><div class="stat-foot">Produk habis atau menipis</div></div>
</div>
<div class="dashboard-grid">
  <section class="card-panel"><div class="card-panel-header"><div><h3>Ringkasan Penjualan</h3><p>Performa penjualan selama 7 hari terakhir</p></div><span class="badge-soft primary"><i class="bi bi-activity"></i> Mingguan</span></div><div class="card-panel-body"><div class="chart-wrap"><canvas id="salesChart" data-labels='@json($salesChartLabels ?? [])' data-values='@json($salesChartValues ?? [])' aria-label="Grafik penjualan tujuh hari terakhir"></canvas></div></div></section>
  <section class="card-panel"><div class="card-panel-header"><div><h3>Akses Cepat</h3><p>Menu yang sering digunakan</p></div></div><div class="card-panel-body"><div class="quick-actions">
    <a href="{{ url('/kasir') }}" class="quick-action"><span class="qa-icon"><i class="bi bi-cart-plus"></i></span><span>Buka kasir<small>Buat transaksi baru</small></span></a>
    <a href="{{ url('/produk') }}" class="quick-action"><span class="qa-icon"><i class="bi bi-box-seam"></i></span><span>Tambah produk<small>Kelola inventaris</small></span></a>
    <a href="{{ url('/stok') }}" class="quick-action"><span class="qa-icon"><i class="bi bi-arrow-down-up"></i></span><span>Perbarui stok<small>Barang masuk/keluar</small></span></a>
    <a href="{{ url('/transaksi') }}" class="quick-action"><span class="qa-icon"><i class="bi bi-journal-text"></i></span><span>Riwayat transaksi<small>Lihat invoice penjualan</small></span></a>
  </div></div></section>
</div>
<div class="dashboard-grid">
  <section class="card-panel"><div class="card-panel-header"><div><h3>Transaksi Terbaru</h3><p>Aktivitas penjualan terakhir</p></div><a class="btn btn-light btn-sm" href="{{ url('/transaksi') }}">Lihat semua <i class="bi bi-arrow-right ms-1"></i></a></div><div class="table-responsive"><table class="table"><thead><tr><th>No. invoice</th><th>Kasir</th><th class="text-end">Total</th><th>Status</th></tr></thead><tbody>
  @forelse($recentTransactions as $transaction)
  <tr><td><div class="table-title">{{ $transaction->invoice_number }}</div><div class="table-sub">{{ optional($transaction->created_at)->format('d M Y H:i') }}</div></td><td>{{ $transaction->cashier->name ?? 'Kasir' }}</td><td class="text-end"><span class="table-title">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></td><td><span class="badge-soft success">Selesai</span></td></tr>
  @empty <tr><td colspan="4"><div class="empty-state py-3"><i class="bi bi-inbox"></i><strong>Belum ada transaksi</strong><span>Transaksi selesai akan muncul di sini.</span></div></td></tr> @endforelse
  </tbody></table></div></section>
  <section class="card-panel"><div class="card-panel-header"><div><h3>Perlu Restock</h3><p>Produk pada batas stok minimum</p></div><a class="btn btn-light btn-sm" href="{{ url('/stok') }}">Kelola stok</a></div><div class="table-responsive"><table class="table"><tbody>
  @forelse($lowStockProducts as $product)
  <tr><td><div class="table-title">{{ $product->name }}</div><div class="table-sub">{{ $product->code }}</div></td><td class="text-end"><span class="badge-soft warning">{{ $product->stock }} tersisa</span></td></tr>
  @empty <tr><td><div class="empty-state py-3"><strong>Stok aman</strong><span>Belum ada produk yang perlu di-restock.</span></div></td></tr> @endforelse
  </tbody></table></div></section>
</div>
@endsection
