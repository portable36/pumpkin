<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminLocationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vendor_id' => ['required', 'exists:vendors,id'],
            'slug' => [
                'required',
                Rule::unique('locations')
                    ->where(fn ($q) => $q->where('vendor_id', $this->vendor_id))
                    ->ignore($this->id),
            ],
            'email' => [
                'nullable', 'email',
                Rule::unique('locations')
                    ->where(fn ($q) => $q->where('vendor_id', $this->vendor_id))
                    ->ignore($this->id),
            ],
        ];
    }
}
