<?php

namespace App\Http\Requests\Vendor\Auth;
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
            'phone'     => 'required|string|exists:vendors,phone',
            'password'  => 'required|string|min:8',
        
        ];
    }
}
