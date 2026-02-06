<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model {
    protected $guarded = ['id'];
    public function director() { return $this->belongsTo(Director::class); }
}