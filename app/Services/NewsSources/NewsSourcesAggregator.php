<?php

declare(strict_types=1);

namespace App\Services\NewsSources;

use App\Contracts\NewsSourceInterface;
use App\Models\Source;

class NewsSourcesAggregator
{
    public static function create(Source $source): ?NewsSourceInterface
    {
        $handlerClass = self::getHandlerClass($source->api_handler);

        if (!\class_exists($handlerClass)) {
            return null;
        }

        return new $handlerClass($source);
    }

    private static function getHandlerClass(string $handler): ?string
    {
        $handlers = [
            'newsapiorg' => NewsApiOrgService::class,
            'guardian'   => GuardianService::class,
            'nytimes'    => NYTimesService::class,
        ];

        return $handlers[$handler] ?? null;
    }
}
