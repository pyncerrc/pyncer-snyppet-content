<?php
namespace Pyncer\Snyppet\Content;

use Psr\Http\Server\MiddlewareInterface as PsrMiddlewareInterface;
use Pyncer\Data\MapperQuery\QueryParams;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Snyppet\Snyppet as BaseSnyppet;
use Pyncer\Snyppet\Content\Middleware\InitializeMiddleware;

use const Pyncer\Snyppet\Content\MAPPER_QUERY_FILTERS as PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_FILTERS;
use const Pyncer\Snyppet\Content\MAPPER_QUERY_OPTIONS as PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_OPTIONS;
use const Pyncer\Snyppet\Content\MAPPER_QUERY_ORDER_BY as PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_ORDER_BY;
use const Pyncer\Snyppet\Content\VOLUMES as PYNCER_SNYPPET_CONTENT_VOLUMES;
use const Pyncer\Snyppet\Content\DEFAULT_FILE_VOLUME as PYNCER_SNYPPET_CONTENT_DEFAULT_FILE_VOLUME;
use const Pyncer\Snyppet\Content\DEFAULT_CACHE_VOLUME as PYNCER_SNYPPET_CONTENT_DEFAULT_CACHE_VOLUME;
use const Pyncer\Snyppet\Content\DEFAULT_TEMPORARY_VOLUME as PYNCER_SNYPPET_CONTENT_DEFAULT_TEMPORARY_VOLUME;

class Snyppet extends BaseSnyppet
{
    /**
     * @inheritdoc
     */
    protected function forgeMiddleware(string $class): PsrMiddlewareInterface|MiddlewareInterface
    {
        if ($class === InitializeMiddleware::class) {
            $queryParams = new QueryParams(
                filters: PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_FILTERS,
                options: PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_OPTIONS,
                orderBy: PYNCER_SNYPPET_CONTENT_MAPPER_QUERY_ORDER_BY,
            );

            return new $class(
                queryParams: $queryParams,
                volumes: PYNCER_SNYPPET_CONTENT_VOLUMES,
                defaultFileVolume: PYNCER_SNYPPET_CONTENT_DEFAULT_FILE_VOLUME,
                defaultCacheVolume: PYNCER_SNYPPET_CONTENT_DEFAULT_CACHE_VOLUME,
                defaultTemporaryVolume: PYNCER_SNYPPET_CONTENT_DEFAULT_TEMPORARY_VOLUME,
            );
        }

        return parent::initializeMiddleware($class);
    }
}
