<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $search = $request->query('search');
        $status = $request->query('status');

        $invoices = Invoice::with(['order.customer'])
            ->when($search, function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('order.customer', fn($c) => $c->where('customer_name', 'like', "%{$search}%"));
            })
            ->when($status, fn($q) => $q->where('payment_status', $status))
            ->latest('invoice_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search', 'status'));
    }

    public function show(Invoice $invoice): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $invoice->load(['order.customer', 'order.items', 'payments.creator']);
        $company = CompanySetting::first();

        return view('invoices.show', compact('invoice', 'company'));
    }

    public function print(Invoice $invoice): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $invoice->load(['order.customer', 'order.items', 'payments']);
        $company = CompanySetting::first();

        return view('invoices.pdf', [
            'invoice' => $invoice,
            'company' => $company,
            'isPrint' => true,
        ]);
    }

    public function downloadPdf(Invoice $invoice): Response|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) return $redirect;

        $invoice->load(['order.customer', 'order.items', 'payments']);
        $company = CompanySetting::first();

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'company' => $company,
            'isPrint' => false,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($invoice->invoice_number . '.pdf');
    }
}