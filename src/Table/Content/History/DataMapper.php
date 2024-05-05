<?php
namespace Pyncer\Snyppet\Content\Table\Content\History;

use Pyncer\Snyppet\Content\Table\Content\History\DataModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class DataMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__history__data';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new DataModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof DataModel);
    }

    public function selectByKey(
        int $historyId,
        int $contentId,
        string $key,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'history_id' => $historyId,
                'content_id' => $contentId,
                'key' => $key,
            ],
            $mapperQuery,
        );
    }

    public function selectAllByKeys(
        int $historyId,
        int $contentId,
        array $keys,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'history_id' => $historyId,
                'content_id' => $contentId,
                'key' => $keys,
            ],
            $mapperQuery,
        );
    }
}
