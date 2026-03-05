<?php

namespace App\Http\Requests\Admin\Customer;

use App\Rules\CheckValidEmail;
use App\Rules\CheckValidPhone;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $vendorId = $this->input('vendor_id');
        $customerId = $this->customer->id ?? null;

        return [
            'vendor_id'    => 'required|integer|exists:vendors,id',
            'name'         => 'required|min:1|max:191',
            'company_name' => 'nullable|min:3|max:191',
            'address_1'    => 'required|min:3|max:191',
            'address_2'    => 'nullable|min:3|max:191',
            'country'      => 'required|max:100',
            'state'        => 'nullable|max:100',
            'city'         => 'required|max:100',
            'zip'          => 'required|min:3|max:100',
            'email'        => ['nullable', 'max:191', new CheckValidEmail()],
            'phone'        => [
                'required',
                'min:7',
                'max:15',
                new CheckValidPhone(),
                \Illuminate\Validation\Rule::unique('customers', 'phone')
                    ->where('vendor_id', $vendorId)
                    ->ignore($customerId),
            ],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'phone.unique' => __('The phone number has already been taken.'),
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Concatenate dial_code and phone into phone
        if ($this->has(['dial_code', 'phone'])) {
            $this->merge([
                'phone' => $this->input('dial_code') . $this->input('phone'),
            ]);
        }
    }
}
