<?php

namespace Botble\Ecommerce\Http\Controllers;

use Botble\DataSynchronize\Exporter\Exporter;
use Botble\DataSynchronize\Http\Controllers\ExportController;
use Botble\Ecommerce\Exporters\ReportExporter;

class ExportReportController extends ExportController
{
    protected function getExporter(): Exporter
    {
        return ReportExporter::make();
    }
}
