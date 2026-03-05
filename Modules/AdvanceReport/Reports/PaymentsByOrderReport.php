<?php

namespace Modules\AdvanceReport\Reports;

use App\Models\Order;
use Modules\AdvanceReport\Reports\Contracts\ReportInterface;
use Modules\AdvanceReport\Services\Traits\ReportHelperTrait;
use App\Services\Order\InvoiceService;
use Illuminate\Support\Facades\DB;

class PaymentsByOrderReport implements ReportInterface
{
    use ReportHelperTrait;

    /**
     * Generate Payments By Order report data
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
     * @return array
     */
    public function generate($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null, $paymentStatus = null)
    {
        // Get paginated payment data
        $paginator = $this->getPaymentsByOrder($fromDate, $toDate, $vendorId, $perPage, $page, $search, $sortColumn, $sortDirection, $paymentStatus);

        // Get totals (for filtered/search results, not just current page)
        $totalsData = $this->getPaymentsByOrderTotals($fromDate, $toDate, $vendorId, $search, $paymentStatus);
        $totalAmount = $totalsData->sum('order_total');
        $totalPaid = $totalsData->sum('total_paid');
        $totalOrders = $totalsData->count();

        // Convert paginated items to simple array format for display
        $paymentData = collect($paginator->items())->map(function ($item) {
            return [
                'order_id' => $item->order_id,
                'order_reference' => $item->order_reference ?? __('N/A'),
                'order_date' => $item->order_date,
                'customer_name' => $item->customer_name ?? __('N/A'),
                'customer_email' => $item->customer_email ?? __('N/A'),
                'order_total' => $item->order_total ?? 0,
                'order_paid' => $item->order_paid ?? 0,
                'payment_status' => $item->payment_status ?? __('N/A'),
            ];
        })->values()->all();

        return [
            'paymentData' => $paymentData,
            'paginator' => $paginator,
            'totalAmount' => $totalAmount,
            'totalPaid' => $totalPaid,
            'totalOrders' => $totalOrders,
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'perPage' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }

    /**
     * Get payments by order
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaymentsByOrder($fromDate, $toDate, $vendorId = null, $perPage = 25, $page = 1, $search = '', $sortColumn = null, $sortDirection = null, $paymentStatus = null)
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
                'orders.channel',
                DB::raw('COALESCE(MAX(customers.name), MAX(users.name)) as customer_name'),
                DB::raw('COALESCE(MAX(customers.email), MAX(users.email)) as customer_email')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->join('order_details', 'orders.id', '=', 'order_details.order_id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->where('order_details.vendor_id', $vendorId)
                ->groupBy('orders.id', 'orders.reference', 'orders.order_date', 'orders.total', 'orders.paid', 'orders.payment_status', 'orders.channel');
        } else {
            $query = Order::select(
                'orders.id as order_id',
                'orders.reference as order_reference',
                'orders.order_date',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.payment_status',
                'orders.channel',
                DB::raw('COALESCE(MAX(customers.name), MAX(users.name)) as customer_name'),
                DB::raw('COALESCE(MAX(customers.email), MAX(users.email)) as customer_email')
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate])
                ->groupBy('orders.id', 'orders.reference', 'orders.order_date', 'orders.total', 'orders.paid', 'orders.payment_status', 'orders.channel');
        }
        // Filter by payment status if provided
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->where('orders.payment_status', $paymentStatus);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.reference', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $search . '%');
            });
        }

        // Map column names for sorting
        $sortableColumns = [
            'order_reference' => 'orders.reference',
            'order_date' => 'orders.order_date',
            'customer_name' => 'customer_name',
            'order_total' => 'orders.total',
            'order_paid' => 'orders.paid',
            'payment_status' => 'orders.payment_status',
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

        // Transform items
        $paginator->getCollection()->transform(function ($item) use ($vendorId) {
            $item->order_reference = $item->order_reference ?? __('N/A');
            $item->customer_name = $item->customer_name ?? __('N/A');
            $item->customer_email = $item->customer_email ?? __('N/A');
            $item->payment_status = $item->payment_status ?? __('N/A');

            // Calculate vendor-specific order total if vendor is filtered
            if ($vendorId !== null && $vendorId !== '') {
                $order = Order::find($item->order_id);
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
     * Get payments by order (non-paginated - for totals calculation)
     *
     * @param  string  $fromDate
     * @param  string  $toDate
     * @param  int|null  $vendorId
     * @param  string  $search
     * @param  string|null  $paymentStatus
     * @return \Illuminate\Support\Collection
     */
    public function getPaymentsByOrderTotals($fromDate, $toDate, $vendorId = null, $search = '', $paymentStatus = null)
    {
        // Filter by vendor if provided - join with order_details
        if ($vendorId !== null && $vendorId !== '') {
            $query = Order::select(
                'orders.id as order_id',
                'orders.total as order_total',
                'orders.paid as order_paid',
                'orders.paid as total_paid',
                'orders.channel'
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
                'orders.channel'
            )
                ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
                ->leftJoin('users', 'orders.user_id', '=', 'users.id')
                ->whereBetween('orders.order_date', [$fromDate, $toDate]);
        }

        // Filter by payment status if provided
        if ($paymentStatus !== null && $paymentStatus !== '') {
            $query->where('orders.payment_status', $paymentStatus);
        }

        // Filter by search term if provided
        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('orders.reference', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.email', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('users.email', 'LIKE', '%' . $search . '%');
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
            $results->transform(function ($item) use ($vendorId) {
                $order = Order::find($item->order_id);
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
