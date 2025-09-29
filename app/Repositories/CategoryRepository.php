<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository
{
    /**
     * Get all active categories, cached until tomorrow.
     *
     * @return Collection<int, Category>
     */
    public function getActive(): Collection
    {
        $expiresAt = Carbon::tomorrow();

        // Cache the pool, not the random selection
        return Cache::remember('categories.active', $expiresAt, function () {
            return Category::where('is_active', true)->get(['id', 'uuid', 'name']);
        });
    }

    /**
     * Get a random selection of active categories.
     *
     * @param int $limit
     * @return Collection<int, Category>
     */
    public function getActiveRandom(int $limit): Collection
    {
        return $this->getActive()->random($limit);
    }
}
