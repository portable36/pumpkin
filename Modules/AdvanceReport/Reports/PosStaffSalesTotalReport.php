<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use Illuminate\Support\Facades\DB;

class PosStaffSalesTotalReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate POS Staff Sales Total report data
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
        // Check if POS module is active
        if (! isActive('Pos')) {
            return [
                'message' => __('You need to install and active Pos module to see report'),
            ];
        }

        // Get paginated staff sales data
        $paginator = $this->getPosStaffSalesTotal($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection);

        // Get totals (for filtered/search results, not just current page)
        $totalsData = $this->getPosStaffSalesTotalTotals($fromDate, $toDate, $vendorId, $search);
        $totalSales = $totalsData->sum('total_sales');
        $totalQuantity = $totalsData->sum('total_quantity');
        $totalOrders = $totalsData->sum('order_count');
        $avgSales = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        // Convert paginated items to simple array format for display
        $staffData = collect($paginator->items())->map(function ($item) {
            return [
                'staff_id' => $item->staff_id,
                'staff_name' => $item->staff_name ?? __('N/A'),
                'total_orders' => $item->order_count ?? 0,
                'average_sales' => $item->average_sales ?? 0,
                'total_quantity' => $item->total_quantity ?? 0,
                'total_sales' => $item->total_sales ?? 0,
            ];
        })->values()->all();

        return [
            'staffData' => $staffData,
            'paginator' => $paginator,
            'totalSales' => $totalSales,
            'totalQuantity' => $totalQuantity,
            'totalOrders' => $totalOrders,
            'averageSales' => $avgSales,
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Get POS staff sales total
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
    public function getPosStaffSalesTotal($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null)
    {
        // Exclude vendor-admin users, only include staff (vendor-staff)
        if ($vendorId !== null && $vendorId !== '') {
            // When vendor is filtered, calculate vendor-specific totals from order_details
            $query = Order::select(
                'orders.staff_id',
                DB::raw('MAX(users.name) as staff_name'),
                DB::raw('SUM(order_details.price * order_details.quantity + COALESCE(order_details.shipping_charge, 0) + COALESCE(order_details.tax_charge, 0)) as total_sales'),
                DB::raw('SUM(order_details.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(order_details.price * order_details.quantity + COALESCE(order_details.shipping_charge, 0) + COALESCE(order_details.tax_charge, 0)) as average_sales')
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
                ->groupBy('orders.staff_id');
        } else {
            // When no vendor filter, use order totals
            $query = Order::select(
                'orders.staff_id',
                DB::raw('MAX(users.name) as staff_name'),
                DB::raw('SUM(orders.total) as total_sales'),
                DB::raw('SUM(orders.total_quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT orders.id) as order_count'),
                DB::raw('AVG(orders.total) as average_sales')
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
                ->groupBy('orders.staff_id');
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Map column names for sorting
        $sortableColumns = [
            'staff_name' => 'staff_name',
            'total_orders' => 'order_count',
            'average_sales' => 'average_sales',
            'total_quantity' => 'total_quantity',
            'total_sales' => 'total_sales',
        ];

        // Apply sorting
        if ($sortColumn && isset($sortableColumns[$sortColumn])) {
            $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        } else {
            // Default sorting by total_sales desc
            $query->orderBy('total_sales', 'desc');
        }

        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        return $paginator;
    }

    /**
     * Get POS staff sales total (non-paginated - for totals calculation)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @return \Illuminate\Support\Collection
     */
    public function getPosStaffSalesTotalTotals($fromDate, $toDate, $vendorId = null, $search = '')
    {
        // Exclude vendor-admin users, only include staff (vendor-staff)
        if ($vendorId !== null && $vendorId !== '') {
            // When vendor is filtered, calculate vendor-specific totals from order_details
            $query = Order::select(
                'orders.staff_id',
                DB::raw('SUM(order_details.price * order_details.quantity + COALESCE(order_details.shipping_charge, 0) + COALESCE(order_details.tax_charge, 0)) as total_sales'),
                DB::raw('SUM(order_details.quantity) as total_quantity'),
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
                ->groupBy('orders.staff_id');
        } else {
            // When no vendor filter, use order totals
            $query = Order::select(
                'orders.staff_id',
                DB::raw('SUM(orders.total) as total_sales'),
                DB::raw('SUM(orders.total_quantity) as total_quantity'),
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
                ->groupBy('orders.staff_id');
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('users.name', 'LIKE', '%' . $search . '%');
            });
        }

        return $query->get();
    }
}
