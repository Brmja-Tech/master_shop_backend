<?php

namespace App\Http\Requests\Vendor;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', new Enum(OrderStatus::class)],
            'cancellation_reason' => ['nullable', 'string', 'max:500'],
        ];
    }
}
