<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Md. Mostafijur Rahman <[mostafijur.techvill@gmail.com]>
 *
 * @created 26-10-2023
 */

namespace App\DataTables\Vendor;

use App\DataTables\DataTable;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CustomerLedgerDataTable extends DataTable
{
    /**
     * Handle the AJAX request for attribute groups.
     *
     * This function queries attribute groups and returns the data in a format suitable
     * for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $order = $this->query();
        $from = $this->from;

        return datatables()
            ->of($order)
            ->editColumn('reference', function ($order) use ($from) {
                return '<a href="' . route($from == 'vendor' ? 'vendorOrder.view' : 'order.view', ['id' => $order->id]) . '">' . $order->reference . '</a>';
            })
            ->editColumn('created_at', function ($order) {
                return timeZoneFormatDate($order->created_at);
            })
            ->editColumn('paid_amount', function ($order) {
                return formatNumber($order->paid_amount);
            })
            ->editColumn('order_amount', function ($order) {
                if ($order->description == 'web') {
                    $vendorId = session('vendorId') ?: auth()->user()->vendor()->vendor_id;
                    $orderModel = new Order();

                    return formatNumber($orderModel->vendorProductPrice($vendorId, $order->id) + $orderModel->vendorProductShippingTax($vendorId, $order->id));
                }

                return formatNumber($order->order_amount);
            })
            ->editColumn('description', function ($order) {
                return str_replace('_', ' ', $order->description);
            })
            ->editColumn('status', function ($order) {
                return '<span class="f-w-600 f-12 text-muted text-uppercase">' . $order->status . '</span>';
            })
            ->rawColumns(['reference', 'created_at', 'description', 'paid_amount', 'order_amount', 'balance', 'status'])
            ->filter(function ($instance) {
                if (isset(request('search')['value'])) {
                    $keyword = xss_clean(request('search')['value']);
                    if (! empty($keyword)) {
                        $instance->where(function ($query) use ($keyword) {
                            $description = str_replace(' ', '_', $keyword);
                            $query->where('reference', 'like', "%{$keyword}%")
                                ->orWhere('description', 'like', "%{$description}%")
                                ->orWhere('status', 'like', "%{$keyword}%");
                        });
                    }
                }
            })
            ->make(true);
    }

    /*
    * DataTable Query
    *
    * @return mixed
    */
    public function query()
    {
        $from = $this->from;
        $customerId = request()->id;
        $vendorId = null;

        if ($from !== 'admin') {
            $vendorId = session('vendorId') ?: auth()->user()->vendor()->vendor_id;
        }

        // --- Common base queries ---
        $orders = Order::join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->select(
                'orders.id',
                'orders.reference',
                'order_statuses.name as status',
                'orders.channel as description',
                DB::raw('0 as paid_amount'),
                'orders.total as order_amount',
                'orders.order_date as created_at'
            )
            ->where('orders.customer_id', $customerId)
            ->where('orders.channel', '!=', 'web');

        $transactions = Transaction::join('orders', 'transactions.order_id', '=', 'orders.id')
            ->join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->select(
                'orders.id',
                'orders.reference',
                'order_statuses.name as status',
                'transactions.transaction_type as description',
                'transactions.paid_amount',
                DB::raw('0 as order_amount'),
                'transactions.transaction_date as created_at'
            )
            ->where('orders.customer_id', $customerId)
            ->where('orders.channel', '!=', 'web')
            ->whereIn('transactions.transaction_type', ['Order_actual_price', 'Order_partial_payment', 'Order_payment']);

        // --- Apply vendor filter only for vendor view ---
        if ($from !== 'admin') {
            $orders->whereHas('orderDetails', fn ($q) => $q->where('vendor_id', $vendorId));
            $transactions->where('transactions.vendor_id', $vendorId);
        }

        // Wrap union in a subquery
        $union = $orders->unionAll($transactions);

        return DB::query()
            ->fromSub($union, 'ledger')
            ->orderBy('created_at', 'desc')
            ->orderBy('reference', 'desc')
            ->orderBy('order_amount');
    }

    /*
    * DataTable HTML
    *
    * @return \Yajra\DataTables\Html\Builder
    */
    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('Id'), 'visible' => false, 'searchable' => false])
            ->addColumn(['data' => 'reference', 'name' => 'reference', 'title' => __('Reference'), 'orderable' => false])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => __('Date'), 'orderable' => false])
            ->addColumn(['data' => 'description', 'name' => 'description', 'title' => __('Description'), 'orderable' => false, 'searchable' => false])
            ->addColumn(['data' => 'paid_amount', 'name' => 'paid_amount', 'title' => __('Paid Amount'), 'orderable' => false, 'width' => '20%'])
            ->addColumn(['data' => 'order_amount', 'name' => 'order_amount', 'title' => __('Order Amount'), 'orderable' => false])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip', 'order' => [4]]));
    }
}
