<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEnterprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    use BelongsToEnterprise;

    protected $fillable = [
        'ticket_id',
        'interaction_id',
        'original_name',
        'path',
        'mime_type',
        'size_kb',
    ];

    protected $casts = [
        'size_kb' => 'integer',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function interaction(): BelongsTo
    {
        return $this->belongsTo(Interaction::class);
    }
}
