<?php

namespace Modules\AdvanceReport\Services;

use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Reports\TotalSalesOverTimeReport;
use Modules\AdvanceReport\Reports\TotalSalesByProductReport;
use Modules\AdvanceReport\Reports\SalesByCustomerNameReport;
use Modules\AdvanceReport\Reports\PaymentsByOrderReport;
use Modules\AdvanceReport\Reports\TotalSalesByOrderReport;
use Modules\AdvanceReport\Reports\PosStaffSalesTotalReport;
use Modules\AdvanceReport\Reports\PosStaffDailySalesTotalReport;
use Modules\AdvanceReport\Reports\PurchaseOrdersOverTimeReport;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;

class ReportDataService
{
    use ReportHelperTrait;

    /**
     * Report class mapping
     *
     * @var array
     */
    protected $reportClasses = [
        'total-sales-over-time' => TotalSalesOverTimeReport::class,
        'total-sales-by-product' => TotalSalesByProductReport::class,
        'sales-by-customer-name' => SalesByCustomerNameReport::class,
        'payments-by-order' => PaymentsByOrderReport::class,
        'total-sales-by-order' => TotalSalesByOrderReport::class,
        'pos-staff-sales-total' => PosStaffSalesTotalReport::class,
        'pos-staff-daily-sales-total' => PosStaffDailySalesTotalReport::class,
        'purchase-orders-over-time' => PurchaseOrdersOverTimeReport::class,
    ];

    /**
     * Generate report data based on report slug
     *
     * @param  string  $slug
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  int  $perPage
     * @param  int  $page
     * @param  string  $search
     * @return array
     */
    public function generateReportData($slug, $fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null, $paymentStatus = null, $orderStatus = null, $channel = null)
    {
        // Check if report class exists
        if (! isset($this->reportClasses[$slug])) {
            return [
                'message' => __('This report is not yet implemented.'),
            ];
        }

        // Get report class
        $reportClass = $this->reportClasses[$slug];

        // Check if class exists
        if (! class_exists($reportClass)) {
            return [
                'message' => __('Report class not found.'),
            ];
        }

        // Instantiate report class
        $report = app($reportClass);

        // Check if it implements the interface
        if (! $report instanceof ReportInterface) {
            return [
                'message' => __('Invalid report class.'),
            ];
        }

        // Generate and return report data
        // Use reflection to call generate() with appropriate parameters based on method signature
        $reflection = new \ReflectionMethod($report, 'generate');
        $parameters = $reflection->getParameters();

        // Build arguments array based on method signature
        $args = [];
        foreach ($parameters as $param) {
            $paramName = $param->getName();

            switch ($paramName) {
                case 'fromDate':
                    $args[] = $fromDate;

                    break;
                case 'toDate':
                    $args[] = $toDate;

                    break;
                case 'vendorId':
                    $args[] = $vendorId;

                    break;
                case 'perPage':
                    $args[] = $perPage;

                    break;
                case 'page':
                    $args[] = $page;

                    break;
                case 'search':
                    $args[] = $search;

                    break;
                case 'sortColumn':
                    $args[] = $sortColumn;

                    break;
                case 'sortDirection':
                    $args[] = $sortDirection;

                    break;
                case 'paymentStatus':
                    $args[] = $paymentStatus;

                    break;
                case 'orderStatus':
                    $args[] = $orderStatus;

                    break;
                case 'channel':
                    $args[] = $channel;

                    break;
                case 'groupBy':
                    // For total-sales-over-time, pass null to let it auto-determine
                    $args[] = null;

                    break;
                default:
                    // Use default value if parameter has one
                    if ($param->isDefaultValueAvailable()) {
                        $args[] = $param->getDefaultValue();
                    } else {
                        $args[] = null;
                    }

                    break;
            }
        }

        return call_user_func_array([$report, 'generate'], $args);
    }

    /**
     * Get total sales over time grouped by date/month/year
     *
     * Note: This method is kept for backward compatibility with vendor controller.
     * New code should use TotalSalesOverTimeReport directly.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  string  $groupBy  'day', 'month', or 'year'
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSalesOverTime($fromDate, $toDate, $groupBy = 'day', $vendorId = null, $search = '')
    {
        // Delegate to TotalSalesOverTimeReport
        $report = app(TotalSalesOverTimeReport::class);

        return $report->getTotalSalesOverTime($fromDate, $toDate, $groupBy, $vendorId, $search);
    }

    /**
     * Get total sales by product
     *
     * Note: This method is kept for backward compatibility.
     * New code should use TotalSalesByProductReport directly.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  int  $perPage
     * @param  int  $page
     * @param  string  $search
     * @param  string|null  $sortColumn
     * @param  string|null  $sortDirection
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTotalSalesByProduct($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null)
    {
        // Delegate to TotalSalesByProductReport
        $report = app(TotalSalesByProductReport::class);

        return $report->getTotalSalesByProduct($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection);
    }

    /**
     * Get total sales by product (non-paginated - for totals calculation)
     *
     * Note: This method is kept for backward compatibility.
     * New code should use TotalSalesByProductReport directly.
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSalesByProductTotals($fromDate, $toDate, $vendorId = null, $search = '')
    {
        // Delegate to TotalSalesByProductReport
        $report = app(TotalSalesByProductReport::class);

        return $report->getTotalSalesByProductTotals($fromDate, $toDate, $vendorId, $search);
    }
}
