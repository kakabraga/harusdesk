<?php

namespace App\Http\Requests\Ticket;

use App\DTOs\Ticket\UpdateTicketDTO;
use App\Rules\SectorAcceptsTickets;
use App\Rules\ValidAttendant;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $enterpriseId = $this->route('ticket')?->enterprise_id;

        return [
            'sector_id' => ['sometimes', 'integer', new SectorAcceptsTickets($enterpriseId)],
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'status' => ['sometimes', 'string', 'in:open,in_progress,concluded,cancelled,reopened'],
            'attendant_id' => ['sometimes', 'nullable', 'integer', new ValidAttendant($enterpriseId)],
        ];
    }

    public function messages(): array
    {
        return [
            'sector_id.exists' => 'The selected sector does not exist.',
            'title.max' => 'The ticket title must not exceed 255 characters.',
            'priority.in' => 'Priority must be one of: low, medium, high.',
            'status.in' => 'Status must be one of: open, in_progress, concluded, cancelled, reopened.',
            'attendant_id.exists' => 'The selected attendant does not exist.',
        ];
    }

    public function toDTO(): UpdateTicketDTO
    {
        return new UpdateTicketDTO(
            sectorId: $this->has('sector_id') ? (int) $this->sector_id : null,
            title: $this->has('title') ? (string) $this->title : null,
            description: $this->has('description') ? (string) $this->description : null,
            priority: $this->has('priority') ? (string) $this->priority : null,
            status: $this->has('status') ? (string) $this->status : null,
            attendantId: $this->has('attendant_id') ? ($this->attendant_id ? (int) $this->attendant_id : null) : null,
        );
    }
}
