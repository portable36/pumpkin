<?php

namespace Modules\AdvanceReport\Reports;

use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Entities\PurchaseDetail;
use Carbon\Carbon;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class PurchaseOrdersOverTimeReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Purchase Orders Over Time report data
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
        // Check if Inventory module is active
        if (! isActive('Inventory')) {
            return [
                'message' => __('You need to install and active Inventory module to see report'),
            ];
        }

        // Determine grouping type
        if ($groupBy === null) {
            $groupBy = $this->determineGroupBy($fromDate, $toDate);
        }

        // Get current period data
        $purchaseData = $this->getPurchaseOrdersOverTime($fromDate, $toDate, $groupBy, $vendorId, $search);
        $totalAmount = $purchaseData->sum('total_amount');
        $totalProduct = $purchaseData->sum('total_product');
        $totalPurchases = $purchaseData->sum('purchase_count');
        $avgPurchaseValue = $totalPurchases > 0 ? ($totalAmount / $totalPurchases) : 0;

        // Get previous period dates
        $previousPeriod = $this->getPreviousPeriodDates($fromDate, $toDate);

        // Get previous period data with same grouping
        $previousPurchaseData = $this->getPurchaseOrdersOverTime(
            $previousPeriod['from'],
            $previousPeriod['to'],
            $groupBy,
            $vendorId,
            $search
        );
        $previousTotalAmount = $previousPurchaseData->sum('total_amount');
        $previousTotalProduct = $previousPurchaseData->sum('total_product');
        $previousTotalPurchases = $previousPurchaseData->sum('purchase_count');
        $previousAvgPurchaseValue = $previousTotalPurchases > 0 ? ($previousTotalAmount / $previousTotalPurchases) : 0;

        // Calculate percentage changes
        $amountChange = $this->calculatePercentageChange($totalAmount, $previousTotalAmount);
        $productChange = $this->calculatePercentageChange($totalProduct, $previousTotalProduct);
        $purchasesChange = $this->calculatePercentageChange($totalPurchases, $previousTotalPurchases);
        $avgPurchaseValueChange = $this->calculatePercentageChange($avgPurchaseValue, $previousAvgPurchaseValue);

        // Build comparison data based on grouping
        $comparisonData = $this->buildComparisonData(
            $purchaseData,
            $previousPurchaseData,
            $fromDate,
            $toDate,
            $previousPeriod,
            $groupBy
        );

        return [
            'purchaseData' => $purchaseData,
            'previousPurchaseData' => $previousPurchaseData,
            'comparisonData' => $comparisonData,
            'totalAmount' => $totalAmount,
            'totalProduct' => $totalProduct,
            'totalPurchases' => $totalPurchases,
            'avgPurchaseValue' => $avgPurchaseValue,
            'previousTotalAmount' => $previousTotalAmount,
            'previousTotalProduct' => $previousTotalProduct,
            'previousTotalPurchases' => $previousTotalPurchases,
            'previousAvgPurchaseValue' => $previousAvgPurchaseValue,
            'amountChange' => $amountChange,
            'productChange' => $productChange,
            'purchasesChange' => $purchasesChange,
            'avgPurchaseValueChange' => $avgPurchaseValueChange,
            'groupBy' => $groupBy,
            'currentPeriodLabel' => $this->formatPeriodLabel($fromDate, $toDate),
            'previousPeriodLabel' => $this->formatPeriodLabel($previousPeriod['from'], $previousPeriod['to']),
        ];
    }

    /**
     * Build comparison data based on grouping type
     *
     * @param  \Illuminate\Support\Collection  $purchaseData
     * @param  \Illuminate\Support\Collection  $previousPurchaseData
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  array  $previousPeriod
     * @param  string  $groupBy
     * @return array
     */
    protected function buildComparisonData($purchaseData, $previousPurchaseData, $fromDate, $toDate, $previousPeriod, $groupBy)
    {
        $comparisonData = [];

        // Convert to keyed collections
        $currentMap = $purchaseData->keyBy('period');
        $previousMap = $previousPurchaseData->keyBy('period');

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
                        'total_amount' => $currentData->total_amount,
                        'total_product' => $currentData->total_product,
                        'purchase_count' => $currentData->purchase_count,
                        'avg_purchase_value' => $currentData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_amount' => $previousData->total_amount,
                        'total_product' => $previousData->total_product,
                        'purchase_count' => $previousData->purchase_count,
                        'avg_purchase_value' => $previousData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
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
                        'total_amount' => $currentData->total_amount,
                        'total_product' => $currentData->total_product,
                        'purchase_count' => $currentData->purchase_count,
                        'avg_purchase_value' => $currentData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_amount' => $previousData->total_amount,
                        'total_product' => $previousData->total_product,
                        'purchase_count' => $previousData->purchase_count,
                        'avg_purchase_value' => $previousData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
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
                        'total_amount' => $currentData->total_amount,
                        'total_product' => $currentData->total_product,
                        'purchase_count' => $currentData->purchase_count,
                        'avg_purchase_value' => $currentData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
                    ],
                    'previous' => $previousData ? [
                        'total_amount' => $previousData->total_amount,
                        'total_product' => $previousData->total_product,
                        'purchase_count' => $previousData->purchase_count,
                        'avg_purchase_value' => $previousData->avg_purchase_value,
                    ] : [
                        'total_amount' => 0,
                        'total_product' => 0,
                        'purchase_count' => 0,
                        'avg_purchase_value' => 0,
                    ],
                ];
                $current->addYear();
            }
        }

        return $comparisonData;
    }

    /**
     * Get purchase orders over time grouped by date/month/year
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  string  $groupBy  'day', 'month', or 'year'
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getPurchaseOrdersOverTime($fromDate, $toDate, $groupBy = 'day', $vendorId = null, $search = '')
    {
        // Use purchase_date if available, otherwise fall back to created_at
        $dateField = DB::raw('COALESCE(purchase_date, DATE(created_at))');

        // Build the period select based on groupBy
        $periodSelect = DB::raw(
            $groupBy === 'day'
            ? 'DATE(COALESCE(purchases.purchase_date, purchases.created_at)) as period'
            : ($groupBy === 'month'
                ? 'DATE_FORMAT(COALESCE(purchases.purchase_date, purchases.created_at), "%Y-%m") as period'
                : 'YEAR(COALESCE(purchases.purchase_date, purchases.created_at)) as period')
        );

        // Count distinct products from purchase_details
        // Use a subquery to count distinct products per period, then join with purchase aggregations
        $productCountSubquery = PurchaseDetail::select(
            DB::raw($groupBy === 'day'
                ? 'DATE(COALESCE(purchases.purchase_date, DATE(purchases.created_at))) as period'
                : ($groupBy === 'month'
                    ? 'DATE_FORMAT(COALESCE(purchases.purchase_date, purchases.created_at), "%Y-%m") as period'
                    : 'YEAR(COALESCE(purchases.purchase_date, purchases.created_at)) as period')),
            DB::raw('COUNT(DISTINCT purchase_details.product_id) as total_product')
        )
            ->join('purchases', 'purchase_details.purchase_id', '=', 'purchases.id')
            ->whereBetween(DB::raw('COALESCE(purchases.purchase_date, DATE(purchases.created_at))'), [$fromDate, $toDate])
            ->groupBy('period');

        if ($vendorId !== null && $vendorId !== '') {
            $productCountSubquery->where('purchases.vendor_id', $vendorId);
        }

        $query = Purchase::select(
            $periodSelect,
            DB::raw('SUM(purchases.total_amount) as total_amount'),
            DB::raw('COUNT(purchases.id) as purchase_count'),
            DB::raw('COALESCE(product_counts.total_product, 0) as total_product')
        )
            ->leftJoinSub($productCountSubquery, 'product_counts', function ($join) use ($groupBy) {
                $join->on(
                    DB::raw($groupBy === 'day'
                        ? 'DATE(COALESCE(purchases.purchase_date, DATE(purchases.created_at)))'
                        : ($groupBy === 'month'
                            ? 'DATE_FORMAT(COALESCE(purchases.purchase_date, purchases.created_at), "%Y-%m")'
                            : 'YEAR(COALESCE(purchases.purchase_date, purchases.created_at))')),
                    '=',
                    'product_counts.period'
                );
            })
            ->whereBetween(DB::raw('COALESCE(purchases.purchase_date, DATE(purchases.created_at))'), [$fromDate, $toDate]);

        // Filter by vendor if provided
        if ($vendorId !== null && $vendorId !== '') {
            $query->where('purchases.vendor_id', $vendorId);
        }

        // Filter by search term if provided (search in reference)
        if (! empty($search)) {
            $query->where('purchases.reference', 'LIKE', '%' . $search . '%');
        }

        $results = $query->groupBy('period')
            ->orderBy('period', 'desc')
            ->get();

        // Calculate average purchase value and format period
        return $results->map(function ($item) {
            $item->period = is_numeric($item->period) ? $item->period : $item->period;
            $item->avg_purchase_value = $item->purchase_count > 0
                ? ($item->total_amount / $item->purchase_count)
                : 0;

            return $item;
        });
    }
}
