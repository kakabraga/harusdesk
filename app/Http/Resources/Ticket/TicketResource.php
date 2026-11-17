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
            'requester' => $this->requester ? [
                'id' => $this->requester->id,
                'name' => $this->requester->name,
                'email' => $this->requester->email,
            ] : null,
            'requester_name' => $this->requester?->name,
            'attendant_id' => $this->attendant_id,
            'attendant' => $this->attendant ? [
                'id' => $this->attendant->id,
                'name' => $this->attendant->name,
                'email' => $this->attendant->email,
            ] : null,
            'attendant_name' => $this->attendant?->name,
            'sector' => $this->sector ? [
                'id' => $this->sector->id,
                'name' => $this->sector->name,
            ] : null,
            'sector_name' => $this->sector?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'attachments' => AttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}
