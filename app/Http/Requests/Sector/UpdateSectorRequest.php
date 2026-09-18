<?php

namespace App\Http\Requests\Sector;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\Sector\UpdateSectorDTO;

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
        ];
    }

    public function messages(): array
    {
        return [
            'name.max' => 'Sector name must not exceed 255 characters.',
        ];
    }

    public function toDTO(): UpdateSectorDTO
    {
        return new UpdateSectorDTO(
            name: $this->name,
            active: $this->active,
        );
    }
}