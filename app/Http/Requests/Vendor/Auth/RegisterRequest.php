<?php

namespace App\Http\Requests\Vendor\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'owner_name' => ['required', 'string', 'max:255'],

            'phone' => [
                'required',
                'string',
                'unique:vendors,phone'
            ],

            'password' => [
                'required',
                'confirmed',
                'min:6'
            ],

            'store_name' => [
                'required',
                'string',
                'max:255'
            ],

            'store_type_id' => [
                'required',
                'exists:store_types,id'
            ],

            'latitude' => [
                'nullable',
                'numeric'
            ],

            'longitude' => [
                'nullable',
                'numeric'
            ],

            'address_description' => [
                'nullable',
                'string'
            ],

            'fcm_token' => [
                'nullable',
                'string'
            ],
        ];
    }
}
