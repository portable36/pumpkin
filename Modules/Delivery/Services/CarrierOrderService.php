<?php

namespace Modules\Delivery\Services;

use App\Models\{
    Order,
    OrderDetail,
    OrderStatus,
    OrderStatusHistory,
    Transaction
};
use Illuminate\Support\Facades\{
    Auth,
    DB,
    Log
};
use Modules\Delivery\Entities\{
    Delivery,
    DeliveryCommission,
    DeliveryManOrder
};
use Modules\Gateway\Entities\PaymentLog;

class CarrierOrderService
{
    public function deliveryCommission($orderId, $statusId)
    {
        try {
            if (! $this->isAssigned($orderId)) {
                throw new \Exception(__('This order can not assigned.'));
            }

            if (preference('payment_type_delivery_man') == 'commission') {
                $orderDetails =  OrderDetail::where('order_id', $orderId)->get();
                foreach ($orderDetails as $value) {
                    $this->commissionTransaction($value->id, $statusId);
                }
            }

            if (preference('payment_type_delivery_man') == 'flat') {
                $orderDetails = $this->getOrderDetailsByVendor($orderId);

                foreach ($orderDetails as $value) {
                    $this->flatTransaction($value, $statusId, $orderDetails->count('vendor_id'));
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Carrier commission transaction
     */
    public function commissionTransaction(string|int $orderDetailId, string|int $statusId): mixed
    {
        $finalOrderStatus = Order::getFinalOrderStatus();
        $orderDetails = OrderDetail::where('id', $orderDetailId)->with('order', 'order.deliveryMens', 'vendor')->first();

        if ($finalOrderStatus != $statusId || empty($orderDetails)) {
            return false;
        }

        $totalAmount = $orderDetails->price * $orderDetails->quantity;
        $totalCommission = (preference('commission_percentage') * $totalAmount) / 100;

        $this->createTransaction($orderDetails, $totalCommission, 'order_details');

        if (isset($orderDetails->vendor)) {
            $this->handleWalletUpdates($orderDetails, $totalCommission);
        }

        return true;
    }

    /**
     * Carrier flat transaction
     */
    public function flatTransaction(object $orderDetails, string|float $statusId, string|int $count): mixed
    {
        $finalOrderStatus = Order::getFinalOrderStatus();

        if ($finalOrderStatus != $statusId || empty($orderDetails)) {
            return false;
        }

        $totalCommission = preference('commission_flat') / $count;

        $this->createTransaction($orderDetails, $totalCommission, 'order');

        if (isset($orderDetails->vendor)) {
            $this->handleWalletUpdates($orderDetails, $totalCommission);
        }

        return true;
    }

    /**
     * Handle carrier wallet
     */
    private function handleWalletUpdates(object $orderDetails, string|float $totalCommission): void
    {
        $orderDetails->order->deliveryMens->first()->user->wallet()->incrementBalance($totalCommission);
    }

    /**
     * Create transaction
     */
    private function createTransaction(object $orderDetails, string|float $totalCommission, string $reference_type): void
    {
        $transaction['user_id'] =  getUserId();
        $transaction['currency_id'] = $orderDetails->order->currency_id ?? null;
        $transaction['order_id'] =  $orderDetails->order->id ?? null;
        $transaction['exchange_rate'] = $orderDetails->order->currency->exchange_rate ?? null;
        $transaction['vendor_id'] = $orderDetails->vendor_id ?? null;
        $transaction['shop_id'] =  $orderDetails->shop_id ?? null;
        $transaction['total_amount'] = $totalCommission;
        $transaction['amount'] = $totalCommission;
        $transaction['reference_number'] = optional($orderDetails)->order_id;
        $transaction['reference_type'] = $reference_type;
        $transaction['transaction_type'] = 'Admin_delivery_commission';
        $transaction['transaction_date'] = now();
        $transaction['status'] = 'Accepted';
        (new Transaction())->store($transaction);
        $transaction['reference_number'] = optional($orderDetails->order->deliveryMens)->first()->user_id;
        $transaction['transaction_type'] = 'delivery_commission';
        $transaction['reference_type'] = 'delivery_man_user_id';
        (new Transaction())->store($transaction);
    }

    public function OrderCollection(Order $order): bool
    {
        if (optional($order->paymentMethod)->gateway == 'CashOnDelivery') {
            return false;
        }

        $orderCollectionData = $this->prepareOrderCollectionData($order, $order->deliveryMens->first()->id);
        $collection = Delivery::UpdateOrCreate(['order_id' => $order->id], $orderCollectionData);

        if (! $collection) {
            return false;
        }

        return true;
    }

    /**
     * Prepare data for order collect data
     */
    private function prepareOrderCollectionData(Order $order, int|string $deliveryManId, ?string $remark = null): array
    {
        return [
            'delivery_man_id' => $deliveryManId,
            'order_id' => $order->id,
            'date' => now(),
            'collected_amount' => $order->amount_received,
            'remark' => $remark,
        ];
    }

    /**
     * Calculate delivery total collection
     */
    public static function calculateTotalCollection(string|int $deliveryManId): mixed
    {
        $deliveryManCollection = Delivery::where('delivery_man_id', $deliveryManId)->sum('collected_amount');
        $adminWithdrawalCollection = Transaction::where(['reference_number' => $deliveryManId, 'reference_type' => 'delivery_man', 'transaction_type' => 'Admin_collected_collection_from_delivery_man'])->sum('total_amount');

        return $deliveryManCollection - $adminWithdrawalCollection;
    }

    /**
     * Store order status history
     */
    public function storeOrderStatusHistory(OrderDetail $orderDetail, array $data): void
    {
        $history['user_id'] = Auth::user()->id;
        $history['product_id'] = $orderDetail->product_id;
        $history['order_id'] = $data['order_id'];
        $history['order_status_id'] = $data['status_id'];

        (new OrderStatusHistory())->store($history);
    }

    public function handleOrderStatus(string|int $orderId, string|int $orderStatusId): bool
    {
        try {
            DB::beginTransaction();
            $order = Order::where('id', $orderId)->with('deliveryMens')->first();

            if (! $order) {
                throw new \Exception(__('Order not found.'));
            }

            $deliveredStatus = DeliveryManOrder::getDeliveredOrderStatus();

            if (! (new Order())->updateOrder(['order_status_id' => $orderStatusId], $order->id)) {
                throw new \Exception(__('Something is wrong. Order status can not update.'));
            }

            $orderDetails = OrderDetail::where('order_id', $order->id)->get();

            foreach ($orderDetails as $detail) {
                $detailData = ['order_status_id' => $orderStatusId];
                (new OrderDetail())->updateOrder($detailData, $detail->id);
                $this->storeOrderStatusHistory($detail, ['order_id' => $order->id, 'status_id' => $orderStatusId]);

                if (isCarrierCommissionable($deliveredStatus, $orderStatusId)) {
                    $this->handleDeliveryCommission($detail, $order->deliveryMens->first()->id);
                }
            }

            if ($deliveredStatus == $orderStatusId) {
                $this->OrderCollection($order);
            }

            DB::commit();

            return true;
        } catch (\Exception $e) {
            Log::error('Error from updateOrderStatus method of carrier order service class: ' . $e->getMessage());
            DB::rollBack();

            return false;
        }
    }

    public function handleDeliveryCommission(OrderDetail $orderDetail, string|int $deliveryManId): void
    {
        $amount = preference('payment_type_delivery_man') == 'commission' ? preference('commission_percentage') : preference('commission_flat');
        (new DeliveryCommission())->storeOrUpdate([
            'order_id' => $orderDetail->order_id,
            'order_details_id' => $orderDetail->id,
            'delivery_man_id' => $deliveryManId,
            'amount' => $amount ?: 0,
            'status' => 'Pending',
        ]);
    }

    /**
     * Get order details by vendor
     */
    public function getOrderDetailsByVendor(string|int|null $orderId = null): mixed
    {
        return OrderDetail::with('order', 'order.deliveryMens', 'vendor')->where('order_id', $orderId)->groupBy('vendor_id')->get();
    }

    /**
     * Check order assign or not
     */
    public function isAssigned(string|int $orderId): bool
    {
        return (bool) DeliveryManOrder::where('order_id', $orderId)->first();
    }

    /**
     * Update Order
     *
     * @param  array  $data
     * @param  null  $id
     * @return bool
     */
    public function updateOrder($data = [], $id = null)
    {
        $result = Order::where('id', $id)->first();

        if (isset($data['user_id'])) {
            $data['user_id'] = $data['user_id'] == 'Guest' ? null : $data['user_id'];
        }

        if (empty($result)) {
            return false;
        }

        if (isset($data['order_status_id'])) {
            $deliveryId = Order::getFinalOrderStatus();

            if ($deliveryId == $data['order_status_id']) {
                $data['is_delivery'] = 1;

                if (in_array(optional($result->paymentMethod)->gateway, offLinePayments())) {
                    PaymentLog::where('code', $result->reference)->update(['status' => 'completed']);
                }
            } else {
                $data['is_delivery'] = 0;
            }

            $orderStatusInfo = OrderStatus::getAll()->where('id', $data['order_status_id'])->first();

            if (strtolower($result->payment_status) != 'paid') {
                $data['payment_status'] = 'Paid';
                $data['paid'] = $result->total;
                $data['amount_received'] = $result->total;
                $result->transactionStore();
            }
        }

        Order::where('id', $id)->update($data);

        return true;

    }
}
