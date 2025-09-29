<?php

declare(strict_types=1);

namespace App\Http\Resources\Article;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array{data: mixed[], meta: array{current_page: int, last_page: int, per_page: int, total: int}}
     */
    public function toArray($request): array
    {
        return [
            'data' => ArticleResource::collection($this->collection)->toArray($request),
            'meta' => [
                'current_page' => (int) $this->resource->currentPage(),
                'last_page'    => (int) $this->resource->lastPage(),
                'per_page'     => (int) $this->resource->perPage(),
                'total'        => (int) $this->resource->total(),
            ],
        ];
    }
}
