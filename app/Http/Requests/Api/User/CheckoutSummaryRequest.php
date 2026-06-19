<?php

namespace App\Http\Requests\Api\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address_id' => [
                'nullable',
                'integer',
                Rule::exists('user_addresses', 'id')->where(
                    fn ($query) => $query->where('user_id', auth('sanctum')->id())
                ),
            ],
            'delivery_latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_without:address_id'],
            'delivery_longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_without:address_id'],
        ];
    }
}
