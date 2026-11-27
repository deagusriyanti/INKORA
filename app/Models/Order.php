<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = ['customer_id', 'order_number', 'order_date', 'total_amount', 'notes', 'order_status', 'created_by'];

    protected function casts(): array
    {
        return [
            'order_date'   => 'date',
            'total_amount' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function latestInvoice(): HasOne { return $this->hasOne(Invoice::class)->latestOfMany(); }
    public function productionLogs(): HasMany { return $this->hasMany(ProductionLog::class)->orderByDesc('updated_at'); }
    public function delivery(): HasOne { return $this->hasOne(Delivery::class); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->order_status) {
            'received'      => 'Pesanan Diterima',
            'queued'        => 'Menunggu Produksi',
            'in_production' => 'Sedang Diproduksi',
            'completed'     => 'Produksi Selesai',
            'ready'         => 'Siap Dikirim/Diambil',
            'handed_over'   => 'Sudah Diserahkan ke Pelanggan',
            'cancelled'     => 'Dibatalkan',
            default         => ucfirst($this->order_status),
        };
    }
}