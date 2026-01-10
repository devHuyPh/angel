<?php

namespace App\Models;
use App\Models\CusTomer;
use Illuminate\Database\Eloquent\Model;

class DailyBonusLog extends Model
{
    protected $table = "daily_bonus_logs";
    protected $fillable = [
        'customer_id',
        'bonus_amount',
        'order_total',
        'distribution_date',
        'created_at'
    ];
public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
