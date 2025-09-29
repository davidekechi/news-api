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
     * @param int $limit
     * @return Collection<int, Category>
     */
    public function getActiveRandom(int $limit): Collection
    {
        $expiresAt = Carbon::tomorrow();

        // Cache the pool, not the random selection
        $categories = Cache::remember('categories.active', $expiresAt, function () {
            return Category::where('is_active', true)->get(['id', 'name']);
        });

        return $categories->random($limit);
    }
}
