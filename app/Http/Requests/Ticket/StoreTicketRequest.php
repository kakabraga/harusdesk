<?php

namespace App\Http\Requests\Ticket;

use App\DTOs\Ticket\CreateTicketDTO;
use App\Rules\SectorAcceptsTickets;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enterpriseId = auth()->user()?->isSuperAdmin() ? (int) $this->input('enterprise_id') : null;

        return [
            'sector_id' => ['required', 'integer', new SectorAcceptsTickets($enterpriseId)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'enterprise_id' => [
                auth()->user()?->isSuperAdmin() ? 'required' : 'prohibited',
                'exists:enterprises,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'sector_id.required' => 'The sector is required.',
            'sector_id.exists' => 'The selected sector does not exist.',
            'title.required' => 'The ticket title is required.',
            'title.max' => 'The ticket title must not exceed 255 characters.',
            'description.required' => 'The ticket description is required.',
            'priority.in' => 'Priority must be one of: low, medium, high.',
        ];
    }

    public function toDTO(): CreateTicketDTO
    {
        return new CreateTicketDTO(
            enterpriseId: auth()->user()->isSuperAdmin()
                ? (int) $this->enterprise_id
                : (int) auth()->user()->enterprise_id,
            sectorId: (int) $this->sector_id,
            requesterId: (int) auth()->id(),
            title: (string) $this->title,
            description: (string) $this->description,
            priority: $this->input('priority', 'low'),
            status: 'open',
            attendantId: null,
        );
    }
}
