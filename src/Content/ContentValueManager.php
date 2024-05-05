<?php
namespace Pyncer\Snyppet\Content\Content;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Access\Table\Content\ValueMapper;
use Pyncer\Snyppet\Access\Table\Content\ValueModel;
use Pyncer\Snyppet\Utility\Data\AbstractDataManager;
use Pyncer\Utility\Params;

class ContentValueManager extends AbstractDataManager
{
    public function __construct(
        ConnectionInterface $connection,
        protected int $contentId
    ) {
        parent::__construct($connection);
    }

    public function load(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->connection);
        $result = $valueMapper->selectAllByKeys($this->contentId, $keys);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
        }

        return $this;
    }

    public function save(string ...$keys): static
    {
        $valueMapper = new ValueMapper($this->connection);

        foreach ($keys as $key) {
            $valueModel = $valueMapper->selectByKey($this->contentId, $key);

            $value = $this->get($key);

            if ($value === null || $value === '') {
                if ($valueModel) {
                    $valueMapper->delete($valueModel);
                }

                continue;
            }

            if (!$valueModel) {
                $valueModel = new ValueModel();
                $valueModel->setContentId($this->contentId);
                $valueModel->setKey($key);
            }

            $value = match ($value) {
                true => '1',
                false => '0',
                default => strval($value),
            };

            $valueModel->setValue($value);

            $valueMapper->replace($valueModel);
        }

        return $this;
    }
}
