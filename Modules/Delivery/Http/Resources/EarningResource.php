<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EarningResource extends JsonResource
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
            'id' => $this->id,
            'order_id' => $this->order_id,
            'order_reference' => $this?->order?->reference,
            'order_amount' => $this?->order?->total,
            'reference_number' => $this->reference_number,
            'total_amount' => $this->total_amount,
            'transaction_date' => $this->transaction_date,
            'created_at' => $this->created_at,
        ];
    }
}
