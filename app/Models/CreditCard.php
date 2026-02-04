<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    protected $fillable = ['director_id', 'bank_name', 'card_number'];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }
}