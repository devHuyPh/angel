<?php
// app\Models\WalletTransfer.php
namespace App\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransfer extends BaseModel
{
    protected $table = 'wallet_transfers';

    protected $fillable = [
        'from_customer_id',
        'to_customer_id',
        'amount',
        'reference',
        'status',
        'note',
        'code_used',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'from_customer_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'to_customer_id');
    }
}
