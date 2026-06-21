<?php

namespace App\Http\Requests\Delivery\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => trim((string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'phone' => ['required', 'string', 'max:25'],
            'password' => ['required', 'string', 'min:6'],
            'fcm_token' => ['nullable', 'string'],
        ];
    }
}
