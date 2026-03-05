<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Sakawat Hossain Rony <[sakawat.techvill@gmail.com]>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 *
 * @created 25-08-2021
 *
 * @modified 04-10-2021
 */

namespace App\DataTables\Vendor;

use App\DataTables\DataTable;
use App\Models\{
    Brand,
    VendorBrand
};
use Illuminate\Http\JsonResponse;

class VendorBrandListDataTable extends DataTable
{
    /**
     * Handle the AJAX request for attribute groups.
     *
     * This function queries attribute groups and returns the data in a format suitable
     * for DataTables to consume via AJAX.
     */
    public function ajax(): JsonResponse
    {
        $brands = $this->query();

        return datatables()
            ->of($brands)

            ->addColumn('image', function ($brands) {
                return '<img class="rounded" src="' . $brands->fileUrl() . '" alt="' . __('image') . '" width="40" height="40">';
            })

            ->editColumn('name', function ($brands) {
                return '<a href="' . route('vendor.brands.edit', ['id' => $brands->id]) . '">' . wrapIt($brands->name, 10, ['columns' => 2]) . '</a>';
            })
            ->editColumn('product_count', function ($brands) {
                return $brands->product_count;
            })
            ->editColumn('is_global', function ($brands) {
                return $brands->is_global ? __('Yes') : __('No');
            })
            ->editColumn('status', function ($brands) {
                return statusBadges(lcfirst($brands->status));
            })->editColumn('created_at', function ($brands) {
                return $brands->format_created_at;
            })
            ->addColumn('action', function ($brands) {
                $edit = '<a title="' . __('Edit') . '" href="' . route('vendor.brands.edit', ['id' => $brands->id]) . '" class="action-icon"><i class="feather icon-edit-1"></i></a>';

                $str = '';
                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\BrandController@edit')) {
                    if ($brands->is_global) {
                        return $str;
                    }
                    $str .= $edit;
                }
                if (auth()->user()?->hasPermission('App\Http\Controllers\Vendor\BrandController@destroy')) {
                    $str .= view('components.backend.datatable.delete-button', [
                        'route' => route('vendor.brands.destroy', ['id' => $brands->id]),
                        'id' => $brands->id,
                    ])->render();
                }

                return $str;
            })

            ->rawColumns(['image', 'name', 'status', 'action'])
            ->filter(function ($instance) {
                if (in_array(request('status'), getStatus())) {
                    $instance->where('status', request('status'));
                }

                if (isset(request('search')['value'])) {
                    $keyword = xss_clean(request('search')['value']);
                    if (! empty($keyword)) {
                        $instance->where(function ($query) use ($keyword) {
                            $query->WhereLike('name', $keyword)
                                ->OrWhereLike('status', $keyword);
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
        $vendorBrand = VendorBrand::where('vendor_id', auth()->user()->vendor()->vendor_id)->pluck('brand_id')->toArray();
        $brands = Brand::query()->withCount('product')->whereIn('id', $vendorBrand);

        return $this->applyScopes($brands);
    }

    /*
    * DataTable HTML
    *
    * @return \Yajra\DataTables\Html\Builder
    */
    public function html()
    {
        return $this->builder()

            ->addColumn(['data' => 'image', 'name' => 'image', 'title' => __('Image'), 'orderable' => false, 'searchable' => false, 'className' => 'align-middle text-left'])

            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => __('Name'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'product_count', 'name' => 'product_count', 'title' => __('Total Products'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'is_global', 'name' => 'is_global', 'title' => __('Global'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => __('Created at'), 'className' => 'align-middle'])

            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => 'Action',
                'visible' => auth()->user()?->hasAnyPermission(['App\Http\Controllers\Vendor\BrandController@edit', 'App\Http\Controllers\Vendor\BrandController@destroy']),
                'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle'])

            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));
    }

    /**
     * Set View Data
     *
     * @return void
     */
    public function setViewData()
    {
        $statusCounts = $this->query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->data['groups'] = ['All' => $statusCounts->sum()] + $statusCounts->toArray();
    }
}
