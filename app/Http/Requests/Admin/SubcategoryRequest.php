<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->route('id') !== null
            || $this->isMethod('put')
            || $this->isMethod('patch')
            || in_array(strtolower((string) $this->input('_method')), ['put', 'patch'], true);

        $storeTypeRule = $isUpdate
            ? ['sometimes', 'integer', 'exists:store_types,id']
            : ['required', 'integer', 'exists:store_types,id'];

        return [
            'store_type_id' => $storeTypeRule,
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
        ];
    }
}
