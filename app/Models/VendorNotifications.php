<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Models\Store;

class VendorNotifications extends BaseModel
{
   protected $table = 'vendor_notifications';

    protected $fillable = [
        'title',
        'description',
        'variables',
        'vendor_id',
        'readed',
        'url',
        'viewed'
    ];
    public function vendor()
    {
        return $this->belongsTo(Customer::class, 'vendor_id', 'id');
    }
    public function fromStore()
    {
        return $this->belongsTo(Store::class, 'from_store', 'id');
    }
}
