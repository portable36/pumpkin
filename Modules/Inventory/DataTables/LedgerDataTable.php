<?php

namespace Modules\Inventory\DataTables;

use App\DataTables\DataTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Entities\PurchasePayment;

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
        $purchase = $this->query();
        $from = $this->from;

        return datatables()
            ->of($purchase)
            ->editColumn('reference', function ($purchase) use ($from) {
                if ($from == 'vendor') {
                    return '<a href="' . route('vendor.purchase.view', ['id' => $purchase->id]) . '">' . $purchase->reference . '</a>';
                } else {
                    return '<a href="' . route('purchase.view', ['id' => $purchase->id]) . '">' . $purchase->reference . '</a>';
                }
            })
            ->editColumn('created_at', function ($purchase) {
                return timeZoneFormatDate($purchase->created_at);
            })
            ->editColumn('paid_amount', function ($purchase) {
                return formatNumber($purchase->paid_amount);
            })
            ->editColumn('order_amount', function ($purchase) {
                return formatNumber($purchase->order_amount);
            })
            ->editColumn('description', function ($purchase) {
                return str_replace('_', ' ', $purchase->description);
            })
            ->editColumn('status', function ($purchase) {
                return '<span class="f-w-600 f-12 text-muted text-uppercase">' . $purchase->status . '</span>';
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
        $supplierId = request()->id;

        $purchases = Purchase::select('purchases.id', 'reference', 'purchases.status', 'note as description', DB::raw('0 as paid_amount'), 'total_amount as order_amount', 'purchase_date as created_at')
            ->where('supplier_id', $supplierId);

        $payments = PurchasePayment::join('purchases', 'purchase_payments.purchase_id', '=', 'purchases.id')
            ->select('purchases.id as id', 'purchases.reference as reference', 'purchases.status as status', 'description', 'amount as paid_amount', DB::raw('0 as order_amount'), 'payment_date as created_at')
            ->where(['purchases.supplier_id' => $supplierId]);

        // Wrap union in a subquery
        $union = $purchases->unionAll($payments);

        return DB::query()
            ->fromSub($union, 'ledger')
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
