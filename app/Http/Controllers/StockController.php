<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        $stockLogs = StockLog::with('product', 'user')->get();
        $stats = [
            'products_total' => $products->count(),
            'stock_units' => $products->sum('stock'),
            'low_stock' => $products->where('stock', '<=', function ($query) {
                $query->selectRaw('MIN(min_stock)')->from('products');
            })->count(),
            'out_of_stock' => $products->where('stock', 0)->count(),
        ];

        return view('pages.stock', compact('products', 'stockLogs', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'action' => ['required', 'string', 'in:in,out,set'],
            'quantity' => ['required', 'integer', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if (! auth()->check()) {
            return redirect()->route('stok')
                ->withErrors(['stock' => 'Anda harus login untuk memperbarui stok.']);
        }

        try {
            DB::transaction(function () use ($validatedData): void {
                $product = Product::query()
                    ->whereKey($validatedData['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $quantity = $validatedData['quantity'];
                $oldStock = $product->stock;

                [$newStock, $type, $delta] = match ($validatedData['action']) {
                    'in' => [$oldStock + $quantity, 'in', $quantity],
                    'out' => [$oldStock - $quantity, 'out', -$quantity],
                    'set' => [$quantity, 'adjustment', $quantity - $oldStock],
                };

                if ($newStock < 0) {
                    throw new \InvalidArgumentException('Stok keluar melebihi stok yang tersedia.');
                }

                $product->update(['stock' => $newStock]);

                StockLog::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => $type,
                    'quantity' => $delta,
                    'note' => $validatedData['note'] ?? null,
                ]);
            });
        } catch (\InvalidArgumentException $exception) {
            return redirect()->route('stok')
                ->withErrors(['quantity' => $exception->getMessage()])
                ->withInput();
        }

        return redirect()->route('stok')->with('success', 'Stok berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
