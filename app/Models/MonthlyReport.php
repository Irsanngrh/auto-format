<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('transaction_date', 'asc');
    }

    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
            5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
            9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
        ];

        return $months[$this->month] ?? '';
    }
}