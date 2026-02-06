<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model {
    protected $guarded = ['id'];
    protected $casts = ['transaction_date' => 'date'];
    public function monthlyReport() { return $this->belongsTo(MonthlyReport::class); }
}