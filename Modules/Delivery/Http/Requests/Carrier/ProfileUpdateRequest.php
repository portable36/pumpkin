<?php

namespace Modules\Delivery\Http\Requests\Carrier;

use App\Rules\CheckValidEmail;
use App\Rules\CheckValidFile;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'min:3', 'max:191'],
            'email' => ['required', 'max:191', 'unique:users,email,' . $this->get('id'), new CheckValidEmail()],
            'phone' => ['nullable', 'max:45', 'regex:/^[0-9\-\,]*$/'],
            'birthday' => ['nullable', 'regex:/^[0-9]{1,4}[\/\-\.]{1}[0-9]{1,2}[\/\-\.]{1}[0-9]{1,4}$/'],
            'address' => ['nullable', 'max:200'],
            'gender' => ['nullable', 'max:6', 'in:Male,Female'],
            'image'  => ['nullable', new CheckValidFile(getFileExtensions(2)), 'max:' . preference('file_size') * 1024],
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
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gender.in' => __('Gender must be either Male or Female'),
            'image.max' => __('Maximum File Size :x MB.', ['x' => preference('file_size')]),
        ];
    }
}
