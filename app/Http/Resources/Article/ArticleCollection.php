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
     * @return array<int, ArticleResource>|\Illuminate\Contracts\Support\Arrayable<int, ArticleResource>|\JsonSerializable
    */
    public function toArray($request)
    {
        return ArticleResource::collection($this->collection);
    }
}
