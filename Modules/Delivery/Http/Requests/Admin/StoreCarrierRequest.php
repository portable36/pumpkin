<?php

namespace Modules\Delivery\Http\Requests\Admin;

use App\Rules\CheckValidEmail;
use App\Rules\CheckValidPhone;
use App\Rules\StrengthPassword;
use Illuminate\Foundation\Http\FormRequest;

class StoreCarrierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:191',
            'email' => ['required', 'max:191', 'unique:users,email', new CheckValidEmail()],
            'password' => ['required', new StrengthPassword()],
            'phone' => ['required', 'unique:users,phone', 'min:10', 'max:45', new CheckValidPhone()],
            'gender' => 'required',
            'address' => 'required|max:191',
            'file_id'  => ['nullable', 'array'],
            'file_id.*'  => ['nullable', 'numeric'],
            'license_status' => 'required|in:pending,verified,invalid',
            'is_active' => 'required|in:0,1',
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
