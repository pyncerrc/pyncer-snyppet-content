<?php
namespace Pyncer\Snyppet\Content\Table\Content\History;

// use Pyncer\Snyppet\Content\Table\Content\History\HistoryMapperQuery;
use Pyncer\Snyppet\Content\Table\Content\History\HistoryModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;

class HistoryMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__history';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new HistoryModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof HistoryModel);
    }

    /* public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof HistoryMapperQuery);
    } */
}
