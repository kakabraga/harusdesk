<?php

namespace App\Http\Resources\Ticket;

use App\Http\Resources\Attachment\AttachmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'status' => $this->status,
            'has_attachments' => (bool) $this->has_attachments,
            'sector_id' => $this->sector_id,
            'enterprise_id' => $this->enterprise_id,
            'requester_id' => $this->requester_id,
            'attendant_id' => $this->attendant_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}
