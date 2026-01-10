<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerNotification extends BaseModel
{
    protected $table = 'customer_notifications';

    protected $fillable = [
        'title',
        'dessription',
        'variables',
        'customer_id',
        'readed',
        'url',
        'viewed'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
