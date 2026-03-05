<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class TotalSalesByProductFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Product Name'),
            __('Vendor Name'),
            __('Quantity Sold'),
            __('Total Sales'),
            __('Total Tax'),
            __('Order Count'),
            __('Average Sales Value'),
        ];
    }

    public function format(array $data, string $fromDate, string $toDate): array
    {
        $csvData = [];

        // Add header row
        $csvData[] = $this->getHeaders();

        // Add summary row
        $csvData[] = [
            '',
            '',
            $this->formatValue($data['totalQuantity'] ?? 0),
            $this->formatValue($this->formatNumber($data['totalSales'] ?? 0)),
            $this->formatValue($this->formatNumber($data['totalTax'] ?? 0)),
            $this->formatValue($data['totalOrders'] ?? 0),
            $this->formatValue($this->formatNumber($data['avgSalesValue'] ?? 0)),
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['productData']) && count($data['productData']) > 0) {
            foreach ($data['productData'] as $product) {
                $csvData[] = [
                    $product['product_name'] ?? __('N/A'),
                    $product['vendor_name'] ?? __('N/A'),
                    $this->formatValue($product['quantity_sold'] ?? 0),
                    $this->formatValue($this->formatNumber($product['total_sales'] ?? 0)),
                    $this->formatValue($this->formatNumber($product['total_tax'] ?? 0)),
                    $this->formatValue($product['order_count'] ?? 0),
                    $this->formatValue($this->formatNumber($product['avg_unit_price'] ?? 0)),
                ];
            }
        }

        return $csvData;
    }
}
