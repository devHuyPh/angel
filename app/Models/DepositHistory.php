<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DepositHistory extends BaseModel
{
    use HasFactory;

    protected $table = 'deposit_histories';

    protected $fillable = [
        'user_id',
        'amount',
        'method',
        'status',
        'transaction_code',
        'note',
        'confirmed_at',
        'currency',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the deposit.
     */
    public function user()
    {
        return $this->belongsTo(Customer::class);
    }
}
