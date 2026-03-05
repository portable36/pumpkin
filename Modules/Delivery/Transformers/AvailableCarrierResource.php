<?php

namespace Modules\Delivery\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class AvailableCarrierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'unique_id' => $this->unique_id,
            'name' => $this->user->name,
            'assigned_order_count' => $this->assignedOrders()->count(),
        ];
    }
}
