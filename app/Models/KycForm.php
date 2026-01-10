<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class KycForm extends BaseModel
{
    protected $table = 'kyc_forms';

    protected $fillable = [
        'name',
        'form',
        'status'
    ];
    protected $casts=[
        'fields'=>'array',
        'create_at'=>'datetime',
        'update_at'=>'datetime',
  
    ];
    public function kycPendings()
    {
        return $this->hasMany(PendingLog::class, 'kyc_form_id');
    }
}
