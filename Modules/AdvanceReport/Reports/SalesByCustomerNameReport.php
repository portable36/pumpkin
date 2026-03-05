<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use App\Models\Customer;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class SalesByCustomerNameReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Sales By Customer Name report data
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
        // Get paginated customer sales data
        $paginator = $this->getSalesByCustomerName($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection);

        // Get totals (for filtered/search results, not just current page)
        $totalsData = $this->getSalesByCustomerNameTotals($fromDate, $toDate, $vendorId, $search);
        $totalSales = $totalsData->sum('total_sales');
        $totalOrders = $totalsData->sum('order_count');
        $totalQuantity = $totalsData->sum('total_quantity');
        $totalPaid = $totalsData->sum('total_paid');

        // Convert paginated items to simple array format for display
        $customerData = collect($paginator->items())->map(function ($item) {
            return [
                'customer_id' => $item->customer_id,
                'vendor_id' => $item->vendor_id,
                'customer_name' => $item->customer_name ?? __('N/A'),
                'customer_email' => $item->customer_email ?? __('N/A'),
                'vendor_name' => $item->vendor_name ?? __('N/A'),
                'total_sales' => $item->total_sales,
                'total_paid' => $item->total_paid ?? 0,
                'total_quantity' => $item->total_quantity,
                'order_count' => $item->order_count,
                'avg_order_value' => $item->avg_order_value,
            ];
        })->values()->all();

        return [
            'customerData' => $customerData,
            'paginator' => $paginator,
            'totalSales' => $totalSales,
            'totalPaid' => $totalPaid,
            'totalQuantity' => $totalQuantity,
            'totalOrders' => $totalOrders,
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Get sales by customer name
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
    public function getSalesByCustomerName($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null)
    {
        $query = Order::select(
            'orders.customer_id',
            'order_details.vendor_id',
            DB::raw('MAX(customers.name) as customer_name'),
            DB::raw('MAX(customers.email) as customer_email'),
            DB::raw('MAX(vendors.name) as vendor_name'),
            DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'),
            DB::raw('SUM(order_details.quantity) as total_quantity'),
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(orders.paid) as total_paid')
        )
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('vendors', 'order_details.vendor_id', '=', 'vendors.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereNotNull('orders.customer_id')
            ->where('orders.channel', '!=', 'web')
            ->groupBy('orders.customer_id', 'order_details.vendor_id');

        // Map column names for sorting
        $sortableColumns = [
            'customer_name' => 'customer_name',
            'customer_email' => 'customer_email',
            'vendor_name' => 'vendor_name',
            'total_quantity' => 'total_quantity',
            'order_count' => 'order_count',
            'total_sales' => 'total_sales',
            'total_paid' => 'total_paid',
            'avg_order_value' => DB::raw('(SUM(order_details.price * order_details.quantity) / COUNT(DISTINCT orders.id))'),
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
                $q->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendors.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Transform items to include avg_order_value
        $paginator->getCollection()->transform(function ($item) {
            $item->avg_order_value = $item->order_count > 0
                ? ($item->total_sales / $item->order_count)
                : 0;
            $item->customer_name = $item->customer_name ?? __('N/A');
            $item->customer_email = $item->customer_email ?? __('N/A');
            $item->vendor_name = $item->vendor_name ?? __('N/A');

            return $item;
        });

        return $paginator;
    }

    /**
     * Get sales by customer name (non-paginated - for totals calculation)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getSalesByCustomerNameTotals($fromDate, $toDate, $vendorId = null, $search = '')
    {
        $query = Order::select(
            'orders.customer_id',
            'order_details.vendor_id',
            DB::raw('SUM(order_details.price * order_details.quantity) as total_sales'),
            DB::raw('SUM(order_details.quantity) as total_quantity'),
            DB::raw('COUNT(DISTINCT orders.id) as order_count'),
            DB::raw('SUM(orders.paid) as total_paid')
        )
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->join('order_details', 'orders.id', '=', 'order_details.order_id')
            ->leftJoin('vendors', 'order_details.vendor_id', '=', 'vendors.id')
            ->whereBetween('orders.order_date', [$fromDate, $toDate])
            ->whereNotNull('orders.customer_id')
            ->where('orders.channel', '!=', 'web');

        // Filter by vendor if provided
        if ($vendorId !== null && $vendorId !== '') {
            $query->where('order_details.vendor_id', $vendorId);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('vendors.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Group by customer_id and vendor_id for totals
        $query->groupBy('orders.customer_id', 'order_details.vendor_id');

        return $query->get();
    }
}
