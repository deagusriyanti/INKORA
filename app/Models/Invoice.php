<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = ['order_id', 'invoice_number', 'invoice_date', 'due_date', 'total_amount', 'payment_status'];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date'     => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function payments(): HasMany { return $this->hasMany(Payment::class); }
    public function reminderLogs(): HasMany { return $this->hasMany(ReminderLog::class); }

    public function getPaidAmountAttribute(): float
    {
        return (float) $this->payments()->where('status', 'valid')->sum('amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->total_amount - $this->paid_amount);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'unpaid'    => 'Belum Dibayar',
            'dp'        => 'DP',
            'partial'   => 'Sebagian Dibayar',
            'paid'      => 'Lunas',
            'cancelled' => 'Dibatalkan',
            default     => ucfirst($this->payment_status),
        };
    }
}