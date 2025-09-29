<?php

declare(strict_types=1);

namespace App\Http\Resources\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'         => $this->resource->uuid,
            'title'        => $this->resource->title,
            'description'  => $this->resource->description,
            'url'          => $this->resource->url,
            'image_url'    => $this->resource->image_url,
            'author'       => $this->resource->author,
            'published_at' => $this->resource->published_at,
            'source'       => $this->resource->source->name,
            'category'     => $this->resource->category->name,
        ];
    }
}
