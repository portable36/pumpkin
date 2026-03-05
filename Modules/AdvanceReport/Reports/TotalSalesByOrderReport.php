<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use App\Services\Order\InvoiceService;
use Illuminate\Support\Facades\DB;

class TotalSalesByOrderReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Total Sales By Order report data
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  int  $perPage
     * @param  int  $page
     * @param  string  $search
     * @param  string|null  $sortColumn
     * @param  string|null  $sortDirection
     * @param  string|null  $paymentStatus
     * @param  string|null  $orderStatus
     * @param  string|null  $channel
     * @return array
     */
    public function generate($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null, $paymentStatus = null, $orderStatus = null, $channel = null)
    {
        // Get paginated order data
        $paginator = $this->getTotalSalesByOrder($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection, $paymentStatus, $orderStatus, $channel);

        // Get totals (for filtered/search results, not just current page)
        $totalsData = $this->getTotalSalesByOrderTotals($fromDate, $toDate, $vendorId, $search, $paymentStatus, $orderStatus, $channel);
        $totalAmount = $totalsData->sum('order_total');
        $totalPaid = $totalsData->sum('total_paid');
        $totalQuantity = $totalsData->sum('total_quantity');
        $totalOrders = $totalsData->count();

        // Convert paginated items to simple array format for display
        $orderData = collect($paginator->items())->map(function ($item) {
            return [
                'order_id' => $item->order_id,
                'order_date' => $item->order_date,
                'order_reference' => $item->order_reference ?? __('N/A'),
                'customer_name' => $item->customer_name ?? __('N/A'),
                'total_quantity' => $item->total_quantity ?? 0,
                'order_total' => $item->order_total ?? 0,
                'order_paid' => $item->order_paid ?? 0,
                'channel' => $item->channel ?? __('N/A'),
                'payment_status' => $item->payment_status ?? __('N/A'),
                'order_status' => $item->order_status ?? __('N/A'),
            ];
        })->values()->all();

        return [
            'orderData' => $orderData,
            'paginator' => $paginator,
            'totalAmount' => $totalAmount,
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
     * Get total sales by order
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  int  $perPage
     * @param  int  $page
     * @param  string  $search
     * @param  string|null  $sortColumn
     * @param  string|null  $sortDirection
     * @param  string|null  $paymentStatus
     * @param  string|null  $orderStatus
     * @param  string|null  $channel
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getTotalSalesByOrder($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null, $paymentStatus = null, $orderStatus = null, $channel = null)
    {
        // Filter by vendor if provided - join with order_details
        if ($vendorId !== null && $vendorId !== '') {
            $query = Order::select(
                'orders.id as order_id',
                'orders.reference as order_reference',
                'orders.order_date',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.payment_status',
                'orders.order_status_id',
                'orders.channel',
                DB::raw('COALESCE(MAX(customers.name), MAX(users.name)) as customer_name'),
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('order_details.vendor_id', $vendorId)
                ->groupBy('orders.id', 'orders.reference', 'orders.order_date', 'orders.total', 'orders.paid', 'orders.payment_status', 'orders.order_status_id', 'orders.channel');
        } else {
            $query = Order::select(
                'orders.id as order_id',
                'orders.reference as order_reference',
                'orders.order_date',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.payment_status',
                'orders.order_status_id',
                'orders.channel',
                DB::raw('COALESCE(MAX(customers.name), MAX(users.name)) as customer_name'),
                DB::raw('(SELECT SUM(quantity) FROM order_details WHERE order_details.order_id = orders.id) as total_quantity')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->groupBy('orders.id', 'orders.reference', 'orders.order_date', 'orders.total', 'orders.paid', 'orders.payment_status', 'orders.order_status_id', 'orders.channel');
        }

        // Filter by payment status if provided
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->where('orders.payment_status', $paymentStatus);
        }

        // Filter by order status if provided
        if ($orderStatus !== null && $orderStatus !== '') {
            $query->where('orders.order_status_id', $orderStatus);
        }

        // Filter by channel if provided
        if ($channel !== null && $channel !== '') {
            $query->where('orders.channel', $channel);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.reference', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Map column names for sorting
        $sortableColumns = [
            'order_date' => 'orders.order_date',
            'order_reference' => 'orders.reference',
            'customer_name' => 'customer_name',
            'total_quantity' => 'total_quantity',
            'order_total' => 'orders.total',
            'order_paid' => 'orders.paid',
            'channel' => 'orders.channel',
            'payment_status' => 'orders.payment_status',
            'order_status' => 'orders.order_status_id',
        ];

        // Apply sorting
        if ($sortColumn && isset($sortableColumns[$sortColumn])) {
            $sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'desc';
            $query->orderBy($sortableColumns[$sortColumn], $sortDirection);
        } else {
            // Default sorting by order_date desc
            $query->orderBy('orders.order_date', 'desc');
        }

        // Get paginated results
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        // Eager load all orders with relations in one query to prevent N+1 queries
        $items = $paginator->getCollection();
        $orderIds = $items->pluck('order_id')->unique()->filter();
        
        // Eager load orders with relations needed for orderStatus, vendorProductPrice, vendorProductShippingTax, and InvoiceService::totalCustomAmount
        $relations = ['orderStatus', 'metadata'];
        if (isActive('Coupon')) {
            $relations[] = 'couponRedeems.coupon';
        }
        
        $ordersById = Order::whereIn('id', $orderIds)
            ->with($relations)
            ->get()
            ->keyBy('id');

        // Transform items
        $paginator->getCollection()->transform(function ($item) use ($vendorId, $ordersById) {
            $item->order_reference = $item->order_reference ?? __('N/A');
            $item->customer_name = $item->customer_name ?? __('N/A');
            $item->payment_status = $item->payment_status ?? __('N/A');
            $item->channel = ucfirst($item->channel ?? __('N/A'));

            // Get order status name
            $order = $ordersById[$item->order_id] ?? null;
            if ($order && $order->orderStatus) {
                $item->order_status = $order->orderStatus->name;
            } else {
                $item->order_status = __('N/A');
            }

            // Calculate vendor-specific order total if vendor is filtered
            if ($vendorId !== null && $vendorId !== '') {
                if ($order) {
                    if (in_array($order->channel, ['invoice', 'pos'])) {
                        $item->order_total = $order->total + InvoiceService::totalCustomAmount($order);
                    } else {
                        $item->order_total = $order->vendorProductPrice($vendorId, $order->id)
                            + $order->vendorProductShippingTax($vendorId, $order->id)
                            + InvoiceService::totalCustomAmount($order, true);
                    }
                }
            }

            return $item;
        });

        return $paginator;
    }

    /**
     * Get total sales by order (non-paginated - for totals calculation)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @param  string|null  $paymentStatus
     * @param  string|null  $orderStatus
     * @param  string|null  $channel
     * @return \Illuminate\Support\Collection
     */
    public function getTotalSalesByOrderTotals($fromDate, $toDate, $vendorId = null, $search = '', $paymentStatus = null, $orderStatus = null, $channel = null)
    {
        // Filter by vendor if provided - join with order_details
        if ($vendorId !== null && $vendorId !== '') {
            $query = Order::select(
                'orders.id as order_id',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.paid as total_paid',
                'orders.channel',
                DB::raw('SUM(order_details.quantity) as total_quantity')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('order_details.vendor_id', $vendorId);
        } else {
            $query = Order::select(
                'orders.id as order_id',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.paid as total_paid',
                'orders.channel',
                DB::raw('(SELECT SUM(quantity) FROM order_details WHERE order_details.order_id = orders.id) as total_quantity')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate]);
        }

        // Filter by payment status if provided
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->where('orders.payment_status', $paymentStatus);
        }

        // Filter by order status if provided
        if ($orderStatus !== null && $orderStatus !== '') {
            $query->where('orders.order_status_id', $orderStatus);
        }

        // Filter by channel if provided
        if ($channel !== null && $channel !== '') {
            $query->where('orders.channel', $channel);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.reference', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%');
            });
        }

        // Group by order_id for totals
        // When vendor is filtered, groupBy ensures each order appears only once
        if ($vendorId !== null && $vendorId !== '') {
            $query->groupBy('orders.id', 'orders.total', 'orders.paid', 'orders.channel');
        } else {
            $query->groupBy('orders.id', 'orders.total', 'orders.paid', 'orders.channel');
        }

        $results = $query->get();

        // Calculate vendor-specific order totals if vendor is filtered
        if ($vendorId !== null && $vendorId !== '') {
            // Eager load all orders in one query to prevent N+1 queries
            $orderIds = $results->pluck('order_id')->unique()->filter();
            $ordersById = Order::whereIn('id', $orderIds)
                ->get()
                ->keyBy('id');

            $results->transform(function ($item) use ($vendorId, $ordersById) {
                $order = $ordersById[$item->order_id] ?? null;
                if ($order) {
                    if (in_array($order->channel, ['invoice', 'pos'])) {
                        $item->order_total = $order->total + InvoiceService::totalCustomAmount($order);
                    } else {
                        $item->order_total = $order->vendorProductPrice($vendorId, $order->id)
                            + $order->vendorProductShippingTax($vendorId, $order->id)
                            + InvoiceService::totalCustomAmount($order, true);
                    }
                    // Keep total_paid as orders.paid (actual paid amount from database)
                    $item->total_paid = $item->order_paid;
                }

                return $item;
            });
        }

        return $results;
    }
}
