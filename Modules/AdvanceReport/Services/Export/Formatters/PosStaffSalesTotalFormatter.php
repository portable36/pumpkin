<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class PosStaffSalesTotalFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Staff Name'),
            __('Total Orders'),
            __('Total Sales'),
            __('Average Sales'),
            __('Total Quantity'),
        ];
    }

    public function format(array $data, string $fromDate, string $toDate): array
    {
        $csvData = [];

        // Add header row
        $csvData[] = $this->getHeaders();

        // Add summary row
        $csvData[] = [
            __('Total'),
            $this->formatValue($data['totalOrders'] ?? 0),
            $this->formatValue($this->formatNumber($data['totalSales'] ?? 0)),
            $this->formatValue($this->formatNumber($data['averageSales'] ?? 0)),
            $this->formatValue($data['totalQuantity'] ?? 0),
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['staffData']) && count($data['staffData']) > 0) {
            foreach ($data['staffData'] as $staff) {
                $csvData[] = [
                    $staff['staff_name'] ?? __('N/A'),
                    $this->formatValue($staff['total_orders'] ?? 0),
                    $this->formatValue($this->formatNumber($staff['total_sales'] ?? 0)),
                    $this->formatValue($this->formatNumber($staff['average_sales'] ?? 0)),
                    $this->formatValue($staff['total_quantity'] ?? 0),
                ];
            }
        }

        return $csvData;
    }
}
