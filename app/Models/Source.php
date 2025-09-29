<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Source extends Model
{
    protected $fillable = [
        'name',
        'api_handler',
        'base_url',
        'is_active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($source) {
            if (empty($source->uuid)) {
                $source->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @param Builder<\App\Models\Source> $query
     */
    public function scopeFindByUuid(Builder $query, string $uuid): ?Model
    {
        return $query->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * @return HasMany<Article, $this>
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
