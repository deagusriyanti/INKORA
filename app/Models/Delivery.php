<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Delivery extends Model
{
    protected $fillable = ['order_id', 'delivery_address', 'delivery_date', 'delivery_method', 'delivery_status', 'tracking_number', 'notes'];

    protected function casts(): array
    {
        return ['delivery_date' => 'date'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function logs(): HasMany { return $this->hasMany(DeliveryLog::class)->orderByDesc('updated_at'); }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->delivery_status) {
            'not_shipped' => 'Belum Dikirim',
            'in_transit'  => 'Dalam Proses Pengiriman',
            'delivered'   => 'Sudah Diterima Pelanggan',
            default       => ucfirst($this->delivery_status),
        };
    }
}