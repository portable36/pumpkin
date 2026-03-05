<?php

namespace Modules\Inventory\Services;

use App\Models\Product;
use Modules\Inventory\Entities\Log;
use Modules\Inventory\Entities\Purchase;
use Modules\Inventory\Entities\PurchaseDetail;
use DB;
use Modules\Inventory\Entities\PurchasePayment;
use Modules\Inventory\Entities\StockManagement;

class PurchaseService
{
    public static $instance;

    protected $request;

    protected $vendorId = null;

    /**
     * purchase service instance
     *
     * @return PurchaseService
     */
    public static function getInstance($request = null, $vendorId = null)
    {
        if (self::$instance == null) {
            self::$instance = new PurchaseService($request, $vendorId);
        }

        return self::$instance;
    }

    /**
     * constructor
     */
    public function __construct($request = null, $vendorId = null)
    {
        $this->request = $request;
        $this->vendorId = $vendorId;
    }

    /**
     * purchase store
     *
     * @return array|true[]
     */
    public function purchaseStore(): array
    {
        $response = ['status' => true];

        try {
            DB::beginTransaction();
            $reference = Purchase::getPurchaseReference();
            $request = $this->request;
            $purchaseDetails = [];

            $purchaseCalculations = $this->purchaseCalculations();

            $purchase = [
                'reference' => $reference,
                'vendor_id' => $this->vendorId,
                'supplier_id' => $request->supplier_id,
                'location_id' => $request->location_id,
                'payment_type' => $request->payment_type,
                'currency_id' => $request->currency_id,
                'shipping_carrier' => $request->shipping_carrier,
                'tracking_number' => $request->tracking_number,
                'purchase_date' => $request->purchase_date,
                'arrival_date' => $request->arrival_date,
                'note' => $request->note,
                'shipping_charge' => $request->shipping_amount ?? null,
                'adjustment' => isset($request->adjustment) ? json_encode($request->adjustment) : null,
                'total_quantity' => $purchaseCalculations['totalQty'],
                'total_amount' => $purchaseCalculations['totalAmount'],
                'tax_charge' => $purchaseCalculations['totalTax'],
                'status' => 'Ordered',
            ];

            $purchaseId = Purchase::store($purchase);

            $purchase['purchase_id'] = $purchaseId;
            $purchase['quantity'] = $purchase['total_quantity'];
            $purchase['price'] = $purchase['total_amount'];
            $purchase['transaction_type'] = 'purchase_store';
            $purchase['log_type'] = 'purchase';
            $this->logStore($purchase);

            if ($purchaseId) {

                foreach ($request->product_id as $key => $productId) {

                    $purchaseDetails[] = [
                        'purchase_id' => $purchaseId,
                        'product_id' => $productId,
                        'product_name' => $request->product_name[$key],
                        'unit' => $request->product_unit[$key] ?? null,
                        'quantity' => $request->product_qty[$key],
                        'amount' => $request->product_cost[$key],
                        'sku' => $request->sup_sku[$key],
                        'tax_charge' => $request->product_tax[$key],
                    ];
                }

                PurchaseDetail::store($purchaseDetails);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * calculation
     */
    protected function purchaseCalculations(): array
    {
        $request = $this->request;
        $totalAmount = 0;
        $totalTax = 0;
        $totalQty = 0;

        foreach ($request->product_id as $key => $productId) {
            $productCost = (float) validateNumbers($request->product_cost[$key]);
            $productTax = (float) validateNumbers($request->product_tax[$key]);
            $productQty = (float) validateNumbers($request->product_qty[$key]);

            $tax = (($productCost * $productQty) * $productTax) / 100;
            $totalTax += $tax;
            $totalQty += $productQty;
            $totalAmount += ($productCost * $productQty) + $tax;

        }

        if (isset($request->shipping_amount)) {
            $totalAmount += (float) $request->shipping_amount;
        }

        if (isset($request->adjustment) && isset($request->adjustment['name'])) {

            foreach ($request->adjustment['name'] as $key => $adjust) {
                if (isset($request->adjustment['amount'][$key])) {
                    $totalAmount += (float) $request->adjustment['amount'][$key];
                }
            }
        }

        return [
            'totalAmount' => $totalAmount,
            'totalTax' => $totalTax,
            'totalQty' => $totalQty,
        ];
    }

    /**
     * update purchase
     *
     * @return array|true[]
     */
    public function updatePurchase($id): array
    {
        $response = ['status' => true];

        try {
            DB::beginTransaction();

            $request = $this->request;
            $existsPurchase = Purchase::find($id);
            $isLogAble = false;
            $existsProduct = PurchaseDetail::where('purchase_id', $id)->pluck('product_id', 'id')->toArray();
            $swap = array_flip($existsProduct);
            $updateProduct = [];

            $purchaseDetails = [];
            $purchaseCalculations = $this->purchaseCalculations();

            if ($existsPurchase->total_amount != $purchaseCalculations['totalAmount'] || $existsPurchase->total_quantity != $purchaseCalculations['totalQty']) {
                $isLogAble = true;
            }

            $purchase = [
                'supplier_id' => $request->supplier_id,
                'location_id' => $request->location_id,
                'payment_type' => $request->payment_type,
                'currency_id' => $request->currency_id,
                'shipping_carrier' => $request->shipping_carrier,
                'shipping_charge' => $request->shipping_amount ?? null,
                'adjustment' => isset($request->adjustment) ? json_encode($request->adjustment) : null,
                'tracking_number' => $request->tracking_number,
                'purchase_date' => $request->purchase_date,
                'arrival_date' => $request->arrival_date,
                'note' => $request->note,
                'total_quantity' => $purchaseCalculations['totalQty'],
                'total_amount' => $purchaseCalculations['totalAmount'],
                'tax_charge' => $purchaseCalculations['totalTax'],
            ];

            $purchaseId = Purchase::updatePurchase($purchase, $id);

            $purchase['purchase_id'] = $id;
            $purchase['quantity'] = $purchase['total_quantity'];
            $purchase['price'] = $purchase['total_amount'];
            $purchase['transaction_type'] = 'purchase_update';
            $purchase['log_type'] = 'purchase';

            if ($isLogAble) {
                $this->logStore($purchase);
            }

            if ($purchaseId) {
                foreach ($request->product_id as $key => $productId) {
                    $updateProduct[] = $productId;

                    if (in_array($productId, $existsProduct)) {
                        $updateDetailData = [
                            'product_id' => $productId,
                            'product_name' => $request->product_name[$key],
                            'unit' => $request->product_unit[$key] ?? null,
                            'quantity' => validateNumbers($request->product_qty[$key]),
                            'amount' => validateNumbers($request->product_cost[$key]),
                            'sku' => $request->sup_sku[$key],
                            'tax_charge' => validateNumbers($request->product_tax[$key]),
                        ];

                        PurchaseDetail::updatePurchaseDetail($updateDetailData, $swap[$productId]);
                    } else {
                        $purchaseDetails[] = [
                            'purchase_id' => $id,
                            'product_id' => $productId,
                            'product_name' => $request->product_name[$key],
                            'unit' => $request->product_unit[$key] ?? null,
                            'quantity' => validateNumbers($request->product_qty[$key]),
                            'amount' => validateNumbers($request->product_cost[$key]),
                            'sku' => $request->sup_sku[$key],
                            'tax_charge' => validateNumbers($request->product_tax[$key]),
                        ];
                    }
                }

                if (count($purchaseDetails) > 0) {
                    PurchaseDetail::store($purchaseDetails);
                }

                foreach ($existsProduct as $existsId) {
                    if (! in_array($existsId, $updateProduct)) {
                        PurchaseDetail::remove($swap[$existsId]);
                    }
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * product search
     *
     * @return \Illuminate\Http\JsonResponse\
     */
    public function search()
    {
        $data['status']  = 0;
        $data['message']    = __('No Item Found');
        $vendorId = $this->vendorId;
        $request = $this->request;
        $search = $request->search;
        $products = Product::select('id', 'name', 'status', 'type', 'vendor_id', 'manage_stocks')
            ->whereLike('name', $search)
            ->where('type', '!=', 'Grouped Product')
            ->where('type', '!=', 'Variable Product')
            ->where('vendor_id', $vendorId)
            ->orWhereHas('parentDetail', function ($q) use ($vendorId, $search) {
                $q->where('vendor_id', $vendorId)->whereLike('name', $search);
            })
            ->published()
            ->limit(10)
            ->get()
            ->map(function ($query) {
                return [
                    'id' => $query->id,
                    'name' => $query->name,
                    'unit' => $query->unit,
                    'default_unit' => defaultUnit()?->abbr,
                    'status' => $query->status,
                    'type' => $query->type,
                    'vendor_id' => $query->vendor_id,
                    'manage_stocks' => $query->manage_stocks,
                ];
            });

        if (! $products->isEmpty()) {
            $data['status'] = 1;
            $data['message'] = __('Product Found');
            $data['products'] = $products;
        }

        return response()->json($data);
    }

    /**
     * receive reject
     *
     * @return array|true[]
     */
    public function receiveReject($purchaseId = null): array
    {
        $response = ['status' => true];
        $purchase = Purchase::where('id', $purchaseId)->first();

        try {
            DB::beginTransaction();

            $request = $this->request;
            $stock = [];
            $updateQty = 0;

            foreach ($request->products['receive'] as $key => $data) {

                $purchaseDetails = PurchaseDetail::where('id', $key)->first();
                $orderQty = $purchaseDetails->quantity;
                $addedQty = $data + $request->products['reject'][$key];
                $oldRec = $purchaseDetails->quantity_receive ?? 0;
                $oldRej = $purchaseDetails->quantity_reject ?? 0;
                $addedQty = $oldRec + $oldRej;

                if ($addedQty > $orderQty) {
                    $response = ['status' => false, 'message' => __('Sum of receive reject value not more that order quantity!')];

                    return $response;
                }

                if ($request->products['reject'][$key] > 0) {

                    $updateQty += $purchaseDetails->quantity_reject + $request->products['reject'][$key];

                    if (empty($purchaseDetails->quantity_reject)) {
                        $purchaseDetails->quantity_reject = $request->products['reject'][$key];
                    } else {
                        $purchaseDetails->incrementReject($request->products['reject'][$key]);
                    }

                    $log = [
                        'location_id' => $purchaseDetails->purchase->location_id,
                        'product_id' => $purchaseDetails->product_id,
                        'purchase_id' => $purchaseDetails->purchase_id,
                        'supplier_id' => $purchaseDetails->purchase?->supplier_id,
                        'purchase_detail_id' => $purchaseDetails->id,
                        'quantity' => -($request->products['reject'][$key]),
                        'transaction_type' => 'reject',
                        'log_type' => 'reject',
                    ];

                    $this->logStore($log);
                } else {
                    $updateQty += $purchaseDetails->quantity_reject;
                }

                if ($data > 0) {

                    $updateQty += $purchaseDetails->quantity_receive + $data;

                    if (empty($purchaseDetails->quantity_receive)) {
                        $purchaseDetails->quantity_receive = $data;
                    } else {
                        $purchaseDetails->incrementReceive($data);
                    }

                    $stock[] = [
                        'location_id' => $purchaseDetails->purchase->location_id,
                        'product_id' => $purchaseDetails->product_id,
                        'quantity' => $data,
                        'type' => 'purchase',
                        'date' => DbDateFormat(date('Y-m-d')),
                        'status' => 'approve',
                    ];

                    $log = [
                        'location_id' => $purchaseDetails->purchase->location_id,
                        'product_id' => $purchaseDetails->product_id,
                        'purchase_id' => $purchaseDetails->purchase_id,
                        'supplier_id' => $purchaseDetails->purchase?->supplier_id,
                        'purchase_detail_id' => $purchaseDetails->id,
                        'quantity' => $data,
                        'transaction_type' => 'receive',
                        'log_type' => 'receive',
                    ];

                    $this->logStore($log);
                } else {
                    $updateQty += $purchaseDetails->quantity_receive;
                }

                $purchaseDetails->save();
            }

            StockManagement::store($stock);

            if ($updateQty > $purchase->total_quantity) {
                $response = ['status' => false, 'message' => __('Quantity mismatch with order quantity!')];

                return $response;
            }

            if ($purchase->status == 'Ordered' && $updateQty > 0) {
                $purchase->status = 'Partial';
                $purchase->save();
            }

            if ($purchase->total_quantity == $updateQty && $updateQty > 0) {
                $purchase->status = 'Received';
                $purchase->save();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * store log
     *
     * @return false
     */
    protected function logStore($data)
    {
        $data['transaction_date'] = DbDateFormat(date('Y-m-d'));
        $response = Log::store($data);

        return $response;
    }

    public function purchasePaymentStore($purchase)
    {
        $response = ['status' => true];

        try {
            DB::beginTransaction();
            $request = $this->request;


            $purchase->paid_amount = $purchase->paid_amount + $request->amount_paid;
            $purchase->payment_status = $purchase->paid_amount >= $purchase->total_amount ? 'Paid' : 'Partially Paid';
            $purchase->save();

            $purchasePayment = new PurchasePayment();
            $purchasePayment->vendor_id = $request->vendor_id;
            $purchasePayment->supplier_id = $request->supplier_id;
            $purchasePayment->purchase_id = $purchase->id;
            $purchasePayment->payment_method = $request->payment_method;
            $purchasePayment->payment_date = $request->payment_date;
            $purchasePayment->amount = $request->amount_paid;
            $purchasePayment->description = $request->description;
            $purchasePayment->save();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }

    /**
     * supplier multi payment store
     *
     * @param  int  $supplierId
     * @param  array  $amounts
     * @param  string  $paymentMethod
     * @param  string  $paymentDate
     * @param  string  $description
     * @return array
     */
    public function supplierMultiPaymentStore($amounts, $paymentMethod = null, $paymentDate = null, $description = null)
    {
        $response = ['status' => true];
        $successCount = 0;

        if (! is_array($amounts) || empty($amounts)) {
            return ['status' => false, 'message' => __('No valid payments were submitted!')];
        }

        try {
            DB::beginTransaction();

            // Preload all purchases at once for efficiency
            $purchaseIds = array_keys(array_filter($amounts, function ($amt) {
                return floatval($amt) > 0;
            }));
            if (empty($purchaseIds)) {
                DB::rollBack();

                return ['status' => false, 'message' => __('No valid payments were submitted!')];
            }
            $purchases = \Modules\Inventory\Entities\Purchase::whereIn('id', $purchaseIds)->get()->keyBy('id');

            $now = $paymentDate ? $paymentDate : now();
            $paymentsToInsert = [];
            $purchasesToUpdate = [];

            foreach ($purchaseIds as $purchaseId) {
                $paidAmount = floatval($amounts[$purchaseId]);
                if ($paidAmount <= 0) {
                    continue; // skip zero/negative
                }

                if (! isset($purchases[$purchaseId])) {
                    continue; // skip invalid
                }

                $purchase = $purchases[$purchaseId];

                // Only process if there is still a balance
                $remaining = $purchase->total_amount - $purchase->paid_amount;
                if ($remaining <= 0) {
                    continue;
                }

                // Don't pay more than remaining balance
                $applyAmount = min($remaining, $paidAmount);

                $newPaid = $purchase->paid_amount + $applyAmount;
                $paymentStatus = $newPaid >= $purchase->total_amount ? 'Paid' : 'Partially Paid';

                // Queue purchase update data
                $purchasesToUpdate[] = [
                    'id' => $purchase->id,
                    'paid_amount' => $newPaid,
                    'payment_status' => $paymentStatus,
                ];

                // Prepare payment row insertion
                $paymentsToInsert[] = [
                    'vendor_id'      => $purchase->vendor_id,
                    'supplier_id'    => $purchase->supplier_id,
                    'purchase_id'    => $purchase->id,
                    'payment_method' => $paymentMethod,
                    'payment_date'   => $now,
                    'amount'         => $applyAmount,
                    'description'    => $description,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                $successCount++;
            }

            if ($successCount === 0) {
                DB::rollBack();

                return ['status' => false, 'message' => __('No valid payments were submitted!')];
            }

            // Bulk update purchases
            foreach ($purchasesToUpdate as $update) {
                \Modules\Inventory\Entities\Purchase::where('id', $update['id'])
                    ->update([
                        'paid_amount'    => $update['paid_amount'],
                        'payment_status' => $update['payment_status'],
                    ]);
            }

            // Bulk insert payment rows
            if (! empty($paymentsToInsert)) {
                \Modules\Inventory\Entities\PurchasePayment::insert($paymentsToInsert);
            }

            DB::commit();
            $response['msg'] = __('Payments processed for :x orders.', ['x' => $successCount]);
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }

        return $response;
    }
}
