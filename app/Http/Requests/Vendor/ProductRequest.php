<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        foreach (['price', 'discount'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        foreach (['quantity', 'remaining_quantity', 'subcategory_id'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        if ($this->has('delete_image_ids') && is_array($this->input('delete_image_ids'))) {
            $data['delete_image_ids'] = array_values(array_filter(
                $this->input('delete_image_ids'),
                fn ($value) => $value !== null && $value !== ''
            ));
        }

        if ($this->has('expiry_date')) {
            $value = trim((string) $this->input('expiry_date'));
            $data['expiry_date'] = $value === '' ? null : $value;
        }

        foreach (['name', 'description'] as $field) {
            if ($this->has($field)) {
                $value = trim((string) $this->input($field));
                $data[$field] = $value === '' ? null : $value;
            }
        }

        if ($this->has('subcategory_name') && is_string($this->input('subcategory_name'))) {
            $value = trim((string) $this->input('subcategory_name'));
            $data['subcategory_name'] = $value === '' ? null : $value;
        }

        if ($this->has('is_available')) {
            $data['is_available'] = filter_var($this->input('is_available'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }

        if ($data !== []) {
            $this->merge($data);
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->route('id') !== null
            || $this->isMethod('put')
            || $this->isMethod('patch')
            || in_array(strtolower((string) $this->input('_method')), ['put', 'patch'], true);

        $imageRule = $isUpdate ? ['sometimes', 'array'] : ['nullable', 'array'];
        $mainImageRule = $isUpdate
            ? ['sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120']
            : ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];
        $requiredOrSometimes = $isUpdate ? ['sometimes'] : ['required'];
        $requiredStringOrSometimes = $isUpdate ? ['sometimes', 'string'] : ['required', 'string'];
        $subcategoryIdRule = $isUpdate
            ? ['sometimes', 'nullable', 'integer', 'exists:subcategories,id']
            : ['nullable', 'integer', 'exists:subcategories,id', 'required_without:subcategory_name'];
        $subcategoryNameRule = $isUpdate
            ? ['sometimes', 'string', 'max:255']
            : ['nullable', 'string', 'max:255', 'required_without:subcategory_id'];

        return [
            'name' => array_merge($requiredStringOrSometimes, ['max:255']),
            'description' => $requiredStringOrSometimes,
            'subcategory_id' => $subcategoryIdRule,
            'subcategory_name' => $subcategoryNameRule,
            'quantity' => array_merge($requiredOrSometimes, ['integer', 'min:0']),
            'remaining_quantity' => ['nullable', 'integer', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_available' => ['nullable', 'boolean'],
            'unit' => array_merge($requiredOrSometimes, ['string', 'max:255']),
            'price' => array_merge($requiredOrSometimes, ['numeric', 'min:0']),
            'expiry_date' => ['nullable', 'date', 'after:today'],
            'main_image' => $mainImageRule,
            'images' => $imageRule,
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'delete_image_ids' => ['sometimes', 'array'],
            'delete_image_ids.*' => ['integer'],
        ];
    }
}
