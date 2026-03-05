<?php

namespace Modules\Delivery\Http\Controllers\Carrier;

use App\Models\{
    Order,
    Transaction
};
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\Delivery\Entities\DeliveryMan;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $orderStatusCounts = Order::query()
            ->selectRaw('order_status_id, COUNT(*) as count')
            ->whereHas('deliveryMens', function ($query) {
                $query->where('delivery_man_id', optional(auth()->user()->deliveryMan)->id);
            })
            ->groupBy('order_status_id')
            ->get()
            ->keyBy('order_status_id');

        $totalEarning = Transaction::where('reference_number', auth()->user()->id)
            ->where('transaction_type', 'delivery_commission')
            ->where('reference_type', 'delivery_man_user_id')
            ->sum('total_amount');

        $data = [
            'totalCompletedOrder' => $orderStatusCounts->get(getOrderStatusId('completed'))['count'] ?? 0,
            'totalAssignOrder' => $orderStatusCounts->get(getOrderStatusId('assigned'))['count'] ?? 0,
            'totalPickupOrder' => $orderStatusCounts->get(getOrderStatusId('pickup'))['count'] ?? 0,
            'totalDeliveredOrder' => $orderStatusCounts->get(getOrderStatusId('delivered'))['count'] ?? 0,
            'balance' => optional(auth()->user()->wallet())->balance ?? 0,
            'earning' => $totalEarning,
        ];

        return view('delivery::carrier.dashboard', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Renderable
     */
    public function create()
    {
        return view('delivery::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('delivery::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('delivery::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Update the specified resource.
     *
     * @param  int  $id
     * @return Renderable
     */
    public function status()
    {
        $DeliveryMan = DeliveryMan::where('user_id', auth()->user()->id)->first();
        $DeliveryMan->is_active = ! $DeliveryMan->is_active;
        $DeliveryMan->save();

        return response()->json($DeliveryMan);
    }
}
