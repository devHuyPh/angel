<?php

namespace Botble\Ecommerce\Models;
use Botble\Base\Models\BaseModel;

class WareHouseReferral extends BaseModel
{
    protected $table = 'ware_house_referral';
    protected $fillable = ['order_id', 'amount', 'customer_id'];

}