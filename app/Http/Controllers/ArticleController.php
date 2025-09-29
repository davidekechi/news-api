<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RetrieveArticlesRequest;
use App\Http\Resources\Article\ArticleCollection;
use App\Repositories\ArticleRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\SourceRepository;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private SourceRepository $sourceRepository,
        private CategoryRepository $categoryRepository
    ) {
    }

    public function index(RetrieveArticlesRequest $request): JsonResponse
    {
        $articles = $this->articleRepository->search($request->validated());

        return response()->json(new ArticleCollection($articles));
    }

    public function filters(): JsonResponse
    {
        return response()->json([
            'sources'    => $this->sourceRepository->getActive()->map->only(['uuid', 'name']),
            'categories' => $this->categoryRepository->getActive()->map->only(['uuid', 'name']),
        ]);
    }
}
