<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToEnterprise;

class Plan extends Model
{
    use BelongsToEnterprise;
    protected $fillable = [
        'name',
        'max_users',
        'max_tickets_per_month',
        'storage_mb',
        'price',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'price' => 'decimal:2',
        'max_users' => 'integer',
        'max_tickets_per_month' => 'integer',
        'storage_mb' => 'integer',
    ];

    public function enterprises(): HasMany
    {
        return $this->hasMany(Enterprise::class);
    }
}