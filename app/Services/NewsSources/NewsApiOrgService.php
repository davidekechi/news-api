<?php

declare(strict_types=1);

namespace App\Services\NewsSources;

use GuzzleHttp\Promise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class NewsApiOrgService extends BaseNewsSource
{
    public function fetchArticles(Collection $categories): array
    {
        // Prepare async requests per category
        $promises = [];

        foreach ($categories as $category) {
            $queryParams = [
                    'q'        => $category->name,
                    'pageSize' => 10,
                ];

            $promises[$category->id] = $this->handleRequest($this->source->base_url . '/everything', $queryParams);
        }

        $responses = Promise\Utils::settle($promises)->wait();

        $allArticles = [];

        foreach ($categories as $category) {
            $result = $responses[$category->id];

            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                $data     = \json_decode($response->getBody()->getContents(), true);
            } else {
                // log and skip failed request
                Log::warning("Request failed for category {$category->name}: " . $result['reason']->getMessage());
                $data = [];
            }

            $articles = \array_map(function ($apiArticle) use ($category) {
                $mapped                = $this->mapArticleData($apiArticle);
                $mapped['category_id'] = $category->id;

                return $mapped;
            }, $data['articles'] ?? []);

            $allArticles = \array_merge($allArticles, $articles);
        }

        return $allArticles;
    }

    protected function mapArticleData(array $apiArticle): array
    {
        return [
            'external_id'  => $apiArticle['url']         ?? null,
            'title'        => $apiArticle['title']       ?? null,
            'description'  => $apiArticle['description'] ?? null,
            'url'          => $apiArticle['url']         ?? null,
            'image_url'    => $apiArticle['urlToImage']  ?? null,
            'author'       => $apiArticle['author']      ?? null,
            'published_at' => $apiArticle['publishedAt'] ?? null,
        ];
    }

    protected function getDefaultQueryParams(): array
    {
        return [
            'apiKey'   => config('source_keys.newsapiorg.api_key'),
            'language' => 'en',
        ];
    }

    public function getName(): string
    {
        return 'NewsAPI.org';
    }
}
