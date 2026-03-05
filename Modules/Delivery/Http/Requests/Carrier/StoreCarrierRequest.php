<?php

namespace Modules\Delivery\Http\Requests\Carrier;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use App\Rules\{
    CheckValidEmail,
    CheckValidPhone,
    StrengthPassword
};

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
            'name'      => ['required', 'min:3', 'max:80'],
            'email'     => ['required', 'max:99', 'unique:users,email', new CheckValidEmail()],
            'password'  => ['required', 'max:191', 'confirmed', new StrengthPassword()],
            'phone'     => ['required', 'min:10', 'max:45', new CheckValidPhone()],
            'gender'    => ['required', 'in:Male,Female'],
            'address'   => ['required', 'max:191'],
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

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'email' => __('Email Address'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'status' => 'Pending',
            'activation_code' => Str::random(10),
            'activation_otp' => random_int(1111, 9999),
            'is_active' => 1,
            'license_status' => 'Pending',
        ]);
    }
}
