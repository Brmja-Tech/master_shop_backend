<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $result = [
            'name' => ['required', 'min:2', 'max:60'],
            'email' => ['required', 'email', Rule::unique('admins', 'email')->ignore($this->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'status' => ['required', 'in:1,0'],
        ];
        if (in_array($this->method(), ['PATCH', 'PUT'])) {
            $result['password'] = ['nullable', 'min:8', 'max:150', 'confirmed'];
            $result['password_confirmation'] = ['nullable'];
        } else {
            $result['password'] = ['required', 'min:8', 'max:150', 'confirmed'];
            $result['password_confirmation'] = ['required'];
        }

        return $result;
    }
}
