<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Model;

class ProfitHistory extends BaseModel
{
    protected $table = 'profit_history';

    protected $fillable = [
        'recipient_id',
        'referrer_id',
        'amount'
    ];

    public function recipient()
    {
        return $this->belongsTo(Customer::class, 'recipient_id');
    }

    public function referrer()
    {
        return $this->belongsTo(Customer::class, 'referrer_id');
    }
}
