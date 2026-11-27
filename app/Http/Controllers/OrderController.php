<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $search = $request->query('search');
        $status = $request->query('status');

        $orders = Order::with(['customer', 'latestInvoice'])
            ->when($search, function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', fn($c) => $c->where('customer_name', 'like', "%{$search}%"));
            })
            ->when($status, fn($q) => $q->where('order_status', $status))
            ->latest('order_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders', 'search', 'status'));
    }

    public function create(): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,id'],
            'order_date'          => ['required', 'date'],
            'due_date'            => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes'               => ['nullable', 'string'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['nullable', 'exists:products,id'],
            'items.*.product_name'=> ['required_without:items.*.product_id', 'nullable', 'string', 'max:255'],
            'items.*.unit'        => ['nullable', 'string', 'max:50'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.item_notes'  => ['nullable', 'string'],
        ], [
            'customer_id.required'       => 'Pelanggan wajib dipilih.',
            'items.required'             => 'Minimal harus ada 1 item pesanan.',
            'items.*.quantity.required'  => 'Kuantitas item wajib diisi.',
            'items.*.quantity.min'       => 'Kuantitas minimal lebih dari 0.',
            'items.*.unit_price.required'=> 'Harga satuan item wajib diisi.',
        ]);

        $order = $this->orderService->createOrderWithInvoice($validated);
        $this->logActivity('orders', "Membuat pesanan {$order->order_number} beserta invoice {$order->latestInvoice?->invoice_number}.");

        return redirect()->route('orders.show', $order)->with('success', 'Pesanan dan Invoice otomatis berhasil dibuat.');
    }

    public function show(Order $order): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $order->load(['customer', 'creator', 'items', 'latestInvoice.payments', 'productionLogs.updater', 'delivery.logs']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        if ($order->order_status === 'cancelled') {
            return redirect()->route('orders.show', $order)->with('error', 'Pesanan yang dibatalkan tidak dapat diubah.');
        }

        $order->load(['items', 'latestInvoice']);
        $customers = Customer::orderBy('customer_name')->get();
        $products = Product::where('is_active', true)->orderBy('product_name')->get();

        return view('orders.edit', compact('order', 'customers', 'products'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'customer_id'         => ['required', 'exists:customers,id'],
            'order_date'          => ['required', 'date'],
            'due_date'            => ['nullable', 'date', 'after_or_equal:order_date'],
            'notes'               => ['nullable', 'string'],
            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['nullable', 'exists:products,id'],
            'items.*.product_name'=> ['required_without:items.*.product_id', 'nullable', 'string', 'max:255'],
            'items.*.unit'        => ['nullable', 'string', 'max:50'],
            'items.*.quantity'    => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price'  => ['required', 'numeric', 'min:0'],
            'items.*.item_notes'  => ['nullable', 'string'],
        ]);

        $this->orderService->updateOrder($order, $validated);
        $this->logActivity('orders', "Memperbarui pesanan {$order->order_number}.");

        return redirect()->route('orders.show', $order)->with('success', 'Data pesanan dan tagihan berhasil diperbarui.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        if ($order->latestInvoice && $order->latestInvoice->paid_amount > 0) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah memiliki pembayaran masuk. Batalkan pembayaran terlebih dahulu.');
        }

        $this->orderService->cancelOrder($order);
        $this->logActivity('orders', "Membatalkan pesanan {$order->order_number}.");

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }
}