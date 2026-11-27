<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['order_id', 'status', 'notes', 'updated_by', 'updated_at'];

    protected function casts(): array
    {
        return ['updated_at' => 'datetime'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}