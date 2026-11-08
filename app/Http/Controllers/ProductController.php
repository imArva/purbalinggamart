<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $products = Product::query()
            ->with('category')
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
        $categories = Category::query()->orderBy('category_name')->get();

        return view('pages.products', compact('products', 'categories'));
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
        $validatedData = $request->validate([
            'product_code' => 'required|string|max:255|unique:products,product_code',
            'name' => 'required|string|max:255',
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price' => 'required|numeric|min:1',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $validatedData['image_path'] = $request->file('image')->store('products', 'public');
        unset($validatedData['image']);

        $product = Product::create($validatedData);

        return redirect()->route('produk')->with('success', 'Product created successfully.');
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
        $validatedData = $request->validate([
            'product_code' => 'required|string|max:255|unique:products,product_code,'.$id,
            'name' => 'required|string|max:255',
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'price' => 'required|numeric|min:1',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product = Product::findOrFail($id);
        unset($validatedData['image']);

        if ($request->hasFile('image')) {
            $newImagePath = $request->file('image')->store('products', 'public');

            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $validatedData['image_path'] = $newImagePath;
        }

        $product->update($validatedData);

        return redirect()->route('produk')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('produk')->with('success', 'Product deleted successfully.');
    }
}
