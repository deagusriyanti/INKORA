<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function recordPayment(Invoice $invoice, array $data): Payment
    {
        return DB::transaction(function () use ($invoice, $data) {
            $invoice->refresh();

            if ($invoice->payment_status === 'cancelled') {
                throw ValidationException::withMessages([
                    'amount' => 'Invoice yang sudah dibatalkan tidak dapat menerima pembayaran.',
                ]);
            }

            $remaining = $invoice->remaining_amount;
            $amount = round((float) $data['amount'], 2);

            if ($remaining <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'Invoice ini sudah lunas (sisa tagihan Rp 0).',
                ]);
            }

            if ($amount > $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Nominal pembayaran (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').',
                ]);
            }

            if (!FeatureGateService::allows('dp_payment') && $amount < $remaining) {
                throw ValidationException::withMessages([
                    'amount' => 'Paket Inkora Basic hanya mendukung pembayaran lunas penuh sebesar Rp ' . number_format($remaining, 0, ',', '.') . '.',
                ]);
            }

            $validCountBefore = $invoice->payments()->where('status', 'valid')->count();
            $isSettledNow = ($remaining - $amount) <= 0.009;

            $paymentType = match (true) {
                $validCountBefore === 0 && $isSettledNow => 'full',
                $validCountBefore === 0 && !$isSettledNow => 'dp',
                $validCountBefore > 0 && $isSettledNow => 'settlement',
                default => 'installment',
            };

            $payment = Payment::create([
                'invoice_id'     => $invoice->id,
                'payment_date'   => $data['payment_date'],
                'amount'         => $amount,
                'payment_method' => $data['payment_method'],
                'payment_type'   => $paymentType,
                'status'         => 'valid',
                'notes'          => $data['notes'] ?? null,
                'created_by'     => Auth::id(),
            ]);

            $this->recalculateInvoiceStatus($invoice);

            return $payment;
        });
    }

    public function cancelPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'cancelled']);
            $this->recalculateInvoiceStatus($payment->invoice);
        });
    }

    public function recalculateInvoiceStatus(Invoice $invoice): void
    {
        $invoice->refresh();

        if ($invoice->payment_status === 'cancelled') {
            return;
        }

        $validPayments = $invoice->payments()->where('status', 'valid')->orderBy('id')->get();
        $totalPaid = (float) $validPayments->sum('amount');
        $totalInvoice = (float) $invoice->total_amount;

        if ($totalPaid <= 0) {
            $newStatus = 'unpaid';
        } elseif ($totalPaid >= $totalInvoice) {
            $newStatus = 'paid';
        } else {
            $firstValid = $validPayments->first();
            $newStatus = ($validPayments->count() === 1 && $firstValid?->payment_type === 'dp') ? 'dp' : 'partial';
        }

        $invoice->update(['payment_status' => $newStatus]);
    }
}