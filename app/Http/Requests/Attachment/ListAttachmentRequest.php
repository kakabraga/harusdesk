<?php

namespace App\Http\Requests\Attachment;

use App\DTOs\Attachment\ListAttachmentDTO;
use Illuminate\Foundation\Http\FormRequest;

class ListAttachmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'enterprise_id' => [
                'nullable',
                'integer',
                $this->user()?->isSuperAdmin() ? 'exists:enterprises,id' : 'prohibited',
            ],
            'sector_id' => ['nullable', 'integer', 'exists:sectors,id'],
            'ticket_id' => ['nullable', 'integer', 'exists:tickets,id'],
            'interaction_id' => ['nullable', 'integer', 'exists:interactions,id'],
            'original_name' => ['nullable', 'string', 'max:255'],
            'mime_type' => ['nullable', 'string', 'max:100'],
            'min_size_kb' => ['nullable', 'integer', 'min:0'],
            'max_size_kb' => ['nullable', 'integer', 'min:0', 'gte:min_size_kb'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'enterprise_id.exists' => 'The selected enterprise does not exist.',
            'enterprise_id.prohibited' => 'You are not allowed to filter by enterprise_id.',
            'sector_id.exists' => 'The selected sector does not exist.',
            'ticket_id.exists' => 'The selected ticket does not exist.',
            'interaction_id.exists' => 'The selected interaction does not exist.',
            'max_size_kb.gte' => 'The max_size_kb must be greater than or equal to min_size_kb.',
            'per_page.min' => 'per_page must be at least 1.',
            'per_page.max' => 'per_page cannot exceed 100.',
        ];
    }

    public function toDTO(): ListAttachmentDTO
    {
        return new ListAttachmentDTO(
            enterpriseId: auth()->user()?->isSuperAdmin()
            ? ($this->has('enterprise_id') ? (int) $this->enterprise_id : null)
            : (int) auth()->user()?->enterprise_id,
            sectorId: $this->has('sector_id') ? (int) $this->sector_id : null,
            ticketId: $this->has('ticket_id') ? (int) $this->ticket_id : null,
            interactionId: $this->has('interaction_id') ? (int) $this->interaction_id : null,
            originalName: $this->has('original_name') ? (string) $this->original_name : null,
            mimeType: $this->has('mime_type') ? (string) $this->mime_type : null,
            minSizeKb: $this->has('min_size_kb') ? (int) $this->min_size_kb : null,
            maxSizeKb: $this->has('max_size_kb') ? (int) $this->max_size_kb : null,
            perPage: (int) $this->input('per_page', 15),
        );
    }
}
