<?php

namespace Modules\Delivery\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class OrderStatusUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'order_id' => ['required', 'string', 'integer', 'exists:orders,id'],
            'status_id' => ['required', 'string', 'integer', 'exists:order_statuses,id'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
