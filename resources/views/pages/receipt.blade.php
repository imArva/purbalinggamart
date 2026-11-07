<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaction->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #111; margin: 24px auto; max-width: 420px; }
        h1, p { margin: 0; }
        header { text-align: center; margin-bottom: 20px; }
        .muted { color: #666; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; margin: 16px 0; }
        th, td { padding: 6px 0; text-align: left; vertical-align: top; }
        th:last-child, td:last-child { text-align: right; }
        .total { border-top: 1px dashed #111; font-weight: bold; }
        .actions { display: flex; gap: 8px; justify-content: center; margin-top: 24px; }
        .actions a, .actions button { border: 1px solid #222; background: #fff; color: #111; padding: 8px 14px; text-decoration: none; cursor: pointer; }
        @media print {
            body { margin: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <header>
        <h1>Purbalingga Mart</h1>
        <p class="muted">Struk Pembayaran</p>
        <p class="muted">{{ $transaction->invoice_number }} · {{ $transaction->created_at->format('d/m/Y H:i') }}</p>
    </header>

    <p class="muted">Kasir: {{ $transaction->cashier->name ?? 'Kasir' }}</p>
    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }} x Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total">
                <td colspan="2">Total</td>
                <td>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2">Pembayaran</td>
                <td>Rp {{ number_format($transaction->payment, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2">Kembalian</td>
                <td>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="muted" style="text-align: center;">Terima kasih atas kunjungan Anda.</p>
    <div class="actions">
        <button type="button" onclick="window.print()">Cetak struk</button>
        <a href="{{ route('kasir') }}">Transaksi baru</a>
    </div>
</body>
</html>
