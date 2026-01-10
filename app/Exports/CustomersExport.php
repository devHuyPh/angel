<?php

namespace App\Exports;

use App\Models\CusTomer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
  protected $customerId;

  public function __construct($customerId)
  {
    $this->customerId = $customerId;
  }

  public function collection()
  {
    return Customer::with('rank')
      ->where('referral_ids', $this->customerId) // Thêm điều kiện lọc
      ->select([
        'referral_ids',
        'name',
        'email',
        'rank_id',
        'phone',
        'walet_1',
        'total_dowline',

      ])
      ->get()
      ->map(function ($customer) {
        return [
          'Referral ID' => $customer->referral_ids,
          'Tên' => $customer->name,
          'Email' => $customer->email,
          
          'Số điện thoại' => $customer->phone,
            'Ví 1'=>$customer->walet_1,
          'Rank Name' => optional($customer->rank)->rank_name,
          'Tổng tuyến dưới' => $customer->total_dowline,

        ];
      });
  }

  public function headings(): array
  {
    return [
      'ID Người giới thiệu',
      'Tên',
      'Email',

      'Số điện thoại',
    'Ví 1',
      'Rank Name',
      'Tổng tuyến dưới',

    ];
  }
}
