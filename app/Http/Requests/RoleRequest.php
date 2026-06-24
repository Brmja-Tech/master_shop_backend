<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class RoleRequest extends FormRequest
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
        $roleId = $this->route('role');
        return [
            'role.ar' => [
                'required',
                'string',
                'max:100',
                $roleId ? 'unique:roles,role->ar,' . $roleId : 'unique:roles,role->ar'
            ],
            'role.en' => [
                'required',
                'string',
                'max:100',
                $roleId ? 'unique:roles,role->en,' . $roleId : 'unique:roles,role->en'
            ],
            'permession'=>['required' , 'array' , 'min:1']
        ];
    }
}
