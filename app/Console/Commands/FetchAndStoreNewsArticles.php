<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\FetchAndStoreNewsArticlesService;
use Illuminate\Console\Command;

class FetchAndStoreNewsArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-and-store-news-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store articles from news sources';

    /**
     * Execute the console command.
     */
    public function handle(FetchAndStoreNewsArticlesService  $fetchAndStoreNewsArticlesService): int
    {
        $count = $fetchAndStoreNewsArticlesService->getAllActiveSources();
        $this->info("Imported {$count} articles from all active sources");

        return Command::SUCCESS;
    }
}
