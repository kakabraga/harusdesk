<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Concerns\BelongsToEnterprise;

class User extends Authenticatable
{
    use HasApiTokens;
    use SoftDeletes;
    use BelongsToEnterprise;

    protected $fillable = [
        'enterprise_id',
        'name',
        'email',
        'password',
        'role',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'active' => 'boolean',
        'password' => 'hashed',
    ];

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class);
    }

    public function sectors(): BelongsToMany
    {
        return $this->belongsToMany(Sector::class);
    }

    public function ticketsAsRequester(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function ticketsAsAttendant(): HasMany
    {
        return $this->hasMany(Ticket::class, 'attendant_id');
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin()
    {
        return $this->role === "admin";
    }
    public function isSuperAdmin()
    {
        return $this->role === "super_admin";
    }
}