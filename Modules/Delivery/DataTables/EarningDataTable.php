<?php

namespace Modules\Delivery\DataTables;

use App\DataTables\DataTable;
use App\Models\{
    Transaction
};
use Illuminate\Http\JsonResponse;

class EarningDataTable extends DataTable
{
    public function ajax(): JsonResponse
    {
        $earing = $this->query();

        return datatables()
            ->of($earing)
            ->editColumn('id', function ($earing) {
                return $earing->id;
            })
            ->editColumn('reference', function ($earing) {
                return '<a target="_blank" href="' . route('carrier.show', [$earing->order_id]) . '">' .
                    optional($earing->order)->reference . '</a>';
            })
            ->editColumn('transaction_date', function ($earing) {
                return $earing->transaction_date;
            })
            ->editColumn('total_amount', function ($earing) {
                return formatNumber($earing->total_amount);
            })
            ->editColumn('action', function ($earing) {
                $view = '<a title="' . __('Show') . '" href="' . route('carrier.show', [$earing->order_id]) . '" class="btn btn-xs btn-outline-dark pe-2"><i class="feather icon-eye"></i></a>&nbsp';

                $edit = '<a title="' . __('download') . '" href="' . route('carrier.order_print', [$earing->order_id, 'type' => 'pdf']) . '" class="btn btn-xs btn-primary pe-2"><i class="feather icon-download"></i></a>&nbsp';

                return $view . $edit;
            })
            ->rawColumns(['id', 'reference', 'transaction_date', 'total_amount', 'action'])
            ->make(true);
    }

    public function query()
    {
        $earing = Transaction::where('reference_number', optional(auth()->user())->id)->where('transaction_type', 'delivery_commission')
            ->where('reference_type', 'delivery_man_user_id')
            ->groupBy('reference_number', 'order_id')
            ->selectRaw('SUM(total_amount) as total_amount, reference_number, order_id, transaction_date, created_at')
            ->with('order')->filter();

        return $this->applyScopes($earing);
    }

    public function html()
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('ID'), 'visible' => false, 'searchable' => false])
            ->addColumn(['data' => 'reference', 'name' => 'order.reference', 'title' => __('Order Code'), 'orderable' => false, 'searchable' => true])
            ->addColumn(['data' => 'transaction_date', 'name' => 'transaction_date', 'title' => __('Date'), 'searchable' => true])
            ->addColumn(['data' => 'total_amount', 'name' => 'total_amount', 'title' => __('Amount'), 'orderable' => true])
            ->addColumn(['data' => 'action', 'name' => 'action', 'title' => __('Action'), 'visible' => true, 'orderable' => false, 'searchable' => false])
            ->parameters(dataTableOptions(dataTableOptions(['dom' => 'Bfrtip'])));
    }
}
