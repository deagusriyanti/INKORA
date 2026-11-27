<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if ($redirect = $this->requireAuth()) {
            return $redirect;
        }

        $totalTagihan    = (float) Invoice::where('payment_status', '!=', 'cancelled')->sum('total_amount');
        $totalPembayaran = (float) Payment::where('status', 'valid')->sum('amount');
        $sisaBelumLunas  = max(0, $totalTagihan - $totalPembayaran);
        $rasioPelunasan  = $totalTagihan > 0 ? round(($totalPembayaran / $totalTagihan) * 100) : 0;

        $stats = [
            'total_orders'           => Order::where('order_status', '!=', 'cancelled')->count(),
            'total_billed'           => $totalTagihan,
            'total_paid'             => $totalPembayaran,
            'collection_rate'        => $rasioPelunasan,
            'unpaid_invoices_count'  => Invoice::whereIn('payment_status', ['unpaid', 'dp', 'partial'])->count(),
            'unpaid_invoices_amount' => $sisaBelumLunas,
            'queued_orders'          => Order::whereIn('order_status', ['received', 'queued'])->count(),
            'in_progress_orders'     => Order::whereIn('order_status', ['received', 'queued', 'in_production'])->count(),
            'total_customers'        => Customer::count(),
        ];

        $chartLabels  = [];
        $chartOrders  = [];
        $chartRevenue = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[]  = $date->translatedFormat('D');
            $chartOrders[]  = round((float) Invoice::where('payment_status', '!=', 'cancelled')->whereDate('invoice_date', $date)->sum('total_amount') / 1000, 1);
            $chartRevenue[] = round((float) Payment::where('status', 'valid')->whereDate('payment_date', $date)->sum('amount') / 1000, 1);
        }

        $recentOrders = Order::with(['customer', 'latestInvoice'])
            ->where('order_status', '!=', 'cancelled')
            ->latest('order_date')
            ->latest('id')
            ->take(5)
            ->get();

        $topCustomers = Customer::withCount('orders')
            ->orderByDesc('orders_count')
            ->take(4)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'chartLabels',
            'chartOrders',
            'chartRevenue',
            'recentOrders',
            'topCustomers'
        ));
    }
}