<?php

namespace App\Http\Requests\Sector;

use App\DTOs\Sector\CreateSectorDTO;
use Illuminate\Foundation\Http\FormRequest;

class StoreSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'active' => ['boolean'],
            'enterprise_id' => [
                auth()->user()->isSuperAdmin() ? 'required' : 'prohibited',
                'exists:enterprises,id',
            ],
            'accepts_tickets' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Sector name is required.',
            'name.max' => 'Sector name must not exceed 255 characters.',
            'active.boolean' => 'Active must be a boolean.',
            'accepts_tickets.boolean' => 'Accepts tickets must be a boolean.',
        ];
    }

    public function toDTO(): CreateSectorDTO
    {
        return new CreateSectorDTO(
            enterpriseId: auth()->user()->isSuperAdmin()
                ? (int) $this->enterprise_id
                : (int) auth()->user()->enterprise_id,
            name: (string) $this->name,
            active: $this->has('active') ? $this->boolean('active') : true,
            acceptsTickets: $this->has('accepts_tickets') ? $this->boolean('accepts_tickets') : false
        );
    }
}
