<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'period' => ['nullable', 'string', 'in:all,today,week'],
        ]);

        $period = $validated['period'] ?? 'all';
        $search = $validated['q'] ?? null;

        $transactionsQuery = Transaction::query()
            ->with(['cashier', 'items'])
            ->when($search, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('invoice_number', 'like', "%{$search}%")
                        ->orWhereHas('cashier', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($period === 'today', function ($query): void {
                $query->whereDate('created_at', today());
            })
            ->when($period === 'week', function ($query): void {
                $query->whereBetween('created_at', [
                    now()->subDays(6)->startOfDay(),
                    now()->endOfDay(),
                ]);
            });

        $transactions = $transactionsQuery
            ->latest()
            ->get();

        $todayTransactions = Transaction::query()
            ->whereDate('created_at', today());

        $stats = [
            'transactions_total' => Transaction::query()->count(),
            'sales_today' => (float) $todayTransactions->sum('total_amount'),
            'transactions_today' => (clone $todayTransactions)->count(),
            'average_transaction' => (float) Transaction::query()->average('total_amount'),
            'items_today' => (int) TransactionDetail::query()
                ->whereHas('transaction', function ($query): void {
                    $query->whereDate('created_at', today());
                })
                ->sum('quantity'),
        ];

        return view('pages.transactions', compact('transactions', 'stats'));
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
    public function show(Transaction $transaction): View
    {
        $transaction->load(['cashier', 'items.product']);

        return view('pages.transaction-detail', compact('transaction'));
    }

    public function generateInvoice(Transaction $transaction): View
    {
        $transaction->load(['cashier', 'items.product']);

        return view('pages.receipt', compact('transaction'));
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
