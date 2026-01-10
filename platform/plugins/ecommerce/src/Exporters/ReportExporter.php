<?php

namespace Botble\Ecommerce\Exporters;

use App\Models\CusTomer;
use Botble\DataSynchronize\Exporter\ExportColumn;
use Botble\DataSynchronize\Exporter\ExportCounter;
use Botble\DataSynchronize\Exporter\Exporter;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderProduct;
use Illuminate\Support\Collection;

class ReportExporter extends Exporter
{
    public function getLabel(): string
    {
        return trans('core/base::layouts.customer');
    }

    public function columns(): array
    {
        return [
            ExportColumn::make('ID'),
            ExportColumn::make('Referrals ID'),
            ExportColumn::make('Name'),
            ExportColumn::make('Phone'),
            ExportColumn::make('Email'),
            ExportColumn::make('Rank'),
            ExportColumn::make('Total Dowline'),
            ExportColumn::make('Wallet 1'),
            ExportColumn::make('Wallet 2'),
            ExportColumn::make('Total Dowline Months'),
        ];
    }

    public function counters(): array
    {
        return [
            ExportCounter::make()
                ->label(trans('core/base::layouts.total_customers'))
                ->value(Customer::where('id', '!=', 1)->count()),
        ];
    }

    public function hasDataToExport(): bool
    {
        return CusTomer::query()->exists();
    }

    public function collection(): Collection
    {
        return CusTomer::query()->get();
    }

    /**
     * @param Order $row
     */
    public function map($row): array
    {

        return [
            'id' => $row->id,
            'referral_ids' => $row->referral_ids,
            'name' => $row->name,
            'phone' => $row->phone,
            'email' => $row->email,
            'rank_id' => $row->rank_id,
            'total_dowline' => $row->total_dowline,
            'walet_1' => $row->walet_1,
            'walet_2' => $row->walet_2,
            'total_dowline_month' => $row->total_dowline_month,
        ];
    }
}
