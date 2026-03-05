<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\OrderDetail;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class TotalSalesByProductReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Total Sales By Product report data
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  int  $perPage
     * @param  int  $page
     * @param  string  $search
     * @param  string|null  $sortColumn
     * @param  string|null  $sortDirection
     * @return array
     */
    public function generate($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null)
    {
        // Get paginated product sales data
        $paginator = $this->getTotalSalesByProduct($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection);

        // Get totals (for filtered/search results, not just current page)
        $totalsData = $this->getTotalSalesByProductTotals($fromDate, $toDate, $vendorId, $search);
        $totalSales = $totalsData->sum('total_sales');
        $totalQuantity = $totalsData->sum('quantity_sold');
        $totalTax = $totalsData->sum('total_tax');
        $totalOrders = $totalsData->sum('order_count');
        $avgSalesValue = $totalQuantity > 0 ? ($totalSales / $totalQuantity) : 0;

        // Convert paginated items to simple array format for display
        $productData = collect($paginator->items())->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'vendor_id' => $item->vendor_id,
                'product_name' => $item->product_name,
                'vendor_name' => $item->vendor_name ?? __('N/A'),
                'total_sales' => $item->total_sales,
                'total_tax' => $item->total_tax ?? 0,
                'quantity_sold' => $item->quantity_sold,
                'order_count' => $item->order_count,
                'avg_unit_price' => $item->avg_unit_price,
            ];
        })->values()->all();

        return [
            'productData' => $productData,
            'paginator' => $paginator,
            'totalSales' => $totalSales,
            'totalQuantity' => $totalQuantity,
            'totalTax' => $totalTax,
            'totalOrders' => $totalOrders,
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
            'avgSalesValue' => $avgSalesValue,
        ];
    }

    /**
     * Get total sales by product
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
        $query = OrderDetail::select(
            'order_details.product_id',
            'order_details.vendor_id',
            DB::raw('MAX(order_details.product_name) as product_name'),
            DB::raw('MAX(vendors.name) as vendor_name'),
            DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'),
            DB::raw('SUM(order_details.quantity) as quantity_sold'),
            DB::raw('SUM(COALESCE(order_details.tax_charge, 0)) as total_tax'),
            DB::raw('COUNT(DISTINCT order_details.order_id) as order_count')
        )
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('vendors', 'order_details.vendor_id', '=', 'vendors.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereNotNull('order_details.product_id')
            ->groupBy('order_details.product_id', 'order_details.vendor_id');

        // Map column names for sorting
        $sortableColumns = [
            'product_name' => 'product_name',
            'vendor_name' => 'vendor_name',
            'quantity_sold' => 'quantity_sold',
            'order_count' => 'order_count',
            'total_sales' => 'total_sales',
            'total_tax' => 'total_tax',
            'avg_unit_price' => DB::raw('(SUM(order_details.price * order_details.quantity) / SUM(order_details.quantity))'),
        ];

        // Apply sorting
        if ($sortColumn && isset($sortableColumns[$sortColumn])) {
            $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        } else {
            // Default sorting by total_sales desc
            $query->orderBy('total_sales', 'desc');
        }

        // Filter by vendor if provided
        if ($vendorId !== null && $vendorId !== '') {
            $query->where('order_details.vendor_id', $vendorId);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_details.product_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendors.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform items to include avg_unit_price
        $paginator->getCollection()->transform(function ($item) {
            $item->avg_unit_price = $item->quantity_sold > 0
                ? ($item->total_sales / $item->quantity_sold)
                : 0;
            $item->vendor_name = $item->vendor_name ?? __('N/A');

            return $item;
        });

        return $paginator;
    }

    /**
     * Get total sales by product (non-paginated - for totals calculation)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSalesByProductTotals($fromDate, $toDate, $vendorId = null, $search = '')
    {
        $query = OrderDetail::select(
            'order_details.product_id',
            'order_details.vendor_id',
            DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'),
            DB::raw('SUM(order_details.quantity) as quantity_sold'),
            DB::raw('SUM(COALESCE(order_details.tax_charge, 0)) as total_tax'),
            DB::raw('COUNT(DISTINCT order_details.order_id) as order_count')
        )
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('vendors', 'order_details.vendor_id', '=', 'vendors.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereNotNull('order_details.product_id');

        // Filter by vendor if provided
        if ($vendorId !== null && $vendorId !== '') {
            $query->where('order_details.vendor_id', $vendorId);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_details.product_name', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendors.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Group by product_id and vendor_id for totals (to match the paginated query structure)
        $query->groupBy('order_details.product_id', 'order_details.vendor_id');

        return $query->get();
    }
}
