<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 *
 * @created 14-10-2025
 */

namespace App\DataTables;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class AdminCustomerDataTable extends DataTable
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
            ->editColumn('vendor_id', function ($customer) {
                return '<a href="' . route('vendors.edit', $customer->vendor_id) . '">' . wrapIt($customer->vendor?->name, 10, ['columns' => 2]) . '</a>';

            })
            ->editColumn('name', function ($customer) {
                return '<a href="' . route('customers.edit', $customer->id) . '">' . wrapIt($customer->name, 10, ['columns' => 2]) . '</a>';

            })
            ->editColumn('email', function ($customer) {
                return $customer->email;
            })
            ->editColumn('phone', function ($customer) {
                return $customer->phone;
            })
            ->addColumn('action', function ($customer) {
                $str = '';
                if (auth()->user()?->hasPermission('App\Http\Controllers\CustomerController@edit')) {
                    $str .= '<a title="' . __('Edit') . '" href="' . route('customers.edit', $customer->id) . '" class="action-icon"><i class="feather icon-edit-1"></i></a>&nbsp;';
                }
                if (auth()->user()?->hasPermission('App\Http\Controllers\CustomerController@destroy') && $customer->email != 'walkingcustomer@gmail.com') {
                    $str .= view('components.backend.datatable.delete-button', [
                        'route' => route('customers.destroy', $customer->id),
                        'id' => $customer->id,
                        'method' => 'DELETE',
                    ])->render();
                }

                return $str;
            })
            ->rawColumns(['vendor_id', 'name', 'email', 'action'])
            ->make(true);
    }

    /*
    * DataTable Query
    *
    * @return mixed
    */
    public function query()
    {
        $customers = Customer::with('vendor:id,name')
            ->select('customers.id', 'customers.name', 'customers.email', 'customers.phone', 'customers.vendor_id')
            ->filter();

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
            ->addColumn(['data' => 'vendor_id', 'name' => 'vendor.name', 'title' => __('Vendor')])
            ->addColumn(['data' => 'name', 'name' => 'customers.name', 'title' => __('Customer')])
            ->addColumn(['data' => 'phone', 'name' => 'customers.phone', 'title' => __('Phone')])
            ->addColumn(['data' => 'email', 'name' => 'customers.email', 'title' => __('Email')])
            ->addColumn([
                'data' => 'action',
                'name' => 'action',
                'title' => '',
                'width' => '10%',
                'visible' => auth()->user()?->hasAnyPermission(['App\Http\Controllers\CustomerController@edit', 'App\Http\Controllers\CustomerController@destroy']),
                'orderable' => false,
                'searchable' => false,
                'className' => 'text-right align-middle',
            ])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip', 'order' => [0]]));
    }
}
