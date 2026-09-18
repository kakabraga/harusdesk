<?php

namespace App\Http\Requests\Sector;

use Illuminate\Foundation\Http\FormRequest;
use App\DTOs\Sector\CreateSectorDTO;

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
                'exists:enterprises,id'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Sector name is required.',
            'name.max' => 'Sector name must not exceed 255 characters.',
        ];
    }

    public function toDTO(): CreateSectorDTO
    {
        return new CreateSectorDTO(
            name: $this->name,
            enterpriseId: auth()->user()->isSuperAdmin()
            ? $this->enterprise_id :
            auth()->user()->enterprise_id,
        );
    }
}