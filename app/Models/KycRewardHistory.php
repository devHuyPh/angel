<?php

namespace App\Models;
use Botble\Base\Models\BaseModel;
use App\Models\CusTomer;
use Illuminate\Database\Eloquent\Model;

class KycRewardHistory extends BaseModel
{
    protected $table='kyc_rewards_history';
    protected $fillable=['id','customer_id','reward_type','reward_value','description','create_at','update_at'];
    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
