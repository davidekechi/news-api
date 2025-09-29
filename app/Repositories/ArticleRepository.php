<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ArticleRepository
{
    /**
     * Create or update an article based on external_id and source_id.
     *
     * @param array<string, mixed> $articleData
     * @return Article
     */
    public function createOrUpdate(array $articleData): Article
    {
        $article = Article::updateOrCreate(
            [
                'source_id'   => $articleData['source_id'],
                'external_id' => $articleData['external_id'],
            ],
            $articleData
        );

        return $article;
    }

    /**
     * Search articles with various filters.
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Pagination\LengthAwarePaginator<int, \App\Models\Article>
     */
    public function search(array $filters): ?LengthAwarePaginator
    {
        $query = Article::with(['source', 'category'])
            ->when(isset($filters['query']), function ($q) use ($filters) {
                $search = $filters['query'];

                $q->where(function ($q) use ($search) {
                    $q->where('title', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%")
                    ->orWhere('author', 'ILIKE', "%{$search}%");
                });
            })
            ->when(isset($filters['source']), function ($q) use ($filters) {
                $id = Source::whereRaw('LOWER(api_handler) = ?', [\strtolower($filters['source'])])
                            ->value('id');

                return $id
                    ? $q->where('source_id', $id)
                    : $q->whereRaw('0 = 1');
            })
            ->when(isset($filters['category']), function ($q) use ($filters) {
                $id = Category::whereRaw('LOWER(name) = ?', [\strtolower($filters['category'])])
                              ->value('id');

                return $id
                    ? $q->where('category_id', $id)
                    : $q->whereRaw('0 = 1');
            })
            ->when(isset($filters['preferredSources']), function ($q) use ($filters) {
                $preferredSources = \is_string($filters['preferredSources'])
                ? \array_map('trim', \explode(',', $filters['preferredSources']))
                : (array) $filters['preferredSources'];

                $q->whereIn('source_id', Source::whereIn(DB::raw('LOWER(api_handler)'), \array_map('strtolower', $preferredSources))->pluck('id'));
            })
            ->when(isset($filters['preferredCategories']), function ($q) use ($filters) {
                $preferredCategories = \is_string($filters['preferredCategories'])
                ? \array_map('trim', \explode(',', $filters['preferredCategories']))
                : (array) $filters['preferredCategories'];

                $q->whereIn('category_id', Category::whereIn(DB::raw('LOWER(name)'), \array_map('strtolower', $preferredCategories))->pluck('id'));
            })
            ->when(isset($filters['preferredAuthors']), function ($q) use ($filters) {
                $preferredAuthors = \is_string($filters['preferredAuthors'])
                ? \array_map('trim', \explode(',', $filters['preferredAuthors']))
                : (array) $filters['preferredAuthors'];

                $q->where(function ($query) use ($preferredAuthors) {
                    foreach ($preferredAuthors as $author) {
                        $query->orWhereRaw('LOWER(author) ILIKE ?', ['%' . \strtolower($author) . '%']);
                    }
                });
            })
            ->when(isset($filters['from_date']), function ($q) use ($filters) {
                $q->where('published_at', '>=', $filters['from_date']);
            })
            ->when(isset($filters['to_date']), function ($q) use ($filters) {
                $q->where('published_at', '<=', $filters['to_date']);
            });

        return $query->orderBy('published_at', 'desc')
                    ->paginate($filters['per_page'] ?? config('variables.pagination.default_per_page', 10));
    }
}
