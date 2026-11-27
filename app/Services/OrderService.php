<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductionLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrderWithInvoice(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $orderNumber = $this->generateUniqueNumber('ORD', Order::class, 'order_number');

            $order = Order::create([
                'customer_id'  => $data['customer_id'],
                'order_number' => $orderNumber,
                'order_date'   => $data['order_date'],
                'total_amount' => 0,
                'notes'        => $data['notes'] ?? null,
                'order_status' => 'received',
                'created_by'   => Auth::id(),
            ]);

            $grandTotal = 0;

            foreach ($data['items'] as $item) {
                $product = !empty($item['product_id']) ? Product::find($item['product_id']) : null;
                $productName = $product ? $product->product_name : ($item['product_name'] ?? 'Item Kustom');
                $unit = $product ? $product->unit : ($item['unit'] ?? 'pcs');
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $subtotal = round($qty * $unitPrice, 2);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product?->id,
                    'product_name' => $productName,
                    'unit'         => $unit,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $subtotal,
                    'item_notes'   => $item['item_notes'] ?? null,
                ]);

                $grandTotal += $subtotal;
            }

            $order->update(['total_amount' => $grandTotal]);

            if (FeatureGateService::allows('production_tracking')) {
                ProductionLog::create([
                    'order_id'   => $order->id,
                    'status'     => 'received',
                    'notes'      => 'Pesanan baru dibuat dan diterima sistem.',
                    'updated_by' => Auth::id(),
                    'updated_at' => now(),
                ]);
            }

            $invoiceNumber = $this->generateUniqueNumber('INV', Invoice::class, 'invoice_number');

            Invoice::create([
                'order_id'       => $order->id,
                'invoice_number' => $invoiceNumber,
                'invoice_date'   => $data['order_date'],
                'due_date'       => $data['due_date'] ?? $data['order_date'],
                'total_amount'   => $grandTotal,
                'payment_status' => 'unpaid',
            ]);

            return $order->load(['customer', 'items', 'latestInvoice']);
        });
    }

    public function updateOrder(Order $order, array $data): Order
    {
        return DB::transaction(function () use ($order, $data) {
            $order->update([
                'customer_id' => $data['customer_id'],
                'order_date'  => $data['order_date'],
                'notes'       => $data['notes'] ?? null,
            ]);

            $order->items()->delete();
            $grandTotal = 0;

            foreach ($data['items'] as $item) {
                $product = !empty($item['product_id']) ? Product::find($item['product_id']) : null;
                $productName = $product ? $product->product_name : ($item['product_name'] ?? 'Item Kustom');
                $unit = $product ? $product->unit : ($item['unit'] ?? 'pcs');
                $qty = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $subtotal = round($qty * $unitPrice, 2);

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $product?->id,
                    'product_name' => $productName,
                    'unit'         => $unit,
                    'quantity'     => $qty,
                    'unit_price'   => $unitPrice,
                    'subtotal'     => $subtotal,
                    'item_notes'   => $item['item_notes'] ?? null,
                ]);

                $grandTotal += $subtotal;
            }

            $order->update(['total_amount' => $grandTotal]);

            $invoice = $order->latestInvoice;
            if ($invoice) {
                $invoice->update([
                    'total_amount' => $grandTotal,
                    'due_date'     => $data['due_date'] ?? $invoice->due_date,
                ]);
                app(PaymentService::class)->recalculateInvoiceStatus($invoice);
            }

            return $order->fresh(['customer', 'items', 'latestInvoice']);
        });
    }

    public function cancelOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['order_status' => 'cancelled']);
            if ($order->latestInvoice && $order->latestInvoice->paid_amount <= 0) {
                $order->latestInvoice->update(['payment_status' => 'cancelled']);
            }
        });
    }

    private function generateUniqueNumber(string $prefix, string $modelClass, string $column): string
    {
        $datePart = now()->format('Ymd');
        $countToday = $modelClass::whereDate('created_at', today())->count() + 1;

        do {
            $candidate = sprintf('%s-%s-%04d', $prefix, $datePart, $countToday);
            $exists = $modelClass::where($column, $candidate)->exists();
            $countToday++;
        } while ($exists);

        return $candidate;
    }
}