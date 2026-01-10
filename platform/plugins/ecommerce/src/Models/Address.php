<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Traits\LocationTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Address extends BaseModel
{
    use LocationTrait;

    protected $table = 'ec_customer_addresses';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'customer_id',
        'is_default',
        'ward_id',       
        'ward_name',     
        'address_detail' 
    ];

    protected function addressDetail(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ?: $this->address
        );
    }
}
