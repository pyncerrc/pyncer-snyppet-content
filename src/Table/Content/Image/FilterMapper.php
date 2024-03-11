<?php
namespace Pyncer\Snyppet\Content\Table\Content\Image;

use Pyncer\Snyppet\Content\Table\Content\Image\FilterModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class FilterMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__image__filter';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new FilterModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof FilterModel);
    }
}
