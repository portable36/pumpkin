<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

use Carbon\Carbon;

class PurchaseOrdersOverTimeFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Date'),
            __('Purchase Count'),
            __('Total Amount'),
            __('Total Products'),
            __('Average Purchase Value'),
        ];
    }

    public function format(array $data, string $fromDate, string $toDate): array
    {
        $csvData = [];
        $groupBy = $data['groupBy'] ?? 'day';

        // Add header row
        $csvData[] = $this->getHeaders();

        // Add summary row
        $csvData[] = [
            __('Current Period') . ' / ' . __('Previous Period'),
            $this->formatValue($data['totalPurchases'] . ' / ' . $data['previousTotalPurchases']),
            $this->formatValue($this->formatNumber($data['totalAmount']) . ' / ' . $this->formatNumber($data['previousTotalAmount'])),
            $this->formatValue($data['totalProduct'] . ' / ' . $data['previousTotalProduct']),
            $this->formatValue($this->formatNumber($data['avgPurchaseValue']) . ' / ' . $this->formatNumber($data['previousAvgPurchaseValue'])),
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
                    $currentFormatted = Carbon::createFromFormat('Y-m', $currentDate)->format('M Y');
                    $previousFormatted = Carbon::createFromFormat('Y-m', $previousDate)->format('M Y');
                    $formattedDate = $currentFormatted . ' / ' . $previousFormatted;
                } else {
                    $formattedDate = $currentDate . ' / ' . $previousDate;
                }

                $csvData[] = [
                    $formattedDate,
                    $this->formatValue($comparison['current']['purchase_count'] . ' / ' . $comparison['previous']['purchase_count']),
                    $this->formatValue($this->formatNumber($comparison['current']['total_amount']) . ' / ' . $this->formatNumber($comparison['previous']['total_amount'])),
                    $this->formatValue($comparison['current']['total_product'] . ' / ' . $comparison['previous']['total_product']),
                    $this->formatValue($this->formatNumber($comparison['current']['avg_purchase_value']) . ' / ' . $this->formatNumber($comparison['previous']['avg_purchase_value'])),
                ];
            }
        }

        return $csvData;
    }
}
