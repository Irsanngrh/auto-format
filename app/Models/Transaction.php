<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['monthly_report_id', 'transaction_date', 'description', 'amount'];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2'
    ];
}