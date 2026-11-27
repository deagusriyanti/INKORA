<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderSetting extends Model
{
    protected $fillable = ['channel', 'reminder_days', 'subject', 'message_template', 'is_active'];

    protected function casts(): array
    {
        return [
            'reminder_days' => 'integer',
            'is_active'     => 'boolean',
        ];
    }
}