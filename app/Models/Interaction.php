<?php

namespace App\Models;

use App\Models\Concerns\BelongsToEnterprise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interaction extends Model
{
    use BelongsToEnterprise;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'has_attachments',
    ];

    protected $casts = [
        'has_attachments' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
