<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class TotalSalesByOrderFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Order Reference'),
            __('Order Date'),
            __('Customer Name'),
            __('Total Quantity'),
            __('Order Total'),
            __('Order Paid'),
            __('Channel'),
            __('Payment Status'),
            __('Order Status'),
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
            $this->formatValue($data['totalQuantity'] ?? 0),
            $this->formatValue($this->formatNumber($data['totalAmount'] ?? 0)),
            $this->formatValue($this->formatNumber($data['totalPaid'] ?? 0)),
            '',
            '',
            '',
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['orderData']) && count($data['orderData']) > 0) {
            foreach ($data['orderData'] as $order) {
                $csvData[] = [
                    $order['order_reference'] ?? __('N/A'),
                    $this->formatDate($order['order_date'] ?? ''),
                    $order['customer_name'] ?? __('N/A'),
                    $this->formatValue($order['total_quantity'] ?? 0),
                    $this->formatValue($this->formatNumber($order['order_total'] ?? 0)),
                    $this->formatValue($this->formatNumber($order['order_paid'] ?? 0)),
                    ucfirst($order['channel'] ?? __('N/A')),
                    $order['payment_status'] ?? __('N/A'),
                    $order['order_status'] ?? __('N/A'),
                ];
            }
        }

        return $csvData;
    }
}
