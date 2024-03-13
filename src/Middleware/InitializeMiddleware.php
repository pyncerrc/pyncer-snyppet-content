<?php
namespace Pyncer\Snyppet\Content\Middleware;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Data\MapperQuery\QueryParams;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Exception\UnexpectedValueException;
use Pyncer\Http\Server\MiddlewareInterface;
use Pyncer\Http\Server\RequestHandlerInterface;
use Pyncer\Snyppet\Content\Data\ContentDataTree;
use Pyncer\Snyppet\Content\Table\Content\ContentMapperQuery;
use Pyncer\Snyppet\Content\Volume\VolumeManager;

class InitializeMiddleware implements MiddlewareInterface
{
    private ?QueryParams $queryParams;
    private ?array $volumes;
    private string $defaultFileVolume;
    private ?string $defaultCacheVolume;
    private ?string $defaultTemporaryVolume;

    public function __construct(
        ?QueryParams $queryParams = null,
        ?array $volumes = ['local'],
        string $defaultFileVolume = 'local',
        ?string $defaultCacheVolume = null,
        ?string $defaultTemporaryVolume = null,
    ) {
        $this->setQueryParams($queryParams);
        $this->setVolumes($volumes);
        $this->setDefaultFileVolume($defaultFileVolume);
        $this->setDefaultCacheVolume($defaultCacheVolume);
        $this->setDefaultTemporaryVolume($defaultTemporaryVolume);
    }

    public function getQueryParams(): ?QueryParams
    {
        return $this->queryParams;
    }
    public function setQueryParams(?QueryParams $value): static
    {
        $this->queryParams = $value;
        return $this;
    }

    public function getVolumes(): ?array
    {
        return $this->volumes;
    }
    public function setVolumes(?array $value): static
    {
        $this->volumes = $value;
        return $this;
    }

    public function getDefaultFileVolume(): string
    {
        return $this->defaultFileVolume;
    }
    public function setDefaultFileVolume(string $value): static
    {
        $this->defaultFileVolume = $value;
        return $this;
    }

    public function getDefaultCacheVolume(): ?string
    {
        return $this->defaultCacheVolume;
    }
    public function setDefaultCacheVolume(?string $value): static
    {
        $this->defaultCacheVolume = $value;
        return $this;
    }

    public function getDefaultTemporaryVolume(): ?string
    {
        return $this->defaultTemporaryVolume;
    }
    public function setDefaultTemporaryVolume(?string $value): static
    {
        $this->defaultTemporaryVolume = $value;
        return $this;
    }

    public function __invoke(
        PsrServerRequestInterface $request,
        PsrResponseInterface $response,
        RequestHandlerInterface $handler
    ): PsrResponseInterface
    {
        // Database
        if (!$handler->has(ID::DATABASE)) {
            throw new UnexpectedValueException(
                'Database connection expected.'
            );
        }

        $connection = $handler->get(ID::DATABASE);
        if (!$connection instanceof ConnectionInterface) {
            throw new UnexpectedValueException('Invalid database connection.');
        }

        ID::register(ID::content());
        ID::register(ID::content('volumes'));
        ID::register(ID::content('file_volume'));
        ID::register(ID::content('cache_volume'));
        ID::register(ID::content('temporary_volume'));

        $handler->set(ID::content(), new ContentDataTree(
            connection: $connection,
            queryParams: $this->getQueryParams(),
        ));

        $volumes = $this->getVolumes();
        $handler->set(ID::content('volumes'), function() use($connection, $volumes) {
            return new VolumeManager($connection, $volumes);
        });

        $defaultFileVolume = $this->getDefaultFileVolume();
        $handler->set(ID::content('file_volume'), function() use($handler, $defaultFileVolume) {
            $volumeManager = $handler->get(ID::content('volumes'));
            return $volumeManager->get($defaultFileVolume);
        });

        $defaultCacheVolume = $this->getDefaultCacheVolume() ?? $this->getDefaultFileVolume();
        $handler->set(ID::content('cache_volume'), function() use($handler, $defaultCacheVolume) {
            $volumeManager = $handler->get(ID::content('volumes'));
            return $volumeManager->get($defaultCacheVolume);
        });

        $defaultTemporaryVolume = $this->getDefaultTemporaryVolume() ?? $this->getDefaultFileVolume();
        $handler->set(ID::content('temporary_volume'), function() use($handler, $defaultTemporaryVolume) {
            $volumeManager = $handler->get(ID::content('volumes'));
            return $volumeManager->get($defaultTemporaryVolume);
        });

        return $handler->next($request, $response);
    }
}
