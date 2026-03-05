<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

use Modules\AdvanceReport\Services\Export\Contracts\ExportFormatterInterface;

abstract class BaseFormatter implements ExportFormatterInterface
{
    /**
     * Format value to prevent Excel from interpreting as dates
     * Uses equals sign with quotes format (= "value") which Excel treats as text
     *
     * @param  mixed  $value
     */
    protected function formatValue($value): string
    {
        $stringValue = (string) $value;

        // If value contains "/" or "-" and looks like a number/date pattern, use equals format
        // Excel treats = "value" as a formula that returns text, preventing date conversion
        if (preg_match('/^\d+[\/\-]\d+/', $stringValue)) {
            return '="' . str_replace('"', '""', $stringValue) . '"';
        }

        // Also check for patterns like "1 / 0" (with spaces)
        if (preg_match('/^\d+\s*[\/\-]\s*\d+/', $stringValue)) {
            return '="' . str_replace('"', '""', $stringValue) . '"';
        }

        return $stringValue;
    }

    /**
     * Format number with currency symbol
     *
     * @param  float  $value
     */
    protected function formatNumber($value): string
    {
        return formatNumber($value);
    }

    /**
     * Format date
     */
    protected function formatDate(string $date): string
    {
        return formatDate($date);
    }

    /**
     * Format time series comparison data for CSV export
     * Shared implementation for formatters that display current vs previous period comparisons
     *
     * @param  array  $data
     * @param  string  $fromDate
     * @param  string  $toDate
     * @return array
     */
    protected function formatTimeSeriesComparison(array $data, string $fromDate, string $toDate): array
    {
        $csvData = [];
        $groupBy = $data['groupBy'] ?? 'day';

        // Add header row
        $csvData[] = $this->getHeaders();

        // Add summary row
        $csvData[] = [
            __('Current Period') . ' / ' . __('Previous Period'),
            $this->formatValue($data['totalOrders'] . ' / ' . $data['previousTotalOrders']),
            $this->formatValue($this->formatNumber($data['totalSales']) . ' / ' . $this->formatNumber($data['previousTotalSales'])),
            $this->formatValue($this->formatNumber($data['avgOrderValue']) . ' / ' . $this->formatNumber($data['previousAvgOrderValue'])),
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['comparisonData']) && count($data['comparisonData']) > 0) {
            foreach ($data['comparisonData'] as $comparison) {
                $currentDate = $comparison['current_date'];
                $previousDate = $comparison['previous_date'];

                // Format date based on groupBy
                if ($groupBy === 'day') {
                    $formattedDate = $this->formatDate($currentDate) . ' / ' . $this->formatDate($previousDate);
                } elseif ($groupBy === 'month') {
                    $currentFormatted = \Carbon\Carbon::createFromFormat('Y-m', $currentDate)->format('M Y');
                    $previousFormatted = \Carbon\Carbon::createFromFormat('Y-m', $previousDate)->format('M Y');
                    $formattedDate = $currentFormatted . ' / ' . $previousFormatted;
                } else {
                    $formattedDate = $currentDate . ' / ' . $previousDate;
                }

                $csvData[] = [
                    $formattedDate,
                    $this->formatValue($comparison['current']['order_count'] . ' / ' . $comparison['previous']['order_count']),
                    $this->formatValue($this->formatNumber($comparison['current']['total_sales']) . ' / ' . $this->formatNumber($comparison['previous']['total_sales'])),
                    $this->formatValue($this->formatNumber($comparison['current']['avg_order_value']) . ' / ' . $this->formatNumber($comparison['previous']['avg_order_value'])),
                ];
            }
        }

        return $csvData;
    }
}
