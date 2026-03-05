<?php

namespace Modules\Delivery\DataTables;

use App\DataTables\DataTable;
use App\Models\{
    Order,
    OrderStatus
};
use Illuminate\Http\JsonResponse;

class AssignDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $carriers = $this->query();

        return datatables()
            ->of($carriers)
            ->editColumn('id', function ($carrier) {
                return $carrier->id;
            })
            ->editColumn('reference', function ($carrier) {
                return $carrier->reference;
            })
            ->editColumn('order_date', function ($carrier) {
                return $carrier->order_date;
            })
            ->editColumn('total', function ($carrier) {
                return formatNumber($carrier->total);
            })
            ->editColumn('status', function ($carrier) {
                return statusBadges(optional($carrier->orderStatus)->name);
            })
            ->editColumn('payment_status', function ($carrier) {
                return statusBadges($carrier->payment_status);
            })
            ->editColumn('action', function ($carrier) {
                $view = '<a title="' . __('Show') . '" href="' . route('carrier.show', [$carrier->id]) . '" class="btn btn-xs btn-outline-dark"><i class="feather icon-eye"></i></a>&nbsp';

                $edit = '<a title="' . __('download') . '" href="' . route('carrier.order_print', [$carrier->id, 'type' => 'pdf']) . '" class="btn btn-xs btn-primary"><i class="feather icon-download"></i></a>&nbsp';

                return $view . $edit;
            })
            ->rawColumns(['id', 'reference', 'order_date', 'total', 'status', 'payment_status', 'action'])
            ->make(true);
    }

    public function query()
    {
        $orderStatusId = OrderStatus::getAll()->where('slug', 'assigned')->sortBy('order_by')->pluck('id')->first();
        $orders = Order::whereHas('deliveryMens', function ($query) {
            $query->where('delivery_man_id', auth()?->user()?->deliveryMan?->id);
        })->where('order_status_id', $orderStatusId)->with('orderStatus:id,name')->filter();

        return $this->applyScopes($orders);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'visible' => false, 'searchable' => false])
            ->addColumn(['data' => 'reference', 'name' => 'reference', 'title' => __('Order Code'), 'orderable' => false, 'searchable' => true])
            ->addColumn(['data' => 'order_date', 'name' => 'order_date', 'title' => __('Order Date'), 'searchable' => true])
            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Amount'), 'orderable' => true])
            ->addColumn(['data' => 'status', 'name' => 'orderStatus.name', 'title' => __('Status'), 'orderable' => true])
            ->addColumn(['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('Payment status'), 'orderable' => false, 'searchable' => false, 'width' => '20%'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'visible' => true, 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions(dataTableOptions(['dom' => 'Bfrtip'])));
    }
}
