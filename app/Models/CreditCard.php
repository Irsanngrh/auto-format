<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }
}