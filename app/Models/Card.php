<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    protected $fillable = [
        'name',
        'number',
        'cashback',
        'value',
        'expiration_date',
        'gift_description',
        'image',
    ];
    protected $casts = [
        'cashback' => 'float',
        'value' => 'double',
        'expiration_date' => 'date',

    ];


    public function getImageUrlAttribute()
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
