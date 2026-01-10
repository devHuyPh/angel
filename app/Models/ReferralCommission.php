<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralCommission extends BaseModel
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'level',
        'commission_amount',
        'percentage',
    ];

    /**
     * Order associated with the commission.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * User (customer) who receives the commission.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

public function orderCommissions()
  {
    return $this->hasMany(self::class, 'order_id', 'order_id');
  }
}
