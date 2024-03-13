<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Data\Mapper\AbstractMapper;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Snyppet\Content\Table\Content\ContentMapperQuery;
use Pyncer\Snyppet\Content\Table\Content\ContentModel;

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

    public function isValidMapperQuery(MapperQueryInterface $mapperQuery): bool
    {
        return ($mapperQuery instanceof ContentMapperQuery);
    }
}
