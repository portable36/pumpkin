<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Foisal Ahmed <[foisal.techvill@gmail.com]>
 *
 * @created 14-10-2025
 */

namespace App\DataTables;

use App\Models\CustomerAddress;
use Illuminate\Http\JsonResponse;

class VendorAddressDataTable extends DataTable
{
    /**
     * Handle the AJAX request for addresses.
     */
    public function ajax(): JsonResponse
    {
        $address = $this->query();

        return datatables()
            ->of($address)
            ->editColumn('type_of_place', fn ($address) => ucfirst($address->type_of_place))
            ->editColumn('address_1', fn ($address) => $address->address_1)
            ->editColumn('city', fn ($address) => $address->city)
            ->editColumn('state_name', fn ($address) => $address->state_name)
            ->editColumn('country_name', fn ($address) => $address->country_name)
            ->addColumn('action', function ($address) {
                $str = '';

                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\CustomerAddressController@edit')) {
                    $str .= '<a title="' . __('Edit') . '" href="' . route('vendor.customer.addresses.edit', $address->id) . '" class="action-icon"><i class="feather icon-edit-1"></i></a>&nbsp;';
                }

                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\CustomerAddressController@destroy')) {
                    $str .= view('components.backend.datatable.delete-button', [
                        'route' => route('vendor.customer.addresses.destroy', $address->id),
                        'id' => $address->id,
                        'method' => 'DELETE',
                    ])->render();
                }

                return $str;
            })
            ->rawColumns(['action'])
            ->filter(function ($instance) {
                if (isset(request('search')['value'])) {
                    $keyword = xss_clean(request('search')['value']);
                    if (! empty($keyword)) {
                        $instance->where(function ($query) use ($keyword) {
                            $query->where('customer_addresses.type_of_place', 'like', "%{$keyword}%")
                                ->orWhere('customer_addresses.address_1', 'like', "%{$keyword}%")
                                ->orWhere('geolocale_countries.name', 'like', "%{$keyword}%")
                                ->orWhere('geolocale_divisions.name', 'like', "%{$keyword}%")
                                ->orWhere('customer_addresses.address_2', 'like', "%{$keyword}%");
                        });
                    }
                }
            })
            ->make(true);
    }

    /**
     * DataTable Query
     */
    public function query()
    {
        $vendorId = session('vendorId') ?? auth()->user()->vendor()->vendor_id;

        $address = CustomerAddress::join('geolocale_countries', 'geolocale_countries.code', '=', 'customer_addresses.country') // INNER JOIN
            ->leftJoin('geolocale_divisions', function ($join) {
                $join->on('geolocale_divisions.code', '=', 'customer_addresses.state')
                    ->on('geolocale_divisions.country_id', '=', 'geolocale_countries.id'); // Match by country
            })
            ->select(
                'customer_addresses.id',
                'customer_addresses.type_of_place',
                'customer_addresses.address_1',
                'customer_addresses.city',
                'customer_addresses.state',
                'customer_addresses.zip',
                'customer_addresses.country',
                'geolocale_countries.name as country_name',
                'geolocale_divisions.name as state_name'
            )
            ->where('customer_addresses.vendor_id', $vendorId)
            ->where('customer_addresses.customer_id', request()->customer)
            ->where('customer_addresses.is_default', '!=', 1);

        return $this->applyScopes($address);
    }

    /**
     * DataTable HTML
     */
    public function html()
    {
        return $this->builder()
            ->addColumn([
                'data' => 'id',
                'name' => 'id',
                'title' => __('Id'),
                'visible' => false,
                'searchable' => false,
            ])
            ->addColumn([
                'data' => 'type_of_place',
                'name' => 'customer_addresses.type_of_place',
                'title' => __('Type'),
                'width' => '15%',
            ])
            ->addColumn([
                'data' => 'address_1',
                'name' => 'customer_addresses.address_1',
                'title' => __('Address'),
                'width' => '20%',
            ])
            ->addColumn([
                'data' => 'city',
                'name' => 'customer_addresses.city',
                'title' => __('City'),
                'width' => '15%',
            ])
            ->addColumn([
                'data' => 'state_name',
                'name' => 'state_name',
                'title' => __('State'),
                'width' => '15%',
            ])
            ->addColumn([
                'data' => 'country_name',
                'name' => 'country_name',
                'title' => __('Country'),
                'width' => '15%',
            ])
            ->addColumn([
                'data' => 'zip',
                'name' => 'customer_addresses.zip',
                'title' => __('Postal Code'),
                'width' => '10%',
            ])
            ->addColumn([
                'data' => 'action',
                'name' => 'action',
                'title' => '',
                'width' => '10%',
                'visible' => auth()->user()?->hasAnyPermission([
                    'App\Http\Controllers\Vendor\CustomerAddressController@edit',
                    'App\Http\Controllers\Vendor\CustomerAddressController@destroy',
                ]),
                'orderable' => false,
                'searchable' => false,
                'className' => 'text-right align-middle',
            ])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip', 'order' => [0]]));
    }
}
