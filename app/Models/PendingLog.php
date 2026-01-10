<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingLog extends Model
{
    protected $fillable = [
        'customer_id', 'kyc_form_id', 'name', 'avatar', 'verification_type','data','status'
    ];
    protected $table='kyc_pending';
    protected $casts = [
        'data' => 'array', 
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function kycForm()
    {
        return $this->belongsTo(KycForm::class, 'kyc_form_id');
    }
    public function logs()
    {
        return $this->hasMany(KycLog::class, 'kyc_pending_id');
    }
    public function customer()
    {
        return $this->belongsTo(CusTomer::class, 'customer_id');
    }
}
