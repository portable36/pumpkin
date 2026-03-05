<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalPaymentMethodResource extends JsonResource
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
            'user_id' => $this->user_id,
            'withdrawal_method_id' => $this->withdrawal_method_id,
            'param' => json_decode($this->param, true),
            'is_default' => $this->is_default,
        ];
    }
}
