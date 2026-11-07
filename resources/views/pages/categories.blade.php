@extends('layouts.app') @section('title', 'Kategori') @section('eyebrow',
'MANAJEMEN INVENTARIS') @section('page-title', 'Kategori') @section('content')
@php($categories = $categories ?? collect())
<div class="section-heading">
   <div>
      <h2>Kategori Produk</h2>
      <p>Kelompokkan produk agar lebih mudah ditemukan saat transaksi.</p>
   </div>
   <button
      class="btn btn-primary"
      data-bs-toggle="modal"
      data-bs-target="#categoryModal"
   >
      <i class="bi bi-plus-lg me-2"></i>Tambah kategori
   </button>
</div>
<div class="row g-3 mb-4">
   @forelse($categories as $category)
   <div class="col-md-6 col-xl-4">
      <div class="category-card">
         <div class="category-icon"><i class="bi bi-tags"></i></div>
         <div class="min-w-0">
            <h3>{{ $category->category_name }}</h3>
            <p>
               {{ $category->products_count ?? $category->products->count() ?? 0
               }} produk · {{ $category->description ?: 'Tanpa deskripsi' }}
            </p>
         </div>
         <div class="category-actions">
            <button
               class="btn btn-light btn-icon"
               data-bs-toggle="modal"
               data-bs-target="#categoryModal"
               data-category='@json($category)'
            >
               <i class="bi bi-pencil"></i>
            </button>
            <form
               method="POST"
               action="{{ url('/kategori/'.$category->id) }}"
               onsubmit="return confirm('Hapus kategori ini?')"
            >
               @csrf @method('DELETE')<button
                  class="btn btn-light btn-icon text-danger"
               >
                  <i class="bi bi-trash3"></i>
               </button>
            </form>
         </div>
      </div>
   </div>
   @empty
   <div class="col-12">
      <section class="card-panel">
         <div class="empty-state">
            <i class="bi bi-inbox"></i><strong>Belum ada kategori</strong
            ><span>Tambahkan kategori untuk mengelompokkan produk.</span>
         </div>
      </section>
   </div>
   @endforelse
</div>
<section class="card-panel">
   <div class="card-panel-header">
      <div>
         <h3>Ringkasan Kategori</h3>
         <p>Jumlah produk dalam setiap kategori</p>
      </div>
   </div>
   <div class="table-responsive">
      <table class="table">
         <thead>
            <tr>
               <th>Kategori</th>
               <th>Deskripsi</th>
               <th>Jumlah produk</th>
            </tr>
         </thead>
         <tbody>
            @forelse($categories as $category)
            <tr>
               <td>
                  <span class="table-title"
                     >{{ $category->category_name }}</span
                  >
               </td>
               <td>{{ $category->description ?: '-' }}</td>
               <td>
                  <span class="badge-soft neutral"
                     >{{ $category->products_count ?? 0 }} produk</span
                  >
               </td>
            </tr>
            @empty
            <tr>
               <td colspan="3">Belum ada kategori.</td>
            </tr>
            @endforelse
         </tbody>
      </table>
   </div>
</section>
@include('partials.category-modal') @endsection
