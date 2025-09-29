<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Source;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SourceRepository;
use App\Services\NewsSources\NewsSourcesAggregator;
use Illuminate\Support\Facades\Log;

class FetchAndStoreNewsArticlesService
{
    public function __construct(
        private CategoryRepository $categoryRepository,
        private SourceRepository $sourceRepository,
        private ArticleRepository $articleRepository
    ) {
    }

    /**
     * Fetch and store articles from a given source.
     *
     * @param Source $source
     */
    public function storeNewsArticles(Source $source): int
    {
        try {
            $newsSource = NewsSourcesAggregator::create($source);
            if (!$newsSource) {
                Log::warning("No handler found for source: {$source->name}");

                return 0;
            }
            $categories = $this->categoryRepository->getActiveRandom(5);
            $articles   = $newsSource->fetchArticles($categories);

            $importedCount = 0;
            foreach ($articles as $articleData) {
                $this->articleRepository->createOrUpdate(
                    \array_merge($articleData, ['source_id' => $source->id])
                );
                $importedCount++;
            }

            Log::info("Imported {$importedCount} articles from {$source->name}");

            return $importedCount;

        } catch (\Exception $e) {
            Log::error("Failed to aggregate from {$source->name}: " . $e->getMessage());

            return 0;
        }
    }

    public function getAllActiveSources(): int
    {
        $sources       = $this->sourceRepository->getActive();
        $totalImported = 0;

        foreach ($sources as $source) {
            $totalImported += $this->storeNewsArticles($source);
            \sleep(1); // Rate limiting between API calls
        }

        return $totalImported;
    }
}
