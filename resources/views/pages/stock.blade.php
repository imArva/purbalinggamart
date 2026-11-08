@extends('layouts.app')
@section('title', 'Stok Barang')
@section('eyebrow', 'MANAJEMEN INVENTARIS')
@section('page-title', 'Stok Barang')
@section('content')
@php
    $products = ($products ?? collect())->sortBy('stock');
    $stockLogs = $stockLogs ?? collect();
@endphp

<div class="section-heading">
    <div>
        <h2>Pemantauan Stok</h2>
        <p>Pantau ketersediaan barang dan catat penyesuaian inventaris.</p>
    </div>
    <span class="badge-soft warning">
        <i class="bi bi-exclamation-circle"></i>
        {{ $stats['low_stock'] ?? 0 }} perlu perhatian
    </span>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-top">
            Total jenis barang
            <span class="stat-icon blue"><i class="bi bi-boxes"></i></span>
        </div>
        <div class="stat-value">{{ $stats['products_total'] ?? $products->count() }}</div>
        <div class="stat-foot">Produk terdaftar</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            Unit stok tersedia
            <span class="stat-icon green"><i class="bi bi-check2-circle"></i></span>
        </div>
        <div class="stat-value">{{ number_format($stats['stock_units'] ?? $products->sum('stock')) }}</div>
        <div class="stat-foot">Total unit seluruh produk</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            Stok menipis
            <span class="stat-icon orange"><i class="bi bi-exclamation-triangle"></i></span>
        </div>
        <div class="stat-value">{{ $stats['low_stock'] ?? 0 }}</div>
        <div class="stat-foot">Di bawah batas minimum</div>
    </div>

    <div class="stat-card">
        <div class="stat-top">
            Stok habis
            <span class="stat-icon blue"><i class="bi bi-box2"></i></span>
        </div>
        <div class="stat-value">{{ $stats['out_of_stock'] ?? 0 }}</div>
        <div class="stat-foot">Perlu restock segera</div>
    </div>
</div>

<section class="card-panel mb-4">
    <div class="card-panel-header">
        <div>
            <h3>Daftar Stok Barang</h3>
            <p>Diurutkan dari jumlah stok paling sedikit</p>
        </div>
        <span class="badge-soft neutral">{{ $products->count() }} produk</span>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Stok saat ini</th>
                    <th>Min. stok</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <img class="table-avatar product-image" src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                <div>
                                    <div class="table-title">{{ $product->name }}</div>
                                    <div class="table-sub">{{ $product->product_code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->category_name ?? '-' }}</td>
                        <td>
                            <div class="stock-level">
                                <strong>{{ $product->stock }}</strong>
                                <div class="stock-bar">
                                    <span
                                        class="{{ $product->stock <= 0 ? 'empty' : ($product->stock <= $product->min_stock ? 'low' : '') }}"
                                        style="width:{{ min(100, ($product->stock / max(($product->min_stock ?? 1) * 3, 1)) * 100) }}%"
                                    ></span>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->min_stock }}</td>
                        <td>
                            @if($product->stock <= 0)
                                <span class="badge-soft danger">Habis</span>
                            @elseif($product->stock <= $product->min_stock)
                                <span class="badge-soft warning">Menipis</span>
                            @else
                                <span class="badge-soft success">Aman</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <button
                                class="btn btn-outline-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#stockModal"
                                data-product='@json($product)'
                            >
                                <i class="bi bi-arrow-down-up me-1"></i>
                                Perbarui
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada data stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card-panel">
    <div class="card-panel-header">
        <div>
            <h3>Aktivitas Perubahan Stok</h3>
            <p>Catatan penyesuaian stok terakhir</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk / Waktu</th>
                    <th>Perubahan</th>
                    <th>Catatan</th>
                    <th>Pengguna</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockLogs as $log)
                    <tr>
                        <td>
                            <div class="table-title">{{ $log->product->name ?? '-' }}</div>
                            <div class="table-sub">{{ optional($log->created_at)->format('d M Y H:i') }}</div>
                        </td>
                        <td>
                            <span class="badge-soft {{ $log->delta >= 0 ? 'success' : 'warning' }}">
                                {{ $log->delta > 0 ? '+' : '' }}{{ $log->delta }}
                            </span>
                        </td>
                        <td>{{ $log->note ?: 'Penyesuaian stok' }}</td>
                        <td>{{ $log->user->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Belum ada riwayat stok.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@include('partials.stock-modal')
@endsection
