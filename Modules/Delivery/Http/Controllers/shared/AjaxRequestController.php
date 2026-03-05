<?php

namespace Modules\Delivery\Http\Controllers\shared;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Modules\Delivery\Entities\{
    DeliveryMan,
    DeliveryManOrder
};
use Modules\Delivery\Services\CarrierOrderService;
use Modules\Delivery\Services\Mail\AssigneeMailService;
use Modules\Delivery\Transformers\AvailableCarrierResource;

class AjaxRequestController extends Controller
{
    /**
     * Find available carriers using query string
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection|\Illuminate\Http\JsonResponse
     */
    public function searchAvailableCarrier(Request $request)
    {
        if ($request->query->has('q')) {
            $carriers = DeliveryMan::query()
                ->with('user')
                ->whereLike('unique_id', $request->q)
                ->orWhereHas('user', function ($query) use ($request) {
                    $query->where('name', 'LIKE', '%' . $request->q . '%');
                })
                ->limit(10)->get();

            $carriers = $carriers->where('license_status', '=', 'verified')
                ->where('is_active', '=', '1');

            return AvailableCarrierResource::collection($carriers);
        }

        return response()->json(['data' => []], 404);
    }

    /**
     * Assign carrier to order
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignCarrier(Request $request)
    {
        $response = ['is_assigned' => false, 'message' => __('Something went wrong! Please refresh the whole page & try again.')];

        if ($request->has('order_id') && $request->has('delivery_man_id')) {
            $deliveryManOrder =  DeliveryManOrder::where('order_id', $request->order_id)->where('delivery_man_id', $request->delivery_man_id)->first();

            if ($deliveryManOrder) {
                return $response = ['is_assigned' => true, 'message' => __('This has already been assigned to him.')];
            }

            DeliveryManOrder::where('order_id', $request->order_id)->delete();
            $is_inserted = (new DeliveryManOrder())->insert($request->only(['order_id', 'delivery_man_id']));
            $orderStatusId = OrderStatus::getAll()->where('slug', 'assigned')->sortBy('order_by')->pluck('id')->first();
            (new CarrierOrderService())->handleOrderStatus($request->order_id, $orderStatusId);

            if (preference('notification_type_delivery_man') == 'mail') {
                (new AssigneeMailService())->send($request);
            }

            if ($is_inserted) {
                $response = ['is_assigned' => true, 'message' => __('Assigned successfully! Refresh to see effect.')];
            }
        }

        return response()->json($response);
    }
}
