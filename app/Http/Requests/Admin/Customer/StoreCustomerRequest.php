<?php

namespace App\Http\Requests\Admin\Customer;

use App\Rules\CheckValidEmail;
use App\Rules\CheckValidPhone;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
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
            'dial_code'    => 'required|min:1|max:5',
            'email'        => ['nullable', 'max:191', new CheckValidEmail()],
            'phone'        => [
                'required',
                'min:7',
                'max:15',
                new CheckValidPhone(),
                function ($attribute, $value, $fail) {
                    $vendorId = $this->input('vendor_id');
                    if (
                        ! empty($vendorId) &&
                        \App\Models\Customer::where('phone', $value)
                            ->where('vendor_id', $vendorId)
                            ->exists()
                    ) {
                        $fail(__('The phone number has already been taken.'));
                    }
                },
            ],
            'status'       => 'required|in:Pending,Active,Inactive,Deleted',
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
