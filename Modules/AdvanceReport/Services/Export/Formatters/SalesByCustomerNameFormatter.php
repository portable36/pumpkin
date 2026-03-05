<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class SalesByCustomerNameFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Customer Name'),
            __('Customer Email'),
            __('Vendor Name'),
            __('Total Sales'),
            __('Total Paid'),
            __('Total Quantity'),
            __('Order Count'),
            __('Average Order Value'),
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
            '',
            '',
            $this->formatValue($this->formatNumber($data['totalSales'] ?? 0)),
            $this->formatValue($this->formatNumber($data['totalPaid'] ?? 0)),
            $this->formatValue($data['totalQuantity'] ?? 0),
            $this->formatValue($data['totalOrders'] ?? 0),
            '',
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['customerData']) && count($data['customerData']) > 0) {
            foreach ($data['customerData'] as $customer) {
                $csvData[] = [
                    $customer['customer_name'] ?? __('N/A'),
                    $customer['customer_email'] ?? __('N/A'),
                    $customer['vendor_name'] ?? __('N/A'),
                    $this->formatValue($this->formatNumber($customer['total_sales'] ?? 0)),
                    $this->formatValue($this->formatNumber($customer['total_paid'] ?? 0)),
                    $this->formatValue($customer['total_quantity'] ?? 0),
                    $this->formatValue($customer['order_count'] ?? 0),
                    $this->formatValue($this->formatNumber($customer['avg_order_value'] ?? 0)),
                ];
            }
        }

        return $csvData;
    }
}
