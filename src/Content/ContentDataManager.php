<?php
namespace Pyncer\Snyppet\Content\Content;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Content\Table\Content\DataMapper;
use Pyncer\Snyppet\Content\Table\Content\DataModel;
use Pyncer\Snyppet\Utility\Data\AbstractDataManager;
use Pyncer\Snyppet\Utility\Data\PreloadInterface;
use Pyncer\Snyppet\Utility\Data\PreloadTrait;
use Pyncer\Utility\Params;

class ContentDataManager extends AbstractDataManager implements TypeInterface
{
    use TypeTrait;

    public function __construct(
        ConnectionInterface $connection,
        protected int $contentId
    ) {
        parent::__construct($connection);
    }

    public function load(string ...$keys): static
    {
        $valueMapper = new DataMapper($this->connection);
        $result = $valueMapper->selectAllByKeys($this->contentId, $keys);

        foreach ($result as $valueModel) {
            $this->set($valueModel->getKey(), $valueModel->getValue());
            $this->setType($valueModel->getKey(), $valueModel->getType());
        }

        return $this;
    }

    public function save(string ...$keys): static
    {
        $valueMapper = new DataMapper($this->connection);

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
                $valueModel = new DataModel();
                $valueModel->setContentId($this->contentId);
                $valueModel->setKey($key);
            }

            $value = match ($value) {
                true => '1',
                false => '0',
                default => strval($value),
            };

            $valueModel->setValue($value);

            $valueModel->setType($this->getType($key) ?? 'text/plain');

            $valueMapper->replace($valueModel);
        }

        return $this;
    }
}
