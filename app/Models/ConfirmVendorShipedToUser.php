<?php

namespace App\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfirmVendorShipedToUser extends BaseModel
{
    use HasFactory;

    protected $table = 'confirm_vendor_shiped_to_users';

    protected $fillable = [
        'customer_id',
        'admin_id',
        'order_id',
        'shipping_fee',
        'status',
        'note',
    ];

    protected $casts = [
        'shipping_fee' => 'decimal:2',
    ];

    public $timestamps = true;

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
