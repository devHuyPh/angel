<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Botble\Marketplace\Models\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorLateDelivery extends BaseModel
{
    protected $table = 'vendor_late_deliveries';

    protected $fillable = [
        'store_id',
        'order_id',
        'customer_id',
        'status',
    ];

    /**
     * Liên kết tới cửa hàng (store)
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    /**
     * Liên kết tới đơn hàng
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Liên kết tới khách hàng
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
