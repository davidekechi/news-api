<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface NewsSourceInterface
{
    /**
     * Fetch articles from the news source for the given categories.
     *
     * @param Collection<int, Category> $categories
     * @return array<string, mixed>
     */
    public function fetchArticles(Collection $categories): array;

    public function getName(): string;
}
