<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(): View
    {
        $suppliers = Supplier::withCount('products')
            ->orderBy('name')
            ->get();

        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', "Supplier '{$validated['name']}' berhasil ditambahkan.");
    }

    /**
     * Show the form for editing the supplier.
     */
    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama supplier wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', "Data supplier '{$supplier->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified supplier from storage.
     */
    public function destroy(Supplier $supplier): RedirectResponse
    {
        $name = $supplier->name;
        $supplier->products()->detach();
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', "Supplier '{$name}' berhasil dihapus.");
    }
}
