<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreeBonusHistory extends Model
{
    use HasFactory;

    protected $table = "area_bonus_histories";
    protected $fillable = [
        'manager_id',
        'customer_id',
        'bonus_amount',
        'month',
        'year',
    ];
}
