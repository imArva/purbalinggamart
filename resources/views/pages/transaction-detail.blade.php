@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('eyebrow', 'AKTIVITAS TOKO')
@section('page-title', 'Detail Transaksi')
@section('content')
<div class="section-heading">
    <div>
        <h2>{{ $transaction->invoice_number }}</h2>
        <p>{{ $transaction->created_at?->format('d M Y H:i') }} · Kasir {{ $transaction->cashier->name ?? 'Kasir' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-light" href="{{ route('transaksi') }}">Kembali</a>
        <a class="btn btn-primary" href="{{ route('transaksi.invoice', $transaction) }}" target="_blank">
            <i class="bi bi-printer me-1"></i>Cetak invoice
        </a>
    </div>
</div>

<section class="card-panel">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Jumlah</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->items as $item)
                    <tr>
                        <td>
                            <div class="table-title">{{ $item->product->name }}</div>
                            <div class="table-sub">{{ $item->product->product_code }}</div>
                        </td>
                        <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Total</th>
                    <th class="text-end">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</th>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Pembayaran</td>
                    <td class="text-end">Rp {{ number_format($transaction->payment, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-end">Kembalian</td>
                    <td class="text-end">Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection
