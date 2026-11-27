<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentService $paymentService) {}

    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $search = $request->query('search');
        $payments = Payment::with(['invoice.order.customer', 'creator'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('invoice', fn($i) => $i->where('invoice_number', 'like', "%{$search}%"))
                  ->orWhereHas('invoice.order.customer', fn($c) => $c->where('customer_name', 'like', "%{$search}%"));
            })
            ->latest('payment_date')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        return view('payments.index', compact('payments', 'search'));
    }

    public function create(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $invoices = Invoice::with('order.customer')
            ->whereIn('payment_status', ['unpaid', 'dp', 'partial'])
            ->latest()
            ->get();

        $selectedInvoice = $request->query('invoice_id')
            ? Invoice::with('order.customer')->find($request->query('invoice_id'))
            : null;

        return view('payments.create', compact('invoices', 'selectedInvoice'));
    }

    public function store(Request $request): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $validated = $request->validate([
            'invoice_id'     => ['required', 'exists:invoices,id'],
            'payment_date'   => ['required', 'date'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'payment_method' => ['required', 'string', 'max:50'],
            'notes'          => ['nullable', 'string'],
        ], [
            'invoice_id.required'     => 'Pilih nomor invoice terlebih dahulu.',
            'amount.required'         => 'Nominal pembayaran wajib diisi.',
            'amount.min'              => 'Nominal pembayaran harus lebih dari Rp 0.',
            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $payment = $this->paymentService->recordPayment($invoice, $validated);

        $this->logActivity('payments', "Mencatat pembayaran Rp " . number_format($payment->amount, 0, ',', '.') . " untuk {$invoice->invoice_number}.");

        return redirect()->route('invoices.show', $invoice)->with('success', 'Pembayaran berhasil dicatat dan status tagihan diperbarui.');
    }

    public function cancel(Payment $payment): RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        if ($payment->status === 'cancelled') {
            return back()->with('error', 'Pembayaran ini sudah dibatalkan sebelumnya.');
        }

        $this->paymentService->cancelPayment($payment);
        $this->logActivity('payments', "Membatalkan pembayaran ID #{$payment->id} pada invoice {$payment->invoice->invoice_number}.");

        return back()->with('success', 'Pembayaran berhasil dibatalkan dan sisa tagihan telah dihitung ulang.');
    }
}