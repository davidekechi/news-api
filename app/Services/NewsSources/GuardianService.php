<?php

declare(strict_types=1);

namespace App\Services\NewsSources;

use GuzzleHttp\Promise;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class GuardianService extends BaseNewsSource
{
    public function fetchArticles(Collection $categories): array
    {
        $promises = [];

        foreach ($categories as $category) {
            $queryParams = [
                'section'       => \strtolower($category->name),
                'show-fields'   => 'byline,trailText',
                'show-elements' => 'image',
                'page-size'     => 10,
            ];

            $promises[$category->id] = $this->handleRequest(
                $this->source->base_url . '/search',
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
                Log::warning("Guardian API request failed for category {$category->name}: " . $result['reason']->getMessage());
                $data = [];
            }

            $articles = \array_map(function ($apiArticle) use ($category) {
                $mapped                = $this->mapArticleData($apiArticle);
                $mapped['category_id'] = $category->id;

                return $mapped;
            }, $data['response']['results'] ?? []);

            $allArticles = \array_merge($allArticles, $articles);
        }

        return $allArticles;
    }

    protected function mapArticleData(array $apiArticle): array
    {
        return [
            'external_id'  => $apiArticle['id']                  ?? null,
            'title'        => $apiArticle['webTitle']            ?? null,
            'description'  => $apiArticle['fields']['trailText'] ?? null,
            'url'          => $apiArticle['webUrl']              ?? null,
            'image_url'    => $this->extractImageUrl($apiArticle),
            'author'       => $apiArticle['fields']['byline']   ?? null,
            'published_at' => $apiArticle['webPublicationDate'] ?? null,
        ];
    }

    /**
     * Extract image URL from the article data.
     *
     * @param array<string, mixed> $apiArticle
     */
    private function extractImageUrl(array $apiArticle): ?string
    {
        if (!empty($apiArticle['elements'][0]['assets'])) {
            return $apiArticle['elements'][0]['assets'][0]['file'] ?? null;
        }

        return null;
    }

    protected function getDefaultQueryParams(): array
    {
        return [
            'api-key' => config('source_keys.guardian.api_key'),
        ];
    }

    public function getName(): string
    {
        return 'The Guardian';
    }
}
