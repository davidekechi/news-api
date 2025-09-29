<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'source_id',
        'category_id',
        'external_id',
        'title',
        'description',
        'url',
        'image_url',
        'author',
        'published_at',
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
        static::creating(function ($article) {
            if (empty($article->uuid)) {
                $article->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * @param Builder<\App\Models\Article> $query
     */
    public function scopeFindByUuid(Builder $query, string $uuid): ?Model
    {
        return $query->where('uuid', $uuid)->firstOrFail();
    }

    /**
     * @return BelongsTo<Source, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    /**
     * @return BelongsTo<Category   , $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
