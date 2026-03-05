<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use Carbon\Carbon;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class TotalSalesOverTimeReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Total Sales Over Time report data
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string|null  $groupBy
     * @param  string  $search
     * @return array
     */
    public function generate($fromDate, $toDate, $vendorId = null, $groupBy = null, $search = '')
    {
        // Determine grouping type
        if ($groupBy === null) {
            $groupBy = $this->determineGroupBy($fromDate, $toDate);
        }

        // Get current period data
        $salesData = $this->getTotalSalesOverTime($fromDate, $toDate, $groupBy, $vendorId, $search);
        $totalSales = $salesData->sum('total_sales');
        $totalOrders = $salesData->sum('order_count');
        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;

        // Get previous period dates
        $previousPeriod = $this->getPreviousPeriodDates($fromDate, $toDate);

        // Get previous period data with same grouping
        $previousSalesData = $this->getTotalSalesOverTime(
            $previousPeriod['from'],
            $previousPeriod['to'],
            $groupBy,
            $vendorId
        );
        $previousTotalSales = $previousSalesData->sum('total_sales');
        $previousTotalOrders = $previousSalesData->sum('order_count');
        $previousAvgOrderValue = $previousTotalOrders > 0 ? ($previousTotalSales / $previousTotalOrders) : 0;

        // Calculate percentage changes
        $salesChange = $this->calculatePercentageChange($totalSales, $previousTotalSales);
        $ordersChange = $this->calculatePercentageChange($totalOrders, $previousTotalOrders);
        $avgOrderValueChange = $this->calculatePercentageChange($avgOrderValue, $previousAvgOrderValue);

        // Build comparison data based on grouping
        $comparisonData = $this->buildComparisonData(
            $salesData,
            $previousSalesData,
            $fromDate,
            $toDate,
            $previousPeriod,
            $groupBy
        );

        return [
            'salesData' => $salesData,
            'previousSalesData' => $previousSalesData,
            'comparisonData' => $comparisonData,
            'totalSales' => $totalSales,
            'totalOrders' => $totalOrders,
            'avgOrderValue' => $avgOrderValue,
            'previousTotalSales' => $previousTotalSales,
            'previousTotalOrders' => $previousTotalOrders,
            'previousAvgOrderValue' => $previousAvgOrderValue,
            'salesChange' => $salesChange,
            'ordersChange' => $ordersChange,
            'avgOrderValueChange' => $avgOrderValueChange,
            'groupBy' => $groupBy,
            'currentPeriodLabel' => $this->formatPeriodLabel($fromDate, $toDate),
            'previousPeriodLabel' => $this->formatPeriodLabel($previousPeriod['from'], $previousPeriod['to']),
        ];
    }

    /**
     * Build comparison data based on grouping type
     *
     * @param  \Illuminate\Support\Collection  $salesData
     * @param  \Illuminate\Support\Collection  $previousSalesData
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array  $previousPeriod
     * @param  string  $groupBy
     * @return array
     */
    protected function buildComparisonData($salesData, $previousSalesData, $fromDate, $toDate, $previousPeriod, $groupBy)
    {
        $comparisonData = [];

        // Convert to keyed collections
        $currentMap = $salesData->keyBy('period');
        $previousMap = $previousSalesData->keyBy('period');

        // Generate all period points
        if ($groupBy === 'day') {
            $current = Carbon::parse($fromDate);
            $end = Carbon::parse($toDate);
            while ($current->lte($end)) {
                $periodKey = $current->format('Y-m-d');
                $daysDiff = Carbon::parse($fromDate)->diffInDays($current);
                $previousDateObj = Carbon::parse($previousPeriod['from'])->addDays($daysDiff);
                $previousDate = $previousDateObj->format('Y-m-d');

                $currentData = $currentMap->get($periodKey);
                $previousData = $previousMap->get($previousDate);

                $comparisonData[] = [
                    'current_date' => $periodKey,
                    'previous_date' => $previousDate,
                    'current' => $currentData ? [
                        'total_sales' => $currentData->total_sales,
                        'order_count' => $currentData->order_count,
                        'avg_order_value' => $currentData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_sales' => $previousData->total_sales,
                        'order_count' => $previousData->order_count,
                        'avg_order_value' => $previousData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                ];
                $current->addDay();
            }
        } elseif ($groupBy === 'month') {
            $current = Carbon::parse($fromDate)->startOfMonth();
            $end = Carbon::parse($toDate)->startOfMonth();
            while ($current->lte($end)) {
                $periodKey = $current->format('Y-m');
                $monthsDiff = Carbon::parse($fromDate)->startOfMonth()->diffInMonths($current);
                $previousDateObj = Carbon::parse($previousPeriod['from'])->startOfMonth()->addMonths($monthsDiff);
                $previousDate = $previousDateObj->format('Y-m');

                $currentData = $currentMap->get($periodKey);
                $previousData = $previousMap->get($previousDate);

                $comparisonData[] = [
                    'current_date' => $periodKey,
                    'previous_date' => $previousDate,
                    'current' => $currentData ? [
                        'total_sales' => $currentData->total_sales,
                        'order_count' => $currentData->order_count,
                        'avg_order_value' => $currentData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_sales' => $previousData->total_sales,
                        'order_count' => $previousData->order_count,
                        'avg_order_value' => $previousData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                ];
                $current->addMonth();
            }
        } else { // Yearly grouping
            $current = Carbon::parse($fromDate)->startOfYear();
            $end = Carbon::parse($toDate)->startOfYear();
            while ($current->lte($end)) {
                $periodKey = $current->format('Y');
                $yearsDiff = Carbon::parse($fromDate)->startOfYear()->diffInYears($current);
                $previousDateObj = Carbon::parse($previousPeriod['from'])->startOfYear()->addYears($yearsDiff);
                $previousDate = $previousDateObj->format('Y');

                $currentData = $currentMap->get($periodKey);
                $previousData = $previousMap->get($previousDate);

                $comparisonData[] = [
                    'current_date' => $periodKey,
                    'previous_date' => $previousDate,
                    'current' => $currentData ? [
                        'total_sales' => $currentData->total_sales,
                        'order_count' => $currentData->order_count,
                        'avg_order_value' => $currentData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_sales' => $previousData->total_sales,
                        'order_count' => $previousData->order_count,
                        'avg_order_value' => $previousData->avg_order_value,
                    ] : [
                        'total_sales' => 0,
                        'order_count' => 0,
                        'avg_order_value' => 0,
                    ],
                ];
                $current->addYear();
            }
        }

        return $comparisonData;
    }

    /**
     * Get total sales over time grouped by date/month/year
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
        if ($vendorId !== null) {
            // For vendor-specific reports, we need to join with order_details
            // and sum by vendor's sales on each order
            $query = Order::select(
                DB::raw(
                    $groupBy === 'day'
                    ? 'DATE(orders.order_date) as period'
                    : ($groupBy === 'month'
                        ? 'DATE_FORMAT(orders.order_date, "%Y-%m") as period'
                        : 'YEAR(orders.order_date) as period')
                ),
                DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('order_details.vendor_id', $vendorId);
        } else {
            // If no vendor selected, sum total from orders table (all vendors)
            $query = Order::select(
                DB::raw(
                    $groupBy === 'day'
                    ? 'DATE(order_date) as period'
                    : ($groupBy === 'month'
                        ? 'DATE_FORMAT(order_date, "%Y-%m") as period'
                        : 'YEAR(order_date) as period')
                ),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('COUNT(id) as order_count')
            )
                ->whereBetween('order_date', [$fromDate, $toDate]);
        }

        $results = $query->groupBy('period')
            ->orderBy('period', 'desc')
            ->get();

        // Filter by search term if provided (search in date/period)
        if (! empty($search)) {
            $results = $results->filter(function ($item) use ($search) {
                $periodStr = (string) $item->period;

                // Search in formatted date/period
                return stripos($periodStr, $search) !== false;
            })->values();
        }

        // Calculate average order value and format period
        return $results->map(function ($item) {
            $item->period = is_numeric($item->period) ? $item->period : $item->period;
            $item->date = $item->period; // Keep for backward compatibility
            $item->avg_order_value = $item->order_count > 0
                ? ($item->total_sales / $item->order_count)
                : 0;

            return $item;
        });
    }
}
