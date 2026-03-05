<?php

namespace Modules\Delivery\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class BankSettingRequest extends FormRequest
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
            'account_holder_name' => ['required', 'string'],
            'branch_name' => ['required', 'string'],
            'bank_account_number' => ['required', 'string'],
            'branch_city' => ['required', 'string'],
            'swift_code' => ['required', 'max:12', 'min:8'],
            'branch_address' => ['required', 'string'],
            'bank_name' => ['required', 'string'],
            'country' => ['required', 'string'],
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
