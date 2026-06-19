<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VendorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['owner_name', 'store_name', 'description', 'phone', 'address_description', 'working_hours', 'temp_token'] as $field) {
            if ($this->has($field)) {
                $data[$field] = $this->input($field) !== null ? trim((string) $this->input($field)) : null;
            }
        }

        foreach (['store_type_id'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : (int)$value;
            }
        }

        foreach (['latitude', 'longitude', 'rate'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : (float)$value;
            }
        }

        foreach (['is_active', 'is_store_open', 'is_accepting_orders', 'is_verified'] as $field) {
            if ($this->has($field)) {
                $data[$field] = filter_var(
                    $this->input($field),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                );
            }
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $vendor = $this->route('vendor');
        $vendorId = is_object($vendor) ? $vendor->id : $vendor;
        $isUpdate = $vendorId !== null
            || $this->isMethod('put')
            || $this->isMethod('patch')
            || in_array(strtolower((string) $this->input('_method')), ['put', 'patch'], true);

        return [
            'owner_name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'phone' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                Rule::unique('vendors', 'phone')->ignore($vendorId),
            ],
            'password' => [$isUpdate ? 'nullable' : 'required', 'string', 'min:6'],
            'store_name' => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'store_type_id' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:store_types,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'address_description' => ['nullable', 'string'],
            'logo' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'rate' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'is_active' => ['sometimes', 'boolean'],
            'is_store_open' => ['sometimes', 'boolean'],
            'is_accepting_orders' => ['sometimes', 'boolean'],
            'working_hours' => ['nullable', 'string'],
            'work_from' => ['sometimes', 'nullable', 'date_format:H:i'],
            'work_to' => ['sometimes', 'nullable', 'date_format:H:i'],
            'is_verified' => ['sometimes', 'boolean'],
        ];
    }
}
