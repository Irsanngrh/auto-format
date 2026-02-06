<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Director extends Model {
    protected $guarded = ['id'];
    public function creditCards() { return $this->hasMany(CreditCard::class); }
    public function monthlyReports() { return $this->hasMany(MonthlyReport::class); }
}