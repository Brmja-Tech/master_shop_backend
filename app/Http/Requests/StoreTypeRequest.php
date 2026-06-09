<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('store_type');

        return [
            'name' => 'required|string|max:255|unique:store_types,name,' . $id,
        ];
    }
}
