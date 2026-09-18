<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToEnterprise
{
    protected static function bootBelongsToEnterprise(): void
    {
        static::addGlobalScope('enterprise', function (Builder $query) {
            if (!auth()->check()) {
                return;
            }

            if (auth()->user()->isSuperAdmin()) {
                return;
            }

            $query->where(
                'enterprise_id',
                auth()->user()->enterprise_id
            );
        });
    }
}