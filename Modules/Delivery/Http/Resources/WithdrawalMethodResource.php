<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalMethodResource extends JsonResource
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
            'method_name' => $this->method_name,
            'params' => $this->params,
            'is_default' => $this->withdrawalSetting?->count() > 0 ? $this->withdrawalSetting?->is_default : 0,
            'status' => $this->status,
        ];
    }
}
