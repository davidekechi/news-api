<?php

declare(strict_types=1);

namespace App\Services\NewsSources;

use App\Contracts\NewsSourceInterface;
use App\Models\Source;
use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Support\Facades\Log;

abstract class BaseNewsSource implements NewsSourceInterface
{
    protected Client $httpClient;
    protected Source $source;

    public function __construct(Source $source)
    {
        $this->source     = $source;
        $this->httpClient = new Client();
    }

    /**
     * Map raw API article data to the standard format.
     *
     * @param array<string, mixed> $apiArticle
     * @return array<string, mixed>
     */
    abstract protected function mapArticleData(array $apiArticle): array;

    /**
     * Handle API requests and return mapped articles.
     *
     * @param string $endpoint
     * @param array<string, mixed> $queryParams
     * @return PromiseInterface|array<int, array<string, mixed>>
     */
    protected function handleRequest(string $endpoint, array $queryParams = []): PromiseInterface|array
    {
        try {
            $response = $this->httpClient->getAsync($endpoint, [
                'query' => \array_merge($this->getDefaultQueryParams(), $queryParams),
                'delay' => 200,
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error("API Request failed for {$this->getName()}: " . $e->getMessage());

            return [];
        }
    }

    /**
     * Get default query parameters for the API.
     *
     * @return array<string, mixed>
     */
    abstract protected function getDefaultQueryParams(): array;
}
