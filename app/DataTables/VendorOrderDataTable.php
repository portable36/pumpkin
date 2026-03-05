<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 *
 * @created 20-01-2022
 */

namespace App\DataTables;

use App\Models\Order;
use App\Services\CustomFieldService;
use App\Services\Order\InvoiceService;
use Illuminate\Http\JsonResponse;

class VendorOrderDataTable extends DataTable
{
    /**
     * Handle the AJAX request for attribute groups.
     *
     * This function queries attribute groups and returns the data in a format suitable
     * for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $totalAmount = 0;
        $orders = $this->query();

        $dt = datatables()
            ->of($orders)

            ->editColumn('customer', function ($orders) {
                if ($orders->user?->id || $orders->customer?->id) {
                    $name = e($orders->user?->name ?? ($orders->customer?->name ?? $orders->customer?->phone));
                    
                    // Make it linkable if it's a customer (not a user)
                    if ($orders->customer?->id && !$orders->user?->id) {
                        $route = route('vendor.customer.edit', ['customer' => $orders->customer->id]);
                        return '<a href="' . $route . '">'
                            . wrapIt($name, 10, ['columns' => 2])
                            . '</a>';
                    }
                    
                    return wrapIt($name, 10, ['columns' => 2]);
                }

                return wrapIt(__('Guest'), 10, ['columns' => 2]);
            })->editColumn('total', function ($orders) {
                if (in_array($orders->channel, ['invoice', 'pos'])) {
                    return formatNumber($orders->total + InvoiceService::totalCustomAmount($orders), optional($orders->currency)->symbol);
                }

                return formatNumber($orders->vendorProductPrice(session()->get('vendorId') ?: auth()->user()->vendor()->vendor_id, $orders->id) + $orders->vendorProductShippingTax(session()->get('vendorId'), $orders->id) + InvoiceService::totalCustomAmount($orders, true), optional($orders->currency)->symbol);
            })->editColumn('total_quantity', function ($orders) {
                return formatCurrencyAmount($orders->getTotalVendorProduct(session()->get('vendorId') ?: auth()->user()->vendor()->vendor_id, $orders->id));
            })->editColumn('reference', function ($orders) {
                return '<a href="' . route('vendorOrder.view', ['id' => $orders->id]) . '">' . $orders->reference . '</a>';
            })->editColumn('status', function ($orders) {
                return '<span class="f-w-600 f-12 text-muted text-uppercase">' . optional($orders->orderStatus)->name . '</span>';
            })->editColumn('order_date', function ($orders) {
                return timeZoneFormatDate($orders->order_date);
            })->editColumn('payment_status', function ($orders) {
                return statusBadges($orders->vendorPaymentStatus());
            });

        CustomFieldService::dataTableBody($dt, 'orders');

        $dt->addColumn('action', function ($orders) {
            $confirmDel = isActive('BulkPayment') ? 'confirm-delete' : '';

            $str = '';

            if (in_array($orders->channel, ['invoice', 'pos']) && auth()->user()?->hasPermission('App\Http\Controllers\Vendor\VendorOrderController@edit') && $orders->total > $orders->amount_received) {
                $str .= '<a title="' . __('Edit') . '" href="' . route('vendorOrder.edit', ['id' => $orders->id]) . '" class="action-icon view-order" data-id=' . $orders->id . ' ><i class="feather icon-edit"></i></a>';
            }
            if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\VendorOrderController@view')) {
                $str .= '<a title="' . __('Show') . '" href="' . route('vendorOrder.view', ['id' => $orders->id]) . '" class="action-icon view-order ' . $confirmDel . '" data-id=' . $orders->id . ' data-payment=' . $orders->vendorPaymentStatus() . '><i class="feather icon-eye"></i></a>';
            }

            return $str;
        })

            ->rawColumns(['customer', 'total', 'total_quantity', 'reference', 'status', 'order_date', 'payment_status', 'action']);

        return $dt->make(true);
    }

    /*
    * DataTable Query
    *
    * @return mixed
    */
    public function query()
    {
        $vendorId = session()->get('vendorId') ?? auth()->user()->vendor()->vendor_id;
        $orders = Order::select('orders.id', 'user_id', 'customer_id', 'reference', 'order_date', 'currency_id', 'other_discount_amount', 'other_discount_type', 'orders.shipping_charge', 'orders.tax_charge', 'total', 'paid', 'total_quantity', 'order_status_id', 'payment_status', 'channel', 'orders.created_at', 'amount_received')
            ->when(isActive('Pos') && version_compare(moduleData('Pos')->get('version'), '2.0', '>='), function ($query) {
                return $query->addSelect('channel');
            })
            ->whereHas('orderDetails', function ($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->orWhere('note', auth()->user()->id)
            ->with('customFieldValues', 'orderDetails:id,product_id,order_id,vendor_id,shop_id,price,quantity,discount_amount,discount_type,order_status_id', 'user:id,name', 'orderStatus:id,slug,name', 'customer')
            ->filter();

        return $this->applyScopes($orders);
    }

    /*
    * DataTable HTML
    *
    * @return \Yajra\DataTables\Html\Builder
    */
    public function html()
    {
        $builder = $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('Id'), 'visible' => false, 'className' => 'text-left align-middle'])

            ->addColumn(['data' => 'reference', 'name' => 'reference', 'title' => __('Invoice'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'customer', 'name' => 'user.name', 'title' => __('Customer'), 'className' => 'align-middle', 'sortable' => false])

            ->addColumn(['data' => 'total_quantity', 'name' => 'total_quantity', 'title' => __('Quantity'), 'orderable' => false, 'className' => 'align-middle'])

            ->addColumn(['data' => 'total', 'name' => 'total', 'title' => __('Total'), 'className' => 'align-middle']);

        if (isActive('Pos') && version_compare(moduleData('Pos')->get('version'), '2.0', '>=')) {
            $builder->addColumn(['data' => 'channel', 'name' => 'channel', 'title' => __('Channel'), 'className' => 'align-middle']);
        }

        $builder->addColumn(['data' => 'status', 'name' => 'orderStatus.name', 'title' => __('Status'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'payment_status', 'name' => 'payment_status', 'title' => __('Payment Status'), 'className' => 'align-middle']);

        CustomFieldService::dataTableHeader($builder, 'orders');

        $builder->addColumn(['data' => 'order_date', 'name' => 'order_date', 'title' => __('Order Date'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'width' => '7%',
                'visible' => auth()->user()?->hasAnyPermission(['App\Http\Controllers\Vendor\VendorOrderController@view']) || auth()->user()?->hasAnyPermission(['App\Http\Controllers\Vendor\VendorOrderController@edit']),
                'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle'])

            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));

        return $builder;
    }

    /**
     * Set View Data
     *
     * @return void
     */
    public function setViewData()
    {
        $statusCounts = Order::join('order_statuses', 'orders.order_status_id', '=', 'order_statuses.id')
            ->whereHas('orderDetails', function ($q) {
                $q->where('vendor_id', session('vendorId') ?: auth()->user()->vendor()->vendor_id);
            })->orWhere('note', auth()->user()->id)
            ->selectRaw('order_statuses.name, COUNT(*) as count')
            ->groupBy('order_statuses.name')
            ->pluck('count', 'order_statuses.name');

        $this->data['groups'] = ['All' => $statusCounts->sum()] + $statusCounts->toArray();
    }
}
