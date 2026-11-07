@extends('layouts.app')
@section('title', 'Produk')
@section('eyebrow', 'MANAJEMEN INVENTARIS')
@section('page-title', 'Produk')
@section('content')
@php($products = $products ?? collect())
<div class="section-heading">
    <div>
        <h2>Daftar Produk</h2>
        <p>Kelola barang yang tersedia untuk penjualan.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
        <i class="bi bi-plus-lg me-2"></i>
        Tambah produk
    </button>
</div>
<form class="toolbar" method="GET" action="{{ url('/produk') }}">
    <div class="search-box">
        <i class="bi bi-search"></i>
        <input name="q" class="form-control" placeholder="Cari nama atau kode produk..." value="{{ request('q') }}"></div>
        <select name="category" class="form-select" style="max-width:210px">
            <option value="">Semua kategori</option>
            @foreach(($categories ?? collect()) as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                    {{ $category->category_name }}
                </option>
            @endforeach
        </select>
        <button class="btn btn-primary">Filter</button>
</form>
<section class="card-panel">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga jual</th>
                    <th>Stok</th>
                    <th>Status stok</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <div class="product-cell">
                                <div class="table-avatar tone-blue">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div>
                                    <div class="table-title">{{ $product->name }}</div>
                                    <div class="table-sub">{{ $product->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ $product->category->category_name ?? '-' }}</td>
                        <td><span class="table-title">Rp {{ number_format($product->price, 0, ',', '.') }}</span></td>
                        <td>{{ $product->stock }}</td>
                        <td>@if($product->stock <= 0)<span class="badge-soft danger">Habis</span>@elseif($product->stock <= $product->min_stock)<span class="badge-soft warning">Menipis</span>@else<span class="badge-soft success">Aman</span>@endif</td>
                        <td class="text-end action-cell">
                            <button class="btn btn-light btn-icon me-1" data-bs-toggle="modal" data-bs-target="#productModal" data-product='@json($product)'>
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form class="d-inline" method="POST" action="{{ url('/produk/'.$product->id) }}" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-light btn-icon text-danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <strong>Belum ada produk</strong>
                                <span>Tambahkan produk untuk mulai menggunakan inventaris.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination-info">{{ $products->count() }} produk ditampilkan</div>
</section>
@include('partials.product-modal')
@endsection
