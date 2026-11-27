<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $search = $request->query('search');
        $customers = Customer::withCount('orders')
            ->when($search, function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
            'address'       => ['nullable', 'string'],
        ], [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'phone.required'         => 'Nomor telepon/WhatsApp wajib diisi.',
            'email.email'            => 'Format email tidak valid.',
        ]);

        $customer = Customer::create($validated);
        $this->logActivity('customers', "Menambahkan pelanggan baru: {$customer->customer_name}");

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil ditambahkan.');
    }

    public function show(Customer $customer): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $customer->load(['orders' => function ($q) {
            $q->with('latestInvoice')->latest('order_date');
        }]);

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'phone'         => ['required', 'string', 'max:30'],
            'email'         => ['nullable', 'email', 'max:255'],
            'address'       => ['nullable', 'string'],
        ]);

        $customer->update($validated);
        $this->logActivity('customers', "Memperbarui data pelanggan: {$customer->customer_name}");

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        if ($customer->orders()->exists()) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena sudah memiliki riwayat pesanan.');
        }

        $name = $customer->customer_name;
        $customer->delete();
        $this->logActivity('customers', "Menghapus pelanggan: {$name}");

        return redirect()->route('customers.index')->with('success', 'Data pelanggan berhasil dihapus.');
    }
}