<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycLog extends Model
{
    protected $table = 'kyc_logs';

    protected $fillable = [
        'kyc_pending_id',
        'kyc_pending_name',
        'kyc_verification_type',
        'kyc_status',
        'admin_id',
        'admin_name',
        'admin_email',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'action',
        'affected_entity',
        'affected_entity_id',
        'system_notification',
        'data_before',
        'data_after',
        'note',
        'reason',
        'action_at',
    ];

    protected $casts = [
        'action_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'data_before' => 'array',
        'data_after' => 'array',
        'system_notification' => 'boolean',
    ];

    // Quan hệ: Một KycLog thuộc về một KycPending
    public function kycPending()
    {
        return $this->belongsTo(PendingLog::class, 'kyc_pending_id');
    }

    // Quan hệ: Một KycLog thuộc về một User (admin)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
    public function customer()
    {
        return $this->belongsTo(CusTomer::class, 'customer_id');
    }
}
