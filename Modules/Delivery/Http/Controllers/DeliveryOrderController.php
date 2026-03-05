<?php

namespace Modules\Delivery\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Delivery\Entities\{
    DeliveryMan,
    DeliveryManOrder
};
use Illuminate\Contracts\Support\Renderable;

class DeliveryOrderController extends Controller
{
    /**
     * Display rendered view for assigning delivery man
     *
     * @return Renderable
     */
    public function assignCarrierView(Request $request)
    {
        if ($request->ajax() && $request->has('order_id')) {
            $carriers = DeliveryMan::with(['user', 'assignedOrders', 'orders'])
                ->whereHas('user', function ($query) {
                    $query->where('status', 'Active');
                })
                ->where('license_status', 'verified')
                ->where('is_active', 1)
                ->limit(10)
                ->get();

            $carrierOrder = DeliveryManOrder::where('order_id', $request->order_id)
                ->with(['deliveryMan', 'deliveryMan.user'])
                ->first();

            return view('delivery::renderable.admin.assign-carrier', compact('carriers', 'carrierOrder'))->render();
        }

        abort(404);
    }
}
