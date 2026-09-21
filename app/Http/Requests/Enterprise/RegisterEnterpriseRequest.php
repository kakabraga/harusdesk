<?php

namespace App\Http\Requests\Enterprise;

use App\DTOs\Enterprise\EnterpriseDTO;
use App\DTOs\Enterprise\RegisterEnterpriseDTO;
use App\DTOs\User\UserDTO;
use Illuminate\Foundation\Http\FormRequest;

class RegisterEnterpriseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Enterprise
            'enterprise.name' => ['required', 'string', 'max:255'],
            'enterprise.cnpj' => ['required', 'string', 'size:14', 'unique:enterprises,cnpj'],
            'enterprise.email' => ['required', 'email', 'max:255'],
            'enterprise.plan_id' => ['required', 'integer', 'exists:plans,id'],

            // User Admin
            'userAdmin.name' => ['required', 'string', 'max:255'],
            'userAdmin.email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'userAdmin.password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            // Enterprise
            'enterprise.name.required' => 'Enterprise name is required.',
            'enterprise.name.max' => 'Enterprise name must not exceed 255 characters.',

            'enterprise.cnpj.required' => 'Tax ID is required.',
            'enterprise.cnpj.size' => 'Tax ID must be exactly 14 characters.',
            'enterprise.cnpj.unique' => 'This Tax ID is already registered.',

            'enterprise.email.required' => 'Enterprise email is required.',
            'enterprise.email.email' => 'Please provide a valid enterprise email address.',

            'enterprise.plan_id.required' => 'Plan is required.',
            'enterprise.plan_id.exists' => 'Selected plan is invalid.',

            // User Admin
            'userAdmin.name.required' => 'Admin name is required.',
            'userAdmin.name.max' => 'Admin name must not exceed 255 characters.',

            'userAdmin.email.required' => 'Admin email is required.',
            'userAdmin.email.email' => 'Please provide a valid admin email address.',
            'userAdmin.email.unique' => 'This admin email is already registered.',

            'userAdmin.password.required' => 'Password is required.',
            'userAdmin.password.min' => 'Password must be at least 8 characters.',
            'userAdmin.password.confirmed' => 'Password confirmation does not match.',
        ];
    }

    public function toDTO(): RegisterEnterpriseDTO
    {
        return new RegisterEnterpriseDTO(
            enterprise: new EnterpriseDTO(
                name: $this->input('enterprise.name'),
                cnpj: $this->input('enterprise.cnpj'),
                email: $this->input('enterprise.email'),
                planId: $this->input('enterprise.plan_id'),
            ),

            userAdmin: new UserDTO(
                name: $this->input('userAdmin.name'),
                email: $this->input('userAdmin.email'),
                password: $this->input('userAdmin.password'),
            ),
        );
    }
}
