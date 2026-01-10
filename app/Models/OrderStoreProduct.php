<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStoreProduct extends BaseModel
{
    use HasFactory;

    protected $table = 'order_store_product';

    protected $fillable = [
        'product_id',
        'order_store_id',
        'qty',
    ];

    public function order()
    {
        return $this->belongsTo(StoreOrder::class, 'order_store_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
