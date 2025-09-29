<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Source;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SourceRepository
{
    /**
     * Get all active sources, cached until tomorrow.
     *
     * @return Collection<int, Source>
     */
    public function getActive(): Collection
    {
        $expiresAt = Carbon::tomorrow();

        return Cache::remember('sources.active', $expiresAt, function () {
            return Source::where('is_active', true)->get(['id', 'name', 'base_url', 'api_handler']);
        });
    }
}
