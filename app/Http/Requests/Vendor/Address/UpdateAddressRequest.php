<?php

namespace App\Http\Requests\Vendor\Address;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
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
            'company_name' => 'nullable|min:3|max:191',
            'address_1'    => 'required|min:3|max:191',
            'address_2'    => 'nullable|min:3|max:191',
            'country'      => 'required|max:100',
            'state'        => 'nullable|max:100',
            'city'         => 'required|max:100',
            'zip'          => 'required|min:3|max:100',
        ];
    }
}
