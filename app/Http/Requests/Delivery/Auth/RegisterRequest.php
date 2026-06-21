<?php

namespace App\Http\Requests\Delivery\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => trim((string) $this->input('phone')),
            'email' => $this->filled('email') ? trim((string) $this->input('email')) : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:25', 'unique:delivery_users,phone'],
            'email' => ['nullable', 'email', 'max:255', 'unique:delivery_users,email'],
            'password' => ['required', 'string', 'min:6'],
            'img' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'front_ident' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'back_ident' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'personal_deriving_license' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'machine_license' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'fcm_token' => ['nullable', 'string'],
        ];
    }
}
