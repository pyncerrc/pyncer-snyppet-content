<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;

class ContentMapper extends AbstractMapper
{
    public function getTable(): string
    {
        return 'content';
    }

    public function forgeModel(iterable $data = []): ModelInterface
    {
        return new ContentModel($data);
    }

    public function isValidModel(ModelInterface $model): bool
    {
        return ($model instanceof ContentModel);
    }
}
