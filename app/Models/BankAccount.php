<?php
namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_code',
        'account_number',
        'account_holder',
        'branch',
        'swift_code',
        'sepay_webhook_secret',
        'payment_sepay_prefix'
    ];

    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

	public function bank(){
      return $this->belongsTo(Bank::class, 'bank_code', 'bank_code');
    }
}
