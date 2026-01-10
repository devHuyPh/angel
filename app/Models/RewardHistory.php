<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Botble\Ecommerce\Models\Customer;

class RewardHistory extends BaseModel
{
    protected $table = 'reward_history';

    protected $fillable = [
        'customer_id',
        'rank_id',
        'reward',
        'date_reward',
        'rank_name'
    ];

    protected $dates = ['date_reward'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function rank()
    {
        return $this->belongsTo(Ranking::class, 'rank_id');
    }
}
