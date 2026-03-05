<?php

namespace Modules\Inventory\Http\Controllers\Vendor;

use App\Models\Country;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\Inventory\DataTables\LedgerDataTable;
use Modules\Inventory\DataTables\SupplierDataTable;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Entities\Supplier;
use Modules\Inventory\Http\Requests\SupplierStoreRequest;
use Modules\Inventory\Http\Requests\SupplierUpdateRequest;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function index(SupplierDataTable $dataTable)
    {
        return $dataTable->with('from', 'vendor')->render('inventory::common.supplier.index', [
            'from' => 'vendor',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        $data['countries'] = Country::getAll();
        $data['from'] = 'vendor';

        return view('inventory::common.supplier.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(SupplierStoreRequest $request)
    {
        $request['vendor_id'] = auth()->user()->vendor()->vendor_id;
        $supplier = Supplier::store($request->all());

        if ($supplier) {

            if (isset($request->api)) {
                return response()->json([
                    'status' => true,
                    'response' =>  $supplier,
                    'message' => __('The :x has been successfully saved.', ['x' => __('Supplier')]),
                ]);
            }

            return redirect()->route('vendor.supplier.index')->withSuccess(__('The :x has been successfully saved.', ['x' => __('Supplier')]));
        }

        return redirect()->back()->withErrors(__('Something went wrong, please try again.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data['supplier'] = Supplier::where('id', $id)->where('vendor_id', auth()->user()->vendor()->vendor_id)->first();

        if (empty($data['supplier'])) {
            abort(404);
        }

        $data['from'] = 'vendor';

        return view('inventory::common.supplier.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(SupplierUpdateRequest $request, $id)
    {

        $supplier = Supplier::where('id', $id)->where('vendor_id', auth()->user()->vendor()->vendor_id)->first();

        if (empty($supplier)) {
            abort(404);
        }

        if (Supplier::updateLocation($request->only('name', 'company_name', 'vendor_id', 'parent_id', 'address', 'country', 'state', 'city', 'zip', 'phone', 'email', 'status'), $id)) {
            return redirect()->route('vendor.supplier.index')->withSuccess(__('The :x has been successfully saved.', ['x' => __('Supplier')]));
        }

        return redirect()->back()->withErrors(__('Something went wrong, please try again.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $supplier = Supplier::where('id', $id)->where('vendor_id', auth()->user()->vendor()->vendor_id)->first();

        if (empty($supplier)) {
            abort(404);
        }

        $status = Supplier::remove($id);

        if (isset($status['type']) && $status['type'] == 'success') {
            return redirect()->back()->withSuccess($status['message']);
        }

        return redirect()->back()->withErrors($status['message']);
    }

    /**
     * Supplier ledger
     *
     * @return \Illuminate\Http\Response
     */
    public function ledger(LedgerDataTable $dataTable, $id)
    {
        $vendorId = auth()->user()?->vendor()?->vendor_id;

        $supplier = Supplier::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->first();

        if (! $supplier) {
            return redirect()->back()->withFail(__('Supplier does not exist.'));
        }

        $data = [
            'supplier' => $supplier,
            'from' => 'vendor',
            'orderTotal' => Purchase::where('supplier_id', $id)->sum('total_amount'),
            'transactionTotal' => Purchase::where('supplier_id', $id)->sum('paid_amount'),
            'paymentMethods' => (new \Modules\Gateway\Entities\GatewayModule())->payableGateways(),
        ];

        return $dataTable->with('from', 'vendor')->render('inventory::common.supplier.ledger', $data);
    }

    /**
     * Supplier payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function payment($id)
    {
        $vendorId = auth()->user()?->vendor()?->vendor_id;

        $supplier = Supplier::where('id', $id)->where('vendor_id', $vendorId)->first();

        if (! $supplier) {
            return redirect()->back()->withFail(__('Supplier not found.'));
        }

        $purchases = Purchase::where('supplier_id', $id)
            ->where('payment_status', '!=', 'Paid')
            ->orderByDesc('purchase_date')
            ->orderByDesc('reference')
            ->get();

        return view('inventory::common.supplier.payment', [
            'supplier'  => $supplier,
            'from'      => 'vendor',
            'purchases' => $purchases,
        ]);
    }

    /**
     * Store supplier payment
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function paymentStore(\Illuminate\Http\Request $request, $id)
    {
        $vendorId = auth()->user()?->vendor()?->vendor_id;

        $supplier = Supplier::where('id', $id)
            ->where('vendor_id', $vendorId)
            ->first();

        if (! $supplier) {
            return redirect()->back()->withErrors(__('Supplier does not exist.'));
        }

        $amounts       = $request->input('amounts', []);
        $paymentMethod = $request->input('payment_method');
        $paymentDate   = $request->input('payment_date', now());
        $description   = $request->input('description');

        $service = new \Modules\Inventory\Services\PurchaseService($request);
        $result = $service->supplierMultiPaymentStore($amounts, $paymentMethod, $paymentDate, $description);

        if ($result['status']) {
            return redirect()->route('vendor.supplier.ledger', $supplier->id)
                ->withSuccess($result['msg'] ?? __('Payments processed successfully.'));
        } else {
            return redirect()->back()->withErrors($result['message'] ?? __('Payment failed.'));
        }
    }
}
