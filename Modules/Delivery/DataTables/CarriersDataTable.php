<?php

namespace Modules\Delivery\DataTables;

use App\DataTables\DataTable;
use Illuminate\Http\JsonResponse;
use Modules\Delivery\Entities\DeliveryMan;

class CarriersDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $carrier = $this->query();

        return datatables()
            ->of($carrier)
            ->editColumn('id', function ($carrier) {
                return $carrier->id;
            })
            ->editColumn('unique_id', function ($carrier) {
                return $carrier->unique_id;
            })
            ->editColumn('name', function ($carrier) {
                if (! isset($carrier->user)) {
                    return '-';
                }

                return '<a href="' . route('users.edit', [$carrier->user->id]) . '">' . $carrier->user->name . '</a>';
            })
            ->editColumn('license_status', function ($carrier) {
                return statusBadges($carrier->license_status);
            })
            ->editColumn('is_active', function ($carrier) {
                return $carrier->is_active ? statusBadges('yes') : statusBadges('no');
            })
            ->editColumn('assigned_order_count', function ($carrier) {
                return $carrier->assignedOrders()->count();
            })
            ->editColumn('action', function ($carrier) {
                $edit = '<a title="' . __('Edit') . '" href="' . route('admin.delivery.carrier.edit', [$carrier->id]) . '" class="action-icon"><i class="feather icon-edit-1"></i></a>&nbsp';

                $delete = '<form method="post" action="' . route('admin.delivery.carrier.destroy', [$carrier->id]) . '" id="delete-carrier-' . $carrier->id . '" accept-charset="UTF-8" class="display_inline">'
                    . csrf_field() .
                    '
                    <input type="hidden" name="_method" value="delete">
                    <a title="' . __('Delete') . '" class="action-icon confirm-delete" type="button" data-id=' . $carrier->id . ' data-delete="carrier" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Delivery Man')]) . '" data-message="' . __('Are you sure to delete this?') . '">
                                <i class="feather icon-trash"></i>
                            </a>
                        </form>';

                return $edit . $delete;
            })
            ->rawColumns(['id', 'unique_id', 'name', 'license_status', 'is_active', 'assigned_order_count', 'action'])
            ->make(true);
    }

    public function query()
    {
        $carrier = DeliveryMan::query()
            ->with(['user:id,name', 'assignedOrders'])
            ->filter();

        return $this->applyScopes($carrier);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'visible' => false, 'searchable' => false])
            ->addColumn(['data' => 'unique_id', 'name' => 'unique_id', 'title' => __('Unique ID'), 'orderable' => false, 'searchable' => true, 'className' => 'align-middle'])
            ->addColumn(['data' => 'user.name', 'name' => 'user.name', 'title' => __('Name'), 'searchable' => true, 'className' => 'align-middle'])
            ->addColumn(['data' => 'license_status', 'name' => 'license_status', 'title' => __('License Status'), 'orderable' => true, 'className' => 'align-middle'])
            ->addColumn(['data' => 'is_active', 'name' => 'is_active', 'title' => __('Available'), 'orderable' => true, 'className' => 'text-center align-middle', 'width' => '15%'])
            ->addColumn(['data' => 'assigned_order_count', 'name' => 'assigned_order_count', 'title' => __('Orders'), 'orderable' => false, 'searchable' => false, 'width' => '10%', 'className' => 'text-center align-middle'])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => '', 'visible' => true, 'orderable' => false, 'searchable' => false, 'width' => '8%', 'className' => 'text-right align-middle'])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));
    }

    public function setViewData()
    {
        $statusCounts = $this->query()
            ->selectRaw('license_status, COUNT(*) as count')
            ->groupBy('license_status')
            ->pluck('count', 'license_status');

        $this->data['groups'] = ['All' => $statusCounts->sum()] + $statusCounts->toArray();
    }
}
