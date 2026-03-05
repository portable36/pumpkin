<?php

namespace Modules\Delivery\Http\Requests\Api\V1;

use App\Rules\CheckValidEmail;
use Illuminate\Foundation\Http\FormRequest;

class PaypalSettingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'method_id' => ['required', 'exists:withdrawal_methods,id'],
            'email' => ['required', 'max:99', new CheckValidEmail()],
            'is_default' => ['required'],
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
