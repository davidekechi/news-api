<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
            ->when(isset($filters['sources']), function ($q) use ($filters) {
                $q->whereIn('source_id', Source::whereIn('uuid', $filters['sources'])->pluck('id'));
            })
            ->when(isset($filters['categories']), function ($q) use ($filters) {
                $q->whereIn('category_id', Category::whereIn('uuid', $filters['categories'])->pluck('id'));
            })
            ->when(isset($filters['from_date']), function ($q) use ($filters) {
                $q->where('published_at', '>=', $filters['from_date']);
            })
            ->when(isset($filters['to_date']), function ($q) use ($filters) {
                $q->where('published_at', '<=', $filters['to_date']);
            });

        return $query->orderBy('published_at', 'desc')
                    ->paginate($filters['per_page'] ?? 20);
    }
}
