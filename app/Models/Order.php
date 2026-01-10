<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'ec_orders';
    protected $fillable = ['user_id', 'amount', 'status', 'is_finished'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }
}
