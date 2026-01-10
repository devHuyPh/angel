<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscountCustomer extends BaseModel
{
    protected $table = 'ec_discount_customers';
    public $timestamps = false;

    protected $fillable = [
        'discount_id',
        'customer_id',
    ];

    public function customers(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }
    public function discount(){
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
