<?php

namespace App\Http\Resources\Attachment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
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
            'ticket_id' => $this->ticket_id,
            'interaction_id' => $this->interaction_id,
            'original_name' => $this->original_name,
            'path' => $this->path,
            'mime_type' => $this->mime_type,
            'size_kb' => $this->size_kb,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
