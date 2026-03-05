<?php

namespace Modules\Delivery\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawalResource extends JsonResource
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
            'currency_id' => $this->currency_id,
            'currency' => $this->currency?->name,
            'status' => $this->status,
            'is_active' => $this->is_active,
            'withdrawal_method_id' => $this->withdrawal_method_id,
            'withdrawal_method' => $this->withdrawalMethod?->method_name,
            'amount' => $this->amount,
            'charge_amount' => $this->charge_amount,
            'total_amount' => $this->total_amount,
            'transaction_type' => $this->transaction_type,
            'transaction_date' => $this->transaction_date,
            'commission_amount' => $this->commission_amount,
            'created_at' => timeZoneFormatDate($this->created_at),
            'updated_at' => timeZoneFormatDate($this->updated_at),
        ];
    }
}
