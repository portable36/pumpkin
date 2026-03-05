<?php

/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Md. Mostafijur Rahman <[mostafijur.techvill@gmail.com]>
 *
 * @created 18-08-2023
 */

namespace Modules\Delivery\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Order,
    OrderStatus,
};
use App\Services\Actions\Facades\OrderActionFacade as OrderAction;
use Modules\Delivery\Http\Requests\Api\V1\OrderStatusUpdateRequest;
use Modules\Delivery\Services\CarrierOrderService;
use App\Http\Resources\OrderStatusResource;
use Modules\Delivery\Http\Resources\OrderResource;

class OrderController extends Controller
{
    /**
     * User orders
     *
     * @return json $data
     */
    public function index(Request $request)
    {
        $configs = $this->initialize([], $request->all());
        $order = Order::with(OrderAction::relationsWith())->whereHas('deliveryMens', function ($query) {
            $query->where('delivery_man_id', auth()->guard('api')->user()->deliveryMan?->id);
        });

        $filterStatus = $request->filter_status ?? null;
        if (! empty($filterStatus)) {
            $order->whereHas('orderStatus', function ($query) use ($filterStatus) {
                $query->where('slug', $filterStatus);
            });
        }

        $startDate = $request->startDate ?? null;
        if (! empty($startDate)) {
            $order->where('order_date', '>=', DbDateFormat($startDate));
        }

        $endDate = $request->endDate ?? null;
        if (! empty($endDate)) {
            $order->where('order_date', '<=', DbDateFormat($endDate));
        }

        $order->orderBy('created_at', 'desc');

        return $this->response([
            'data' => OrderResource::collection($order->paginate($configs['rows_per_page'])),
            'order_statuses' => OrderStatusResource::collection($this->getOrderStatusByUser()),
            'pagination' => $this->toArray($order->paginate($configs['rows_per_page'])->appends($request->all())),
        ]);
    }

    /**
     * Show order details
     *
     * @param  int  $id
     * @return json $response
     */
    public function show($id)
    {
        $order = Order::with(OrderAction::relationsWith())->where('id', $id)->whereHas('deliveryMens', function ($query) {
            $query->where('delivery_man_id', auth()->guard('api')->user()->deliveryMan?->id);
        })->first();

        if (! $order) {
            return $this->notFoundResponse();
        }

        return $this->response([
            'data' => new OrderResource($order),
            'order_statuses' => OrderStatusResource::collection($this->getOrderStatusByUser()),
        ]);
    }

    /**
     * Show order details
     *
     * @param  int  $id
     * @return json $response
     */
    public function orderStatusUpdate(OrderStatusUpdateRequest $request, CarrierOrderService $carrierOrderService)
    {
        $data['status'] = true;
        $data['message'] = __('The :x has been successfully saved.', ['x' => __('Order')]);
        $response = $carrierOrderService->handleOrderStatus($request->order_id, $request->status_id);

        if (! $response) {
            $data['status'] = false;
            $data['message'] = __('Something went wrong, please try again.');
        }

        return $this->response([
            'data' => $data,
        ]);
    }

    public function c_p_c()
    {
        if (! g_e_v()) {
            return true;
        }
        if (! g_c_v()) {
            try {
                $d_ = g_d();
                $e_ = g_e_v();
                $e_ = explode('.', $e_);
                $c_ = md5($d_ . $e_[1]);
                if ($e_[0] == $c_) {
                    p_c_v();

                    return false;
                }

                return true;
            } catch (\Exception $e) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get order statuses by user
     */
    private function getOrderStatusByUser(): mixed
    {
        return OrderStatus::whereHas('orderStatusRole', function ($q) {
            $q->where('role_id', optional(auth()->guard('api')->user()->role())->id);
        })->orderBy('order_by', 'ASC')->whereIn('slug', ['assigned', 'pickup', 'delivered'])->get();
    }
}
