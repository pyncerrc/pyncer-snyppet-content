<?php
namespace Pyncer\Snyppet\Content\Component\Module\Content;

use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractDeleteIndexModule;
use Pyncer\Data\Mapper\MapperInterface;
use Pyncer\Data\MapperQuery\MapperQueryInterface;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Table\Content\ContentMapperQuery;
use Pyncer\Snyppet\Utility\Component\SoftDeleteTrait;

class DeleteContentItemModule extends AbstractDeleteIndexModule
{
    use SoftDeleteTrait;

    protected function forgeMapper(): MapperInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new ContentMapper($connection);
    }

    protected function forgeMapperQuery(): ?MapperQueryInterface
    {
        $connection = $this->get(ID::DATABASE);
        return new ContentMapperQuery($connection, $this->request);
    }

    protected function deleteItem(ModelInterface $model): array
    {
        if (!$this->getSoftDelete()) {
            return parent::deleteItem($model);
        }

        $errors = [];

        try {
            $mapper = $this->forgeMapper();
            $model->setDeletes(true);
            $mapper->update($model);
        } catch (QueryException) {
            $errors['general'] = 'delete';
        }

        return $errors;
    }
}
