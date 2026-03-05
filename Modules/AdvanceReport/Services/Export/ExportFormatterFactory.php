<?php

namespace Modules\AdvanceReport\Services\Export;

use Modules\AdvanceReport\Services\Export\Contracts\ExportFormatterInterface;
use Modules\AdvanceReport\Services\Export\Formatters\TotalSalesOverTimeFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\TotalSalesByProductFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\TotalSalesByOrderFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\PaymentsByOrderFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\SalesByCustomerNameFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\PosStaffSalesTotalFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\PosStaffDailySalesTotalFormatter;
use Modules\AdvanceReport\Services\Export\Formatters\PurchaseOrdersOverTimeFormatter;

class ExportFormatterFactory
{
    /**
     * Mapping of report slugs to formatter classes
     *
     * @var array
     */
    protected $formatters = [
        'total-sales-over-time' => TotalSalesOverTimeFormatter::class,
        'total-sales-by-product' => TotalSalesByProductFormatter::class,
        'total-sales-by-order' => TotalSalesByOrderFormatter::class,
        'payments-by-order' => PaymentsByOrderFormatter::class,
        'sales-by-customer-name' => SalesByCustomerNameFormatter::class,
        'pos-staff-sales-total' => PosStaffSalesTotalFormatter::class,
        'pos-staff-daily-sales-total' => PosStaffDailySalesTotalFormatter::class,
        'purchase-orders-over-time' => PurchaseOrdersOverTimeFormatter::class,
    ];

    /**
     * Create formatter for given report slug
     */
    public function create(string $slug): ?ExportFormatterInterface
    {
        if (! isset($this->formatters[$slug])) {
            return null;
        }

        $formatterClass = $this->formatters[$slug];

        if (! class_exists($formatterClass)) {
            return null;
        }

        $formatter = app($formatterClass);

        if (! $formatter instanceof ExportFormatterInterface) {
            return null;
        }

        return $formatter;
    }

    /**
     * Check if report has export support
     */
    public function hasExport(string $slug): bool
    {
        return isset($this->formatters[$slug]);
    }
}
