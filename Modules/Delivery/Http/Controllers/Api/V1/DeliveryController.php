<?php

namespace Modules\Delivery\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Modules\Delivery\Entities\DeliveryMan;
use Modules\Delivery\Http\Resources\CarrierUpdateStatusResource;

class DeliveryController extends Controller
{
    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function updateStatus()
    {
        $deliveryMan = DeliveryMan::where('user_id', auth()->guard('api')->user()->id)->first();

        if (request()->status === '1' || request()->status === '0') {
            $deliveryMan->is_active = request()->status;
            $deliveryMan->save();
        }

        if (! $deliveryMan) {
            return $this->notFoundResponse();
        }

        return $this->response([
            'data' => new CarrierUpdateStatusResource($deliveryMan),
        ]);
    }
}
