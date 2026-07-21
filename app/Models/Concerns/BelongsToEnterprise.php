<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToEnterprise
{
    protected static function bootBelongsToEnterprise(): void
    {
        static::addGlobalScope('enterprise', function (Builder $query) {
            if (auth()->check()) {
                $query->where('enterprise_id', auth()->user()->enterprise_id);
            }
        });
    }
}