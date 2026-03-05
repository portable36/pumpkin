<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LocationStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'slug' => [
                'required',
                Rule::unique('locations')->where(function ($query) {
                    return $query->where('vendor_id', request('vendor_id'));
                }),
            ],
            'email' => [
                'nullable',
                Rule::unique('locations')->where(function ($query) {
                    return $query->where('vendor_id', request('vendor_id'));
                }),
            ],
            'parent_id' => 'nullable|exists:locations,id',
            'vendor_id' => 'required|exists:vendors,id',
            'status' => 'required|in:Active,Inactive',
            'is_default' => 'required|in:0,1',
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
