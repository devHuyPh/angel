<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Marketplace\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreOrder extends BaseModel
{
    use HasFactory;

    protected $table = 'store_orders';

    protected $fillable = [
    	'type',
        'from_store',
        'to_store',
        'status',
        'confirm_date',
        'transaction_code',
        'payment_status',
        'amount'
    ];

    protected $casts = [
        'confirm_date' => 'datetime',
    ];

    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store');
    }

    public function toStore()
    {
        return $this->belongsTo(Store::class, 'to_store');
    }

    public function products()
    {
        return $this->hasMany(OrderStoreProduct::class, 'order_store_id');
    }

public function referralCommissions()
  {
    return $this->hasMany(ReferralCommission::class, 'order_id');
  }
}
