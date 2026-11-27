<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $fillable = ['company_name', 'address', 'phone', 'email', 'logo', 'invoice_footer_note'];
}