<?php
namespace Pyncer\Snyppet\Content\Table\Content\Volume;

use Pyncer\Snyppet\Content\Table\Content\VolumeModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class VolumeMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content__volume';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new VolumeModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof VolumeModel);
    }
}
