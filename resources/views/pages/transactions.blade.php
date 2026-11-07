@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('eyebrow', 'AKTIVITAS TOKO')
@section('page-title', 'Riwayat Transaksi')
@section('content')
@php($transactions = $transactions ?? collect())
<div class="section-heading"><div><h2>Riwayat Transaksi</h2><p>Lihat transaksi yang sudah selesai dan cetak ulang invoice.</p></div><a class="btn btn-primary" href="{{ url('/kasir') }}"><i class="bi bi-plus-lg me-2"></i>Transaksi baru</a></div>
<div class="stats-grid">
<div class="stat-card"><div class="stat-top">Total transaksi<span class="stat-icon blue"><i class="bi bi-receipt"></i></span></div><div class="stat-value">{{ number_format($stats['transactions_total'] ?? $transactions->count()) }}</div><div class="stat-foot">Semua transaksi tersimpan</div></div>
<div class="stat-card"><div class="stat-top">Penjualan hari ini<span class="stat-icon green"><i class="bi bi-cash-stack"></i></span></div><div class="stat-value">Rp {{ number_format($stats['sales_today'] ?? 0, 0, ',', '.') }}</div><div class="stat-foot">{{ $stats['transactions_today'] ?? 0 }} transaksi hari ini</div></div>
<div class="stat-card"><div class="stat-top">Rata-rata transaksi<span class="stat-icon blue"><i class="bi bi-graph-up"></i></span></div><div class="stat-value">Rp {{ number_format($stats['average_transaction'] ?? 0, 0, ',', '.') }}</div><div class="stat-foot">Nilai rata-rata per invoice</div></div>
<div class="stat-card"><div class="stat-top">Item terjual hari ini<span class="stat-icon orange"><i class="bi bi-basket"></i></span></div><div class="stat-value">{{ number_format($stats['items_today'] ?? 0) }}</div><div class="stat-foot">Total unit pada transaksi hari ini</div></div>
</div>
<form class="toolbar" method="GET" action="{{ url('/transaksi') }}"><div class="search-box"><i class="bi bi-search"></i><input name="q" class="form-control" placeholder="Cari nomor invoice atau nama kasir..." value="{{ request('q') }}"></div><select name="period" class="form-select" style="max-width:190px"><option value="all">Semua tanggal</option><option value="today" @selected(request('period') === 'today')>Hari ini</option><option value="week" @selected(request('period') === 'week')>7 hari terakhir</option></select><button class="btn btn-primary">Filter</button></form>
<section class="card-panel"><div class="table-responsive"><table class="table"><thead><tr><th>No. invoice / Tanggal</th><th>Kasir</th><th>Jumlah item</th><th>Total transaksi</th><th>Status</th><th class="text-end">Aksi</th></tr></thead><tbody>
@forelse($transactions as $transaction)
<tr><td><div class="table-title">{{ $transaction->invoice_number }}</div><div class="table-sub">{{ optional($transaction->created_at)->format('d M Y H:i') }}</div></td><td>{{ $transaction->cashier->name ?? 'Kasir' }}</td><td><span class="badge-soft neutral">{{ $transaction->items->sum('quantity') }} item</span></td><td><span class="table-title">Rp {{ number_format($transaction->total, 0, ',', '.') }}</span></td><td><span class="badge-soft success">{{ $transaction->status ?? 'Selesai' }}</span></td><td class="text-end action-cell"><a class="btn btn-light btn-icon" href="{{ url('/transaksi/'.$transaction->id) }}" title="Detail"><i class="bi bi-eye"></i></a><a class="btn btn-light btn-icon" href="{{ url('/transaksi/'.$transaction->id.'/invoice') }}" title="Cetak invoice"><i class="bi bi-printer"></i></a></td></tr>
@empty<tr><td colspan="6"><div class="empty-state"><i class="bi bi-inbox"></i><strong>Belum ada transaksi</strong><span>Transaksi selesai akan muncul di sini.</span></div></td></tr>@endforelse
</tbody></table></div><div class="pagination-info">{{ $transactions->count() }} transaksi ditampilkan</div></section>
@endsection
