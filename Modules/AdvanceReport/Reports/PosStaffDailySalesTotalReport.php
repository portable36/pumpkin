<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use Carbon\Carbon;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class PosStaffDailySalesTotalReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate POS Staff Daily Sales Total report data
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
        // Check if POS module is active
        if (! isActive('Pos')) {
            return [
                'message' => __('You need to install and active Pos module to see report'),
            ];
        }

        // Determine grouping type
        if ($groupBy === null) {
            $groupBy = $this->determineGroupBy($fromDate, $toDate);
        }

        // Get current period data (grouped by date/month/year)
        $salesData = $this->getPosStaffDailySalesTotal($fromDate, $toDate, $groupBy, $vendorId, $search);
        $totalSales = $salesData->sum('total_sales');
        $totalOrders = $salesData->sum('order_count');
        $avgOrderValue = $totalOrders > 0 ? ($totalSales / $totalOrders) : 0;

        // Get previous period dates
        $previousPeriod = $this->getPreviousPeriodDates($fromDate, $toDate);

        // Get previous period data with same grouping
        $previousSalesData = $this->getPosStaffDailySalesTotal(
            $previousPeriod['from'],
            $previousPeriod['to'],
            $groupBy,
            $vendorId,
            $search
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
     * Get POS staff daily sales total grouped by date/month/year (aggregated across all staff)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  string  $groupBy  'day', 'month', or 'year'
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getPosStaffDailySalesTotal($fromDate, $toDate, $groupBy = 'day', $vendorId = null, $search = '')
    {
        // Determine the grouping format based on $groupBy
        $periodSelect = DB::raw(
            $groupBy === 'day'
            ? 'DATE(orders.order_date) as period'
            : ($groupBy === 'month'
                ? 'DATE_FORMAT(orders.order_date, "%Y-%m") as period'
                : 'YEAR(orders.order_date) as period')
        );

        $groupBySql = DB::raw(
            $groupBy === 'day'
            ? 'DATE(orders.order_date)'
            : ($groupBy === 'month'
                ? 'DATE_FORMAT(orders.order_date, "%Y-%m")'
                : 'YEAR(orders.order_date)')
        );

        // Exclude vendor-admin users, only include staff (vendor-staff)
        if ($vendorId !== null && $vendorId !== '') {
            // When vendor is filtered, calculate vendor-specific totals from order_details
            $query = Order::select(
                $periodSelect,
                DB::raw('SUM(order_details.price * order_details.quantity + COALESCE(order_details.shipping_charge, 0) + COALESCE(order_details.tax_charge, 0)) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
                ->leftJoin('users', 'orders.staff_id', '=', 'users.id')
                ->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
                ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('orders.channel', 'pos')
                ->where('order_details.vendor_id', $vendorId)
                ->whereNotNull('orders.staff_id')
                ->where(function ($q) {
                    $q->where('roles.slug', '!=', 'vendor-admin')
                        ->where('roles.slug', '!=', 'super-admin')
                        ->orWhereNull('roles.slug');
                })
                ->groupBy($groupBySql);
        } else {
            // When no vendor filter, use order totals
            $query = Order::select(
                $periodSelect,
                DB::raw('SUM(orders.total) as total_sales'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count')
            )
                ->leftJoin('users', 'orders.staff_id', '=', 'users.id')
                ->leftJoin('role_users', 'users.id', '=', 'role_users.user_id')
                ->leftJoin('roles', 'role_users.role_id', '=', 'roles.id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('orders.channel', 'pos')
                ->whereNotNull('orders.staff_id')
                ->where(function ($q) {
                    $q->where('roles.slug', '!=', 'vendor-admin')
                        ->where('roles.slug', '!=', 'super-admin')
                        ->orWhereNull('roles.slug');
                })
                ->groupBy($groupBySql);
        }

        // Note: Search functionality removed since we're not showing staff names

        $results = $query->orderBy('period', 'desc')
            ->get();

        // Calculate average order value and format period
        return $results->map(function ($item) {
            $item->avg_order_value = $item->order_count > 0
                ? ($item->total_sales / $item->order_count)
                : 0;

            return $item;
        });
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
}
