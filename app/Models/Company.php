<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trade_name',
        'slug',
        'document',
        'email',
        'phone',
        'plan',
        'status',
        'logo_path',
        'timezone',
        'locale',
        'currency',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }
}
