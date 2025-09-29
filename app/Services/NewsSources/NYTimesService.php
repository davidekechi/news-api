<?php

declare(strict_types=1);

namespace App\Services\NewsSources;

use GuzzleHttp\Promise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class NYTimesService extends BaseNewsSource
{
    public function fetchArticles(Collection $categories): array
    {
        $promises = [];

        foreach ($categories as $category) {
            $queryParams = [
                'fq' => 'desk:"' . $category->name . '"',
            ];

            $promises[$category->id] = $this->handleRequest(
                $this->source->base_url . '/search/v2/articlesearch.json',
                $queryParams
            );
        }

        $responses = Promise\Utils::settle($promises)->wait();

        $allArticles = [];

        foreach ($categories as $category) {
            $result = $responses[$category->id];

            if ($result['state'] === 'fulfilled') {
                $response = $result['value'];
                $data     = \json_decode($response->getBody()->getContents(), true);
            } else {
                Log::warning("NYTimes API request failed for category {$category->name}: " . $result['reason']->getMessage());
                $data = [];
            }

            $articles = \array_map(function ($apiArticle) use ($category) {
                $mapped                = $this->mapArticleData($apiArticle);
                $mapped['category_id'] = $category->id;

                return $mapped;
            }, $data['response']['docs'] ?? []);

            $allArticles = \array_merge($allArticles, $articles);
        }

        return $allArticles;
    }

    protected function mapArticleData(array $apiArticle): array
    {
        $author = $apiArticle['byline']['original'] ?? null;
        if ($author) {
            $author = \preg_replace('/^By\s+/i', '', $author);
        }

        return [
            'external_id'  => $apiArticle['_id']              ?? null,
            'title'        => $apiArticle['headline']['main'] ?? null,
            'description'  => $apiArticle['abstract']         ?? null,
            'url'          => $apiArticle['web_url']          ?? null,
            'image_url'    => $this->extractImageUrl($apiArticle),
            'author'       => $author,
            'published_at' => $apiArticle['pub_date'] ?? null,
        ];
    }

    /**
     * Extract image URL from the article data.
     *
     * @param array<string, mixed> $apiArticle
     */
    private function extractImageUrl(array $apiArticle): ?string
    {
        if (!empty($apiArticle['multimedia']['default']['url'])) {
            return $apiArticle['multimedia']['default']['url'];
        }
        if (!empty($apiArticle['multimedia']['thumbnail']['url'])) {
            return $apiArticle['multimedia']['thumbnail']['url'];
        }

        return null;
    }

    protected function getDefaultQueryParams(): array
    {
        return [
            'api-key' => config('source_keys.nytimes.api_key'),
        ];
    }

    public function getName(): string
    {
        return 'The New York Times';
    }
}
