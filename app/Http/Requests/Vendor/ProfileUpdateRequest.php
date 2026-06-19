<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['store_name', 'description'] as $field) {
            if ($this->has($field)) {
                $data[$field] = trim((string) $this->input($field));
            }
        }

        foreach (['rate', 'store_type_id'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        if ($this->has('is_active')) {
            $data['is_active'] = filter_var(
                $this->input('is_active'),
                FILTER_VALIDATE_BOOLEAN,
                FILTER_NULL_ON_FAILURE
            );
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        return [
            'store_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'store_type_id' => ['sometimes', 'integer', 'exists:store_types,id'],
            'rate' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'logo' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'work_from' => ['sometimes', 'nullable', 'date_format:H:i'],
            'work_to' => ['sometimes', 'nullable', 'date_format:H:i'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
