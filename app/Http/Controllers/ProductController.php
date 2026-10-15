<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products with search, filtering, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'suppliers']);

        // Search filter: SKU or Name
        if ($request->filled('search')) {
            $keyword = trim($request->input('search'));
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Stock status filter
        if ($request->filled('stock_status')) {
            if ($request->input('stock_status') === 'low') {
                $query->whereColumn('stock', '<=', 'minimum_stock');
            } elseif ($request->input('stock_status') === 'safe') {
                $query->whereColumn('stock', '>', 'minimum_stock');
            }
        }

        // Sorting
        $allowedSorts = ['name', 'sku', 'price', 'stock', 'created_at'];
        $sort = in_array($request->input('sort'), $allowedSorts, true) ? $request->input('sort') : 'name';
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

        $products = $query->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $product = new Product([
            'stock' => 0,
            'minimum_stock' => 5,
            'price' => 0,
        ]);
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.create', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['integer', 'exists:suppliers,id'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'price.required' => 'Harga produk wajib diisi.',
            'stock.required' => 'Stok awal wajib diisi.',
            'minimum_stock.required' => 'Batas minimum stok wajib diisi.',
        ]);

        $supplierIds = $validated['suppliers'] ?? [];
        unset($validated['suppliers']);

        $product = Product::create($validated);

        if (!empty($supplierIds)) {
            $product->suppliers()->sync($supplierIds);
        }

        // Record initial stock transaction if stock > 0
        if ($product->stock > 0) {
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'in',
                'quantity' => $product->stock,
                'note' => 'Stok awal saat pendaftaran produk baru',
                'user_name' => $request->session()->get('demo_user', 'admin'),
            ]);
        }

        return redirect()->route('products.index')->with('success', "Produk '{$product->name}' berhasil ditambahkan ke inventaris.");
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        $product->load([
            'category',
            'suppliers',
            'movements' => function ($q) {
                $q->latest()->take(20);
            },
        ]);

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load('suppliers');
        $categories = Category::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'suppliers' => ['nullable', 'array'],
            'suppliers.*' => ['integer', 'exists:suppliers,id'],
        ], [
            'sku.required' => 'SKU produk wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan oleh produk lain.',
            'name.required' => 'Nama produk wajib diisi.',
            'category_id.required' => 'Kategori produk wajib dipilih.',
            'price.required' => 'Harga produk wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
            'minimum_stock.required' => 'Batas minimum stok wajib diisi.',
        ]);

        $supplierIds = $validated['suppliers'] ?? [];
        unset($validated['suppliers']);

        $product->update($validated);
        $product->suppliers()->sync($supplierIds);

        return redirect()->route('products.index')->with('success', "Data produk '{$product->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        if ($product->movements()->exists()) {
            return back()->withErrors([
                'delete' => "Produk '{$product->name}' memiliki riwayat transaksi persediaan dan tidak dapat dihapus demi integritas audit.",
            ]);
        }

        $productName = $product->name;
        $product->suppliers()->detach();
        $product->delete();

        return redirect()->route('products.index')->with('success', "Produk '{$productName}' berhasil dihapus.");
    }
}
