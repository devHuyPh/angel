<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TotalDowlineMonthHistory extends BaseModel
{
    protected $table = 'total_dowline_month_histories';

    protected $fillable = [
        'customer_id',
        'total_dowline',
        'month'
    ];

    public function user() : BelongsTo {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
}
