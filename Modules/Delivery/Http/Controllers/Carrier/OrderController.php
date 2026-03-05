<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Delivery\Services\CarrierOrderService;
use Modules\Refund\Entities\RefundReason;
use App\Services\Actions\OrderAction;
use App\Models\{
    Order,
    OrderDetail,
    OrderNoteHistory,
    OrderStatus,
    OrderStatusHistory,
    Preference,
};
use Illuminate\Support\Facades\{
    Auth,
    DB
};
use Modules\Delivery\DataTables\{
    AssignDataTable,
    PickupDataTable
};
use Modules\Delivery\Entities\{
    DeliveryManOrder
};

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function assign(AssignDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.order.assign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function pickup(PickupDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.order.assign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function delivered(PickupDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.order.assign');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Renderable
     */
    public function completed(PickupDataTable $dataTable)
    {
        return $dataTable->render('delivery::carrier.order.assign');
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        $orderId = base64_encode(base64_decode($id, true)) === $id ? base64_decode($id) : $id;

        $order = Order::where('id', $orderId)->whereHas('deliveryMens', function ($query) {
            $query->where('delivery_man_id', auth()->user()->deliveryMan?->id);
        })->first();

        if (! $order) {
            return to_route('carrier.dashboard');
        }

        $data['orderStatus'] = OrderStatus::whereHas('orderStatusRole', function ($q) {
            $q->where('role_id', auth()->user()->role()->id);
        })->orderBy('order_by', 'ASC')->whereIn('slug', ['assigned', 'pickup', 'delivered'])->get();
        $data['order'] = $order;
        $data['orderDetails'] = $order->orderDetails->groupBy('vendor_id');
        $data['refundReasons'] = RefundReason::where('status', 'Active')->get();
        $data['orderStatusHistories'] = OrderStatusHistory::where('order_id', $id)->whereNotNull('product_id')->orderByDesc('id')->get();
        $data['finalOrderStatus'] = OrderStatus::orderBy('order_by', 'DESC')->first()->id;
        $data['orderNotes'] = OrderNoteHistory::where(['order_id' => $id, 'user_id' => auth()->user()->id])->orderBy('id', 'desc')->get();
        $data['orderAction'] = new OrderAction();

        return view('delivery::carrier.order.view', $data);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function changeStatus(Request $request)
    {
        try {
            $deliveredStatus = DeliveryManOrder::getDeliveredOrderStatus();


            $data['status'] = 0;
            $data['message'] = __('The :x has been successfully saved.', ['x' => __('Order')]);

            DB::beginTransaction();

            (new CarrierOrderService())->handleOrderStatus($request->data['order_id'], $request->data['status_id']);

            $data['status'] = 1;

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            $data['error'] = __('Something went wrong, please try again.');
        }

        return $data;
    }

    /**
     * Order invoice print function
     */
    public function print(int|string $id)
    {
        try {
            $order = Order::findOrFail($id);

            if (! $order) {
                throw new \Exception(__('Order not found.'));
            }

            $data = $this->preparePrintData($order);

            if (request()->get('type') != 'pdf') {
                throw new \Exception(__('Order type missing.'));
            }

            return $this->generatePDF($data, $order->reference . '.pdf');

        } catch (\Exception $e) {
            $response = $this->messageArray($e->getMessage(), 'fail');
            $this->setSessionValue($response);

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Prepare data for order print
     */
    private function preparePrintData(Order $order): array
    {
        return [
            'orderStatus' => OrderStatus::getAll()->sortBy('order_by'),
            'order' => $order,
            'logo' => Preference::where('field', 'company_logo')->first()->fileUrl(),
            'orderAction' => new OrderAction(),
            'user' => $order->user,
            'orderDetails' => $order->orderDetails,
            'type' => in_array(request()->get('type'), ['print', 'pdf']) ? request()->get('type') : 'print',
        ];
    }

    /**
     * Oder pdf generate
     */
    private function generatePDF(array $data, string $filename): mixed
    {
        $data['unit'] = defaultUnit();
        return printPDF($data, $filename, 'delivery::carrier.order.invoice_print', view('delivery::carrier.order.invoice_print', $data), 'pdf');
    }

    /**
     * Store order status history
     */
    public function storeOrderStatusHistory(OrderDetail $orderDetail, Request $request): void
    {
        $history['user_id'] = Auth::user()->id;
        $history['product_id'] = $orderDetail->product_id;
        $history['order_id'] = $request->data['order_id'];
        $history['order_status_id'] = $request->data['status_id'];

        (new OrderStatusHistory())->store($history);
    }
}
