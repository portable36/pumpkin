<?php

namespace Modules\Inventory\DataTables;

use App\DataTables\DataTable;
use Illuminate\Http\JsonResponse;
use Modules\Inventory\Entities\Supplier;

class SupplierDataTable extends DataTable
{
    /*
   * DataTable Ajax
   *
   * @return \Yajra\DataTables\DataTableAbstract|\Yajra\DataTables\DataTables
   */
    public function ajax(): JsonResponse
    {
        $suppliers = $this->query();
        $from = $this->from;

        return datatables()
            ->of($suppliers)
            ->editColumn('name', function ($suppliers) use ($from) {
                $route = $from === 'vendor' ? route('vendor.supplier.edit', ['id' => $suppliers->id]) : route('supplier.edit', ['id' => $suppliers->id]);

                return '<a href="' . $route . '">' . wrapIt($suppliers->name, 10, ['columns' => 5]) . '</a>';
            })->editColumn('email', function ($suppliers) {
                return wrapIt($suppliers->email, 20, ['columns' => 5]);
            })->editColumn('phone', function ($suppliers) {
                return wrapIt($suppliers->phone, 15, ['columns' => 5]);
            })->editColumn('address', function ($suppliers) {
                return wrapIt($suppliers->fullAddress(), 20, ['columns' => 5]);
            })->editColumn('vendor', function ($suppliers) {
                return wrapIt(optional($suppliers->vendor)->name, 20, ['columns' => 5]);
            })->editColumn('status', function ($suppliers) {
                return statusBadges(lcfirst($suppliers->status));
            })->editColumn('company_name', function ($suppliers) {
                return $suppliers->company_name;
            })->addColumn('action', function ($suppliers) use ($from) {

                if ($from === 'vendor') {
                    $editPermission = auth()->user()?->hasPermission('Modules\Inventory\Http\Controllers\Vendor\SupplierController@edit');
                    $deletePermission = auth()->user()?->hasPermission('Modules\Inventory\Http\Controllers\Vendor\SupplierController@destroy');
                    $editRoute = route('vendor.supplier.edit', ['id' => $suppliers->id]);
                    $deleteRoute = route('vendor.supplier.destroy', ['id' => $suppliers->id]);
                } else {
                    $editPermission = auth()->user()?->hasPermission('Modules\Inventory\Http\Controllers\SupplierController@edit');
                    $deletePermission = auth()->user()?->hasPermission('Modules\Inventory\Http\Controllers\SupplierController@destroy');
                    $editRoute = route('supplier.edit', ['id' => $suppliers->id]);
                    $deleteRoute = route('supplier.destroy', ['id' => $suppliers->id]);
                }

                $str = '';

                if ($editPermission) {
                    $str = '<a title="' . __('Edit') . '" href="' . $editRoute . '" class="action-icon"><i class="feather icon-edit-1"></i></a>&nbsp;';
                }

                if ($deletePermission) {
                    $str .= '<form method="post" action="' . $deleteRoute . '" id="delete-supplier-' . $suppliers->id . '" accept-charset="UTF-8" class="display_inline">
                        ' . csrf_field() . '
                        <a title="' . __('Delete') . '" class="action-icon confirm-delete" type="button" data-id=' . $suppliers->id . ' data-delete="supplier" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Supplier')]) . '" data-message="' . __('Are you sure to delete this?') . '">
                        <i class="feather icon-trash"></i>
                        </button>
                        </form>';
                }

                return $str;
            })
            ->rawColumns(['name', 'email', 'phone', 'address', 'status', 'action', 'vendor', 'company_name'])
            ->make(true);
    }

    /*
    * DataTable Query
    *
    * @return mixed
    */
    public function query()
    {
        $suppliers = Supplier::select('id', 'vendor_id', 'name', 'company_name', 'address', 'country', 'state', 'city', 'zip', 'phone', 'email', 'status')
            ->with('vendor')
            ->when($this->from === 'vendor', function ($query) {
                return $query->where('vendor_id', auth()->user()->vendor()->vendor_id);
            })
            ->filter();

        return $this->applyScopes($suppliers);
    }

    /*
    * DataTable HTML
    *
    * @return \Yajra\DataTables\Html\Builder
    */
    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('Id'), 'visible' => false])
            ->addColumn(['data' => 'name', 'name' => 'name', 'title' => __('Name'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'company_name', 'name' => 'company_name', 'title' => __('Company Name'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'email', 'name' => 'email', 'title' => __('Email'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'phone', 'name' => 'phone', 'title' => __('Phone'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'vendor', 'name' => 'vendor_id', 'title' => __('Vendor'), 'className' => 'align-middle', 'visible' => $this->from === 'admin'])
            ->addColumn(['data' => 'address', 'name' => 'address', 'title' => __('Address'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'width' => '12%',
                'className' => 'text-right align-middle',
                'visible' => auth()->user()?->hasAnyPermission(['Modules\Inventory\Http\Controllers\SupplierController@edit', 'Modules\Inventory\Http\Controllers\SupplierController@destroy']),
                'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));
    }

    public function setViewData()
    {
        $statusCounts = $this->query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->data['groups'] = ['All' => $statusCounts->sum()] + $statusCounts->toArray();
    }
}
