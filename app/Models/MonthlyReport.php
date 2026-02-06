<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MonthlyReport extends Model {
    protected $guarded = ['id'];
    public function director() { return $this->belongsTo(Director::class); }
    public function creditCard() { return $this->belongsTo(CreditCard::class); }
    public function transactions() { return $this->hasMany(Transaction::class); }
    
    public function getMonthNameAttribute() {
        $months = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
        return $months[$this->month] ?? '';
    }
}