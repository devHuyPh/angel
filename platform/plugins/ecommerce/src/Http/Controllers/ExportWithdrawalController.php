<?php

namespace Botble\Ecommerce\Http\Controllers;

use Botble\DataSynchronize\Exporter\Exporter;
use Botble\DataSynchronize\Http\Controllers\ExportController;
use Botble\Ecommerce\Exporters\ReportExporter;
use Botble\Ecommerce\Exporters\WithdrawalExporter;

class ExportWithdrawalController extends ExportController
{
    protected function getExporter(): Exporter
    {
        return WithdrawalExporter::make();
    }
}
