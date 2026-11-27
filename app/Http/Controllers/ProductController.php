<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $search = $request->query('search');
        $products = Product::when($search, function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('unit', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['required', 'string', 'max:50'],
            'price'        => ['required', 'numeric', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ], [
            'product_name.required' => 'Nama produk/jasa wajib diisi.',
            'unit.required'         => 'Satuan produk wajib diisi (contoh: pcs, meter, rim).',
            'price.required'        => 'Harga satuan wajib diisi.',
            'price.min'             => 'Harga tidak boleh negatif.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $product = Product::create($validated);
        $this->logActivity('products', "Menambahkan produk baru: {$product->product_name}");

        return redirect()->route('products.index')->with('success', 'Produk/jasa berhasil ditambahkan.');
    }

    public function show(Product $product): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'unit'         => ['required', 'string', 'max:50'],
            'price'        => ['required', 'numeric', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $product->update($validated);
        $this->logActivity('products', "Memperbarui produk: {$product->product_name}");

        return redirect()->route('products.index')->with('success', 'Data produk/jasa berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $name = $product->product_name;
        $product->delete();
        $this->logActivity('products', "Menghapus produk: {$name}");

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus (riwayat pesanan lama tetap aman).');
    }
}