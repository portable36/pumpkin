<?php

namespace Modules\Delivery\Http\Controllers\vendor;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Support\Renderable;
use Modules\Delivery\Entities\DeliveryMan;
use Modules\Delivery\Entities\DeliveryManOrder;

class DeliveryOrderController extends Controller
{
    /**
     * Display rendered view for assigning delivery man
     *
     * @param  Request  $request
     * @return Renderable
     */
    public function assignCarrierView(Request $request)
    {
        if ($request->ajax() && $request->has('order_id')) {
            $carriers = DeliveryMan::query()
                ->with(['user', 'assignedOrders'])
                ->where('license_status', '=', 'verified')
                ->where('is_active', '=', '1')
                ->limit(10)->get();

            $carrierOrder = DeliveryManOrder::where('order_id', '=', $request->order_id)
                ->with(['deliveryMan', 'deliveryMan.user'])
                ->first();

            return view('delivery::renderable.admin.assign-carrier', compact('carriers', 'carrierOrder'))->render();
        }

        abort(404);
    }
}
