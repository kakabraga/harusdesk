<?php

namespace App\Http\Requests\Sector;

use App\DTOs\Sector\UpdateSectorDTO;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSectorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'accepts_tickets' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Sector name must not exceed 255 characters.',
            'active.boolean' => 'Active must be a boolean.',
            'accepts_tickets.boolean' => 'Accepts tickets must be a boolean.',
        ];
    }

    public function toDTO(): UpdateSectorDTO
    {
        return new UpdateSectorDTO(
            name: $this->has('name') ? (string) $this->name : null,
            active: $this->has('active') ? $this->boolean('active') : null,
            acceptsTickets: $this->has('accepts_tickets') ? $this->boolean('accepts_tickets') : null
        );
    }
}
