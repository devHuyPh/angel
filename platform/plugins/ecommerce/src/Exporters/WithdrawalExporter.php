<?php

namespace Botble\Ecommerce\Exporters;

use App\Models\CusTomer;
use Botble\DataSynchronize\Exporter\ExportColumn;
use Botble\DataSynchronize\Exporter\ExportCounter;
use Botble\DataSynchronize\Exporter\Exporter;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Illuminate\Support\Collection;
use App\Models\CustomerWithdrawal;

class WithdrawalExporter extends Exporter
{
    public function getLabel(): string
    {
        return trans('core/base::layouts.withdrawal');
    }

    public function columns(): array
    {
        return [
            ExportColumn::make('ID'),
            ExportColumn::make('Customer Name'),
            ExportColumn::make('Amount'),
            ExportColumn::make('Bank Name'),
            ExportColumn::make('Bank Account'),
            ExportColumn::make('Bank Code'),
            ExportColumn::make('Account Number'),
            ExportColumn::make('Account Holder'),
            ExportColumn::make('Transaction Id'),
            ExportColumn::make('Bank Branch'),
            ExportColumn::make('Swift Code'),
            ExportColumn::make('Created At'),
            ExportColumn::make('Fee'),
        ];
    }

    public function counters(): array
    {
        return [
            ExportCounter::make()
                ->label(trans('core/base::layouts.total_withdrawal'))
                ->value(CustomerWithdrawal::where('status', 'pending')->count()),
        ];
    }

    public function hasDataToExport(): bool
    {
        return CustomerWithdrawal::query()->exists();
    }

    public function collection(): Collection
    {
        return CustomerWithdrawal::query()->get();
    }

    /**
     * @param Order $row
     */
    public function map($row): array
    {

        return [
            'ID' => $row->id,
            'Customer Name' => $row->customer->name,
            'Amount' => $row->amount,
            'Bank Name' => $row->bank_name,
            'Bank Account' => $row->account_name,
            'Bank Code' => $row->bank_code,
            'Account Number' => $row->account_number,
            'Account Holder' => $row->account_name,
            'Transaction Id' => $row->transaction_id,
            'Bank Branch' => $row->bank_branch,
            'Swift Code' => $row->swift_code,
            'Created At' => $row->created_at,
            'Fee' => $row->fee,
        ];
    }
}
