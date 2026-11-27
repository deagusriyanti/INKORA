<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryLog extends Model
{
    public $timestamps = false;
    protected $fillable = ['delivery_id', 'status', 'notes', 'updated_by', 'updated_at'];

    protected function casts(): array
    {
        return ['updated_at' => 'datetime'];
    }

    public function delivery(): BelongsTo { return $this->belongsTo(Delivery::class); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
}