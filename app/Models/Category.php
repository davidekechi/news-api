<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($category) {
            if (empty($category->uuid)) {
                $category->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @param Builder<\App\Models\Category> $query
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
