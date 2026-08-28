<?php
namespace Pyncer\Snyppet\Content\Table\Communication\Content;

use Pyncer\Data\Mapper\AbstractRelationMapper;

class ContentRelationMapper extends AbstractRelationMapper
{
    public function getTable(): string
    {
        return 'content__organization';
    }

    public function getParentIdColumn(): string
    {
        return 'content_id';
    }

    public function getChildIdColumn(): string
    {
        return 'organization_id';
    }
}
