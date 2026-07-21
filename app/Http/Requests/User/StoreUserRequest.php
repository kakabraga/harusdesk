<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use App\DTOs\User\CreateUserDTO;
class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enterprise_id' => ['required', 'integer', 'exists:enterprises,id'],

            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],

            'role' => [
                'required',
                'string',
                'in:admin,attendant,user',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'enterprise_id.required' => 'A empresa é obrigatória.',
            'enterprise_id.exists' => 'A empresa informada não existe.',

            'name.required' => 'O nome é obrigatório.',

            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.unique' => 'Este e-mail já está em uso.',

            'password.required' => 'A senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',

            'role.required' => 'O perfil é obrigatório.',
            'role.in' => 'O perfil informado é inválido.',
        ];
    }

    public function toDto(): CreateUserDTO
    {
        return new CreateUserDTO(
            enterprise_id: $this->enterprise_id,
            name: $this->name,
            email: $this->email,
            password: $this->password,
            newPassword: $this->newPassword,
            role: $this->role,
        );
    }
}