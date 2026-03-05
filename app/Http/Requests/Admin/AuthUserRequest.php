<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AuthUserRequest extends FormRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];
        if (array_key_exists('phone', $this->all())) {
            $rules['phone'] = 'required|exists:users';
        } else {
            $rules['email'] = 'required|email|exists:users';
        }
        $rules['password'] = 'required';
        $rules['gCaptcha'] = isRecaptchaActive() ? 'required|captcha' : 'nullable';

        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $data = ['gCaptcha' => $this['g-recaptcha-response']];
        if ($this->phone) {
            $data['phone'] = $this['dial_code'] . $this['phone'];
        }
        $this->merge($data);
    }
}
