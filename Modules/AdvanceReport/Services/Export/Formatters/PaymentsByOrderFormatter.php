<?php

namespace Modules\AdvanceReport\Services\Export\Formatters;

class PaymentsByOrderFormatter extends BaseFormatter
{
    public function getHeaders(): array
    {
        return [
            __('Order Reference'),
            __('Order Date'),
            __('Customer Name'),
            __('Customer Email'),
            __('Order Total'),
            __('Order Paid'),
            __('Payment Status'),
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
            '',
            $this->formatValue($this->formatNumber($data['totalAmount'] ?? 0)),
            $this->formatValue($this->formatNumber($data['totalPaid'] ?? 0)),
            '',
        ];

        // Add empty row for separation
        $csvData[] = [];

        // Add data rows
        if (isset($data['paymentData']) && count($data['paymentData']) > 0) {
            foreach ($data['paymentData'] as $payment) {
                $csvData[] = [
                    $payment['order_reference'] ?? __('N/A'),
                    $this->formatDate($payment['order_date'] ?? ''),
                    $payment['customer_name'] ?? __('N/A'),
                    $payment['customer_email'] ?? __('N/A'),
                    $this->formatValue($this->formatNumber($payment['order_total'] ?? 0)),
                    $this->formatValue($this->formatNumber($payment['order_paid'] ?? 0)),
                    $payment['payment_status'] ?? __('N/A'),
                ];
            }
        }

        return $csvData;
    }
}
