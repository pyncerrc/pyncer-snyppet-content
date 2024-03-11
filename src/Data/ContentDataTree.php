<?php
namespace Pyncer\Snyppet\Content\Data;

use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\MapperQuery\QueryParams;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Tree\AbstractAliasTree;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\ConnectionTrait;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Table\Content\ContentMapperQuery;

use const DIRECTORY_SEPARATOR as DS;

class ContentDataTree extends AbstractAliasTree
{
    protected ContentMapper $mapper;
    protected ?ContentMapperQuery $mapperQuery;

    public function __construct(
        ConnectionInterface $connection,
        ?QueryParams $queryParams = null,
    ) {
        // Don't allow preloading, too many rows
        parent::__construct(
            connection: $connection,
            preload: false,
        );

        $this->mapper = new ContentMapper($this->getConnection());

        if ($queryParams === null || $queryParam->isEmpty()) {
            $this->mapperQuery = null;
        } else {
            $mapperQuery = new ContentMapperQuery($connection);
            $mapperQuery->setQueryParams($queryParams);
            $this->mapperQuery = $mapperQuery;
        }
    }

    protected function forgeMapper(): MapperInterface
    {
        return $this->mapper;
    }

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        return $this->mapperQuery;
    }

    public function hasItemFromDirPath(
        string $path,
        ?int $parentId = null
    ): bool
    {
        if ($parentId !== null && $parentId <= 0) {
            throw new InvalidArgumentException('Parent id must be greater than zero or null.');
        }

        if (DS !== '/') {
            $path = str_replace(DS, '/', $path);
        }

        return $this->hasItemFromAliasPathAndColumns($path, [
            'parent_id' => $parentId,
            'type' => 'dir',
        ]);
    }

    public function getItemFromDirPath(
        string $path,
        ?int $parentId = null
    ): ModelInterface
    {
        if ($parentId !== null && $parentId <= 0) {
            throw new InvalidArgumentException('Parent id must be greater than zero or null.');
        }

        if (DS !== '/') {
            $path = str_replace(DS, '/', $path);
        }

        return $this->getItemFromAliasPathAndColumns($path, [
            'parent_id' => $parentId,
            'type' => 'dir',
        ]);
    }

    public function getChildrenOfType(?int $id, array $types): iterable
    {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Id must be greater than zero or null.');
        }

        $items = [];

        if ($this->preload) {
            foreach ($this->getItems() as $model) {
                if ($model->getParentId() === $id &&
                    in_array($model->getType(), $types)
                ) {
                    $items[$model->getId()] = $model;
                }
            }

            return $items;
        }

        $mapper = $this->forgeMapper();
        $mapperQuery = $this->forgeMapperQuery();
        $result = $mapper->selectAllByColumns([
            'parent_id' => $id,
            'type' => [$types]
        ], $mapperQuery);

        foreach ($result as $model) {
            $this->addItem($model);
            $items[$model->getId()] = $model;
        }

        return $items;
    }
    public function getDescendentsOfType(?int $id, array $types): iterable
    {
        if ($id !== null && $id <= 0) {
            throw new InvalidArgumentException('Id must be greater than zero or null.');
        }

        $items = [];

        if ($this->preload) {
            foreach ($this->getItems() as $model) {
                if ($model->getParentId() === $id &&
                    in_array($model->getType(), $types)
                ) {
                    $items[$model->getId()] = $model;

                    foreach ($this->getDescendents($model->getId()) as $model2) {
                        $items[$model2->getId()] = $model2;
                    }
                }
            }

            return $items;
        }

        $mapper = $this->forgeMapper();
        $mapperQuery = $this->forgeMapperQuery();
        $result = $mapper->selectAllByColumns([
            'parent_id' => $id,
            'type' => [$types]
        ], $mapperQuery);

        foreach ($result as $model) {
            $this->addItem($model);

            $items[$model->getId()] = $model;

            foreach ($this->getDescendents($model->getId()) as $model2) {
                $items[$model2->getId()] = $model2;
            }
        }

        return $items;
    }
}
