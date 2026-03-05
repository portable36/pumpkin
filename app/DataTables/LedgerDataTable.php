<?php

namespace App\DataTables;

use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LedgerDataTable extends DataTable
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

        return datatables()
            ->of($order)
            ->editColumn('reference', function ($order) {
                return '<a href="' . route('order.view', ['id' => $order->id]) . '">' . $order->reference . '</a>';
            })
            ->editColumn('created_at', function ($order) {
                return timeZoneFormatDate($order->created_at);
            })
            ->editColumn('paid_amount', function ($order) {
                return formatNumber($order->paid_amount);
            })
            ->editColumn('order_amount', function ($order) {
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
        $userId = request()->id;

        $orders = Order::join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->select('orders.id', 'reference', 'order_statuses.name as status', 'channel as description', 'paid as paid_amount', 'total as order_amount', 'order_date as created_at')
            ->where('user_id', $userId);

        return DB::query()
            ->fromSub($orders, 'ledger')
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
