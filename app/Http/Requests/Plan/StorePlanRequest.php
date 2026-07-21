<?php

namespace App\Http\Requests\Plan;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\Plan\PlanCreateDTO;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:plans,name'],
            'max_users' => ['required', 'integer', 'min:1'],
            'max_tickets_per_month' => ['required', 'integer', 'min:1'],
            'storage_mb' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Plan name is required.',
            'name.unique' => 'This plan name is already registered.',
            'name.max' => 'Plan name must not exceed 255 characters.',

            'max_users.required' => 'Maximum users is required.',
            'max_users.integer' => 'Maximum users must be an integer.',
            'max_users.min' => 'Maximum users must be at least 1.',

            'max_tickets_per_month.required' => 'Maximum tickets per month is required.',
            'max_tickets_per_month.integer' => 'Maximum tickets per month must be an integer.',
            'max_tickets_per_month.min' => 'Maximum tickets per month must be at least 1.',

            'storage_mb.required' => 'Storage limit is required.',
            'storage_mb.integer' => 'Storage limit must be an integer.',
            'storage_mb.min' => 'Storage limit must be at least 1 MB.',

            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',

            'active.boolean' => 'Active must be true or false.',
        ];
    }

    public function toDTO(): PlanCreateDTO
    {
        return new PlanCreateDTO(
            name: $this->name,
            max_users: $this->max_users,
            max_tickets_per_month: $this->max_tickets_per_month,
            storage_mb: $this->storage_mb,
            price: $this->price,
            active: $this->boolean('active', true),
        );
    }
}