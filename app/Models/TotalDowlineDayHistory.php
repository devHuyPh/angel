<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TotalDowlineDayHistory extends BaseModel
{
    protected $table = 'total_dowline_day_histories';

    protected $fillable = [
        'customer_id',
        'total_dowline'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
