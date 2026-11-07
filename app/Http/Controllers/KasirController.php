<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KasirController extends Controller
{
    public function home(): View
    {
        return view('pages.home');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View|JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $products = Product::query()
            ->when($validated['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('product_code', 'like', "%{$search}%");
                });
            })
            ->when($validated['category'] ?? null, function ($query, int $categoryId): void {
                $query->where('category_id', $categoryId);
            })
            ->orderBy('name')
            ->get();

        $categories = Category::query()
            ->orderBy('category_name')
            ->get();

        $cartItems = $this->cartItems();
        $subtotal = $cartItems->sum(
            fn (object $item): float => (float) $item->price * $item->quantity
        );
        $discount = 0;
        $total = $subtotal - $discount;

        if ($request->expectsJson()) {
            return response()->json($this->posPayload($products, $cartItems, $subtotal, $discount, $total));
        }

        return view('pages.pos', compact(
            'categories',
            'products',
            'cartItems',
            'subtotal',
            'discount',
            'total',
        ));
    }

    public function addToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $cart = session('cart', []);
        $currentQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);

        if ($product->stock < $currentQuantity + 1) {
            return response()->json([
                'message' => 'Jumlah produk melebihi stok yang tersedia.',
            ], 422);
        }

        $cart[$product->id] = [
            'product_id' => $product->id,
            'quantity' => $currentQuantity + 1,
        ];
        session(['cart' => $cart]);

        return $this->cartResponse();
    }

    public function updateCartQuantity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'change' => ['required', 'integer', 'in:-1,1'],
        ]);

        $product = Product::query()->findOrFail($validated['product_id']);
        $cart = session('cart', []);
        $currentQuantity = (int) ($cart[$product->id]['quantity'] ?? 0);
        $newQuantity = $currentQuantity + $validated['change'];

        if ($newQuantity > $product->stock) {
            return response()->json([
                'message' => 'Jumlah produk melebihi stok yang tersedia.',
            ], 422);
        }

        if ($newQuantity <= 0) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = [
                'product_id' => $product->id,
                'quantity' => $newQuantity,
            ];
        }

        session(['cart' => $cart]);

        return $this->cartResponse();
    }

    public function clearCart(): JsonResponse
    {
        session()->forget('cart');

        return $this->cartResponse();
    }

    /**
     * Resolve the current session cart into product-backed view data.
     *
     * @return Collection<int, object>
     */
    private function cartItems(): Collection
    {
        $cart = session('cart', []);

        if (! is_array($cart) || $cart === []) {
            return collect();
        }

        $cartRows = collect($cart)->map(function (mixed $item, mixed $key): array {
            if (is_array($item)) {
                return [
                    'product_id' => (int) ($item['product_id'] ?? $key),
                    'quantity' => max(0, (int) ($item['quantity'] ?? 0)),
                ];
            }

            return [
                'product_id' => (int) $key,
                'quantity' => max(0, (int) $item),
            ];
        })->filter(fn (array $item): bool => $item['product_id'] > 0 && $item['quantity'] > 0);

        if ($cartRows->isEmpty()) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $cartRows->pluck('product_id'))
            ->get()
            ->keyBy('id');

        return $cartRows
            ->map(function (array $item) use ($products): ?object {
                $product = $products->get($item['product_id']);

                if ($product === null) {
                    return null;
                }

                return (object) [
                    'product_id' => $product->id,
                    'product' => $product,
                    'price' => (float) $product->price,
                    'quantity' => $item['quantity'],
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function posPayload(
        Collection $products,
        Collection $cartItems,
        float $subtotal,
        float $discount,
        float $total,
    ): array {
        return [
            'products' => $products->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'code' => $product->code,
                'price' => (float) $product->price,
                'stock' => $product->stock,
                'min_stock' => $product->min_stock,
            ])->values()->all(),
            'cart_items' => $cartItems->map(fn (object $item): array => [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ])->values()->all(),
            'cart_count' => $cartItems->sum('quantity'),
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => $total,
        ];
    }

    private function cartResponse(): JsonResponse
    {
        $cartItems = $this->cartItems();
        $subtotal = $cartItems->sum(
            fn (object $item): float => (float) $item->price * $item->quantity
        );
        $discount = 0;

        return response()->json($this->posPayload(
            collect(),
            $cartItems,
            $subtotal,
            $discount,
            $subtotal - $discount,
        ));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'payment_amount' => ['required', 'numeric', 'min:0'],
        ]);
        
        $userId = auth()->id() ?? User::query()
            ->where('is_active', true)
            ->value('id');

        if ($userId === null) {
            return back()
                ->withErrors(['payment_amount' => 'Belum ada kasir aktif untuk mencatat transaksi.'])
                ->withInput();
        }

        $cartItems = $this->cartItems();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Keranjang masih kosong.']);
        }

        try {
            $transaction = DB::transaction(function () use ($validated, $userId, $cartItems): Transaction {
                $productIds = $cartItems->pluck('product_id');
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $total = 0;

                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);

                    if ($product === null || $product->stock < $cartItem->quantity) {
                        throw ValidationException::withMessages([
                            'cart' => "Stok {$cartItem->product->name} tidak mencukupi.",
                        ]);
                    }

                    $total += (float) $product->price * $cartItem->quantity;
                }

                $payment = (float) $validated['payment_amount'];

                if ($payment < $total) {
                    throw ValidationException::withMessages([
                        'payment_amount' => 'Nominal pembayaran kurang dari total transaksi.',
                    ]);
                }

                $transaction = Transaction::create([
                    'user_id' => $userId,
                    'invoice_number' => Transaction::generateInvoiceNumber(),
                    'total_amount' => $total,
                    'payment' => $payment,
                    'change_amount' => $payment - $total,
                ]);

                foreach ($cartItems as $cartItem) {
                    $product = $products->get($cartItem->product_id);
                    $subtotal = (float) $product->price * $cartItem->quantity;

                    TransactionDetail::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $product->id,
                        'quantity' => $cartItem->quantity,
                        'unit_price' => $product->price,
                        'subtotal' => $subtotal,
                    ]);

                    $product->decrement('stock', $cartItem->quantity);

                    StockLog::create([
                        'product_id' => $product->id,
                        'user_id' => $userId,
                        'type' => 'out',
                        'quantity' => -$cartItem->quantity,
                        'note' => "Penjualan {$transaction->invoice_number}",
                    ]);
                }

                return $transaction;
            });
        } catch (ValidationException $exception) {
            throw $exception;
        }

        session()->forget('cart');

        return redirect()->route('kasir.receipt', $transaction);
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['cashier', 'items.product']);

        return view('pages.receipt', compact('transaction'));
    }

    public function dashboard()
    {
        $today = today();
        $weekStart = now()->subDays(6)->startOfDay();
        $weekEnd = now()->endOfDay();

        $todayTransactions = Transaction::query()
            ->whereDate('created_at', $today);

        $stats = [
            'sales_today' => (float) $todayTransactions->sum('total_amount'),
            'transactions_today' => (clone $todayTransactions)->count(),
            'transactions_total' => Transaction::query()->count(),
            'products_total' => Product::query()->count(),
            'categories_total' => Category::query()->count(),
            'low_stock' => Product::query()
                ->whereColumn('stock', '<=', 'min_stock')
                ->count(),
        ];

        $weeklyTransactions = Transaction::query()
            ->select(['total_amount', 'created_at'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->get()
            ->groupBy(fn (Transaction $transaction): string => $transaction->created_at->toDateString());

        $salesChartLabels = [];
        $salesChartValues = [];

        for ($date = $weekStart->copy()->startOfDay(); $date <= $weekEnd; $date->addDay()) {
            $dateKey = $date->toDateString();
            $salesChartLabels[] = $date->translatedFormat('D');
            $salesChartValues[] = (float) $weeklyTransactions
                ->get($dateKey, collect())
                ->sum('total_amount');
        }

        $recentTransactions = Transaction::query()
            ->with('cashier')
            ->latest()
            ->limit(5)
            ->get();

        $lowStockProducts = Product::query()
            ->whereColumn('stock', '<=', 'min_stock')
            ->orderBy('stock')
            ->orderBy('name')
            ->limit(5)
            ->get();

        return view('pages.dashboard', compact(
            'stats',
            'recentTransactions',
            'lowStockProducts',
            'salesChartLabels',
            'salesChartValues',
        ));
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
