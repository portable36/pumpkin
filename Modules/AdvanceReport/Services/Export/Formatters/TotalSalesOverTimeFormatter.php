<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class TotalSalesOverTimeFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Date'),
            __('Number of Orders'),
            __('Total Sales'),
            __('Average Order Value'),
        ];
    }

    public function format(array $data, string $fromDate, string $toDate): array
    {
        return $this->formatTimeSeriesComparison($data, $fromDate, $toDate);
    }
}
