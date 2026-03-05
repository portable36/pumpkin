<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request = [])
    {
        return [
            'total_withdrawal' => $this['user']->transactions->where('transaction_type', 'Delivery_withdrawal')->sum('total_amount'),
            'total_balance' => $this['user']->wallet()->balance,
            'total_earned' => $this['total_earned'],
            'total_order_delivered' => $this['user']->deliveryMan->orders->count(),
            'total_order_delivered' => $this['user']->deliveryMan->orders->whereIn('order_status_id', getOrderStatusIds(['completed', 'delivered']))->count(),
        ];
    }
}
