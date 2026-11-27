<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReminderLog extends Model
{
    protected $fillable = ['invoice_id', 'reminder_setting_id', 'channel', 'recipient', 'message', 'sent_at', 'status', 'provider_message_id', 'error_message'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}