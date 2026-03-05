<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CarrierUpdateStatusResource extends JsonResource
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
            'license_status' => $this->license_status,
            'is_active' => $this->is_active,
            'created_at' => timeZoneFormatDate($this->created_at),
            'updated_at' => timeZoneFormatDate($this->updated_at),
        ];
    }
}
