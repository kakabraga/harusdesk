<?php

namespace App\Http\Requests\Ticket;

use App\DTOs\Ticket\ListTicketDTO;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enterpriseId = auth()->user()?->isSuperAdmin() ? $this->input('enterprise_id') : null;

        return [
            'status' => ['nullable', 'string', 'in:open,in_progress,concluded,cancelled,reopened'],
            'priority' => ['nullable', 'string', 'in:low,medium,high'],
            'sector_id' => ['nullable', 'integer', Rule::existsForTenant('sectors', 'id', $enterpriseId)],
            'enterprise_id' => [
                'nullable',
                'integer',
                auth()->user()?->isSuperAdmin() ? 'exists:enterprises,id' : 'prohibited',
            ],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status must be one of: open, in_progress, concluded, cancelled, reopened.',
            'priority.in' => 'Priority must be one of: low, medium, high.',
            'sector_id.exists' => 'The selected sector does not exist.',
            'enterprise_id.prohibited' => 'You are not allowed to filter by enterprise_id.',
            'per_page.min' => 'per_page must be at least 1.',
            'per_page.max' => 'per_page cannot exceed 100.',
        ];
    }

    public function toDTO(): ListTicketDTO
    {
        return new ListTicketDTO(
            status: $this->has('status') ? (string) $this->status : null,
            priority: $this->has('priority') ? (string) $this->priority : null,
            sectorId: $this->has('sector_id') ? (int) $this->sector_id : null,
            enterpriseId: auth()->user()?->isSuperAdmin()
                ? ($this->has('enterprise_id') ? (int) $this->enterprise_id : null)
                : (int) auth()->user()?->enterprise_id,
            perPage: (int) $this->input('per_page', 15),
        );
    }
}
