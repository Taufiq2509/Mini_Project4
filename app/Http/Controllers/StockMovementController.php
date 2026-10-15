<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    /**
     * Display stock movement transactions and record form.
     */
    public function index(): View
    {
        $products = Product::orderBy('name')->get();
        $movements = StockMovement::with('product.category')
            ->latest()
            ->paginate(15);

        return view('stock.index', compact('products', 'movements'));
    }

    /**
     * Store a stock transaction (Stock In / Stock Out).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'type' => ['required', 'in:in,out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'product_id.required' => 'Pilih produk yang akan ditransaksikan.',
            'product_id.exists' => 'Produk tidak ditemukan dalam sistem.',
            'type.required' => 'Pilih jenis transaksi persediaan.',
            'type.in' => 'Jenis transaksi harus berupa Barang Masuk atau Barang Keluar.',
            'quantity.required' => 'Jumlah barang wajib diisi.',
            'quantity.min' => 'Jumlah barang minimal 1 unit.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $product = Product::whereKey($validated['product_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['type'] === 'out' && $product->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Stok tidak mencukupi untuk pengeluaran barang. Stok saat ini: {$product->stock} unit.",
                ]);
            }

            if ($validated['type'] === 'in') {
                $product->stock += $validated['quantity'];
            } else {
                $product->stock -= $validated['quantity'];
            }

            $product->save();

            StockMovement::create([
                'product_id' => $product->id,
                'type' => $validated['type'],
                'quantity' => $validated['quantity'],
                'note' => $validated['note'] ?? ($validated['type'] === 'in' ? 'Penerimaan barang masuk' : 'Pengeluaran barang keluar'),
                'user_name' => $request->session()->get('demo_user', 'petugas'),
            ]);
        });

        $typeLabel = $validated['type'] === 'in' ? 'Barang Masuk' : 'Barang Keluar';

        return redirect()->route('stock.index')->with('success', "Transaksi {$typeLabel} sebanyak {$validated['quantity']} unit berhasil dicatat.");
    }
}
