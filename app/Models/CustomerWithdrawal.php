<?php

namespace App\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerWithdrawal extends BaseModel
{
    use HasFactory;

    protected $table = 'customer_withdrawals'; // Tên bảng

    protected $fillable = [
        'customer_id',
        'amount',
        'currency',
        'status',
        'transaction_id',
        'withdrawal_method',
        'account_name',
        'account_number',
        'bank_name',
        'bank_code',
        'bank_branch',
        'swift_code',
        'notes',
        'admin_id',
        'processed_at',
    	'fee'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Quan hệ với model Customer.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Quan hệ với model Admin (người xử lý giao dịch).
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Kiểm tra xem yêu cầu rút tiền đã được xử lý chưa.
     */
    public function isProcessed()
    {
        return in_array($this->status, ['approved', 'rejected', 'cancelled']);
    }
}
