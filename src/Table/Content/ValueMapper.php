<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\ValueModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\Mapper\MapperResultInterface;

class ValueMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__value';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new ValueModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof ValueModel);
    }

    public function selectByKey(
        int $contentId,
        string $key,
        ?MapperQueryInterface $mapperQuery = null
    ): ?ModelInterface
    {
        return $this->selectByColumns(
            [
                'content_id' => $contentId,
                'key' => $key,
            ],
            $mapperQuery,
        );
    }

    public function selectAllByKeys(
        int $contentId,
        array $keys,
        ?MapperQueryInterface $mapperQuery = null
    ): MapperResultInterface
    {
        return $this->selectAllByColumns(
            [
                'content_id' => $contentId,
                'key' => $keys,
            ],
            $mapperQuery,
        );
    }
}
