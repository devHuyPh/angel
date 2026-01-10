<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Model;

// use Botble\Base\Models\BaseModel;


class TotalRevenue extends Model
{
    protected $table = 'ec_total_revenue_of_downline';
    protected $fillable = ['amount','percentage'];

    public function customers()
    {
        return $this->hasMany(Customer::class, 'rank_id');
    }
}
