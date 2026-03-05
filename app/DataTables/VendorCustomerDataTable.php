<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 *
 * @created 14-10-2025
 */

namespace App\DataTables;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class VendorCustomerDataTable extends DataTable
{
    /**
     * Handle the AJAX request for attribute groups.
     *
     * This function queries attribute groups and returns the data in a format suitable
     * for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $customer = $this->query();

        return datatables()
            ->of($customer)
            ->editColumn('name', function ($customer) {
                return '<a href="' . route('vendor.customer.edit', ['customer' => $customer->id]) . '">' . wrapIt($customer->name, 10, ['columns' => 2]) . '</a>';

            })
            ->editColumn('email', function ($customer) {
                return $customer->email;

            })
            ->editColumn('phone', function ($customer) {
                return $customer->phone;
            })
            ->editColumn('total_orders', function ($customer) {
                return $customer->total_orders;
            })->addColumn('action', function ($customer) {
                $str = '';
                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\CustomerController@edit')) {
                    $str .= '<a title="' . __('Edit') . '" href="' . route('vendor.customer.edit', $customer->id) . '" class="action-icon"><i class="feather icon-edit-1"></i></a>&nbsp;';
                }
                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\CustomerController@destroy') && $customer->email != 'walkingcustomer@gmail.com') {
                    $str .= view('components.backend.datatable.delete-button', [
                        'route' => route('vendor.customer.destroy', $customer->id),
                        'id' => $customer->id,
                        'method' => 'DELETE',
                    ])->render();
                }

                return $str;
            })
            ->rawColumns(['name', 'email', 'total_orders', 'action'])
            ->filter(function ($instance) {
                if (isset(request('search')['value'])) {
                    $keyword = xss_clean(request('search')['value']);
                    if (! empty($keyword)) {
                        $instance->where(function ($query) use ($keyword) {
                            $query->where('customers.name', 'like', "%{$keyword}%")
                                ->orWhere('customers.email', 'like', "%{$keyword}%");
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
        $vendorId = session('vendorId') ?? auth()->user()->vendor()->vendor_id;

        $customers = DB::table('customers')
            ->leftJoin('orders', 'orders.customer_id', '=', 'customers.id')
            ->leftJoin('order_details', function ($join) use ($vendorId) {
                $join->on('order_details.order_id', '=', 'orders.id')
                    ->where('order_details.vendor_id', $vendorId);
            })
            ->select(
                'customers.id',
                'customers.name',
                'customers.email',
                'customers.phone',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders')
            )
            ->where('customers.vendor_id', $vendorId)
            ->groupBy('customers.name', 'customers.email', 'customers.phone', 'customers.vendor_id');


        return $this->applyScopes($customers);
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
            ->addColumn(['data' => 'name', 'name' => 'customers.name', 'title' => __('Customer')])
            ->addColumn(['data' => 'phone', 'name' => 'customers.phone', 'title' => __('Phone')])
            ->addColumn(['data' => 'email', 'name' => 'customers.email', 'title' => __('Email')])
            ->addColumn(['data' => 'total_orders', 'name' => 'total_orders', 'title' => __('Total Orders'), 'width' => '15%', 'searchable' => false, 'className' => 'align-middle text-center'])
            ->addColumn([
                'data' => 'action',
                'name' => 'action',
                'title' => '',
                'width' => '10%',
                'visible' => auth()->user()?->hasAnyPermission(['App\Http\Controllers\Vendor\CustomerController@edit', 'App\Http\Controllers\Vendor\CustomerController@destroy']),
                'orderable' => false,
                'searchable' => false,
                'className' => 'text-right align-middle',
            ])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip', 'order' => [0]]));
    }
}
