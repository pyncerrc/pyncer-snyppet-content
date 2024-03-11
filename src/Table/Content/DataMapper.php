<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\DataModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class DataMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__data';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new DataModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof DataModel);
    }
}
