<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Content\Table\Content\DataMapper;
use Pyncer\Snyppet\Content\Table\Content\DataModel;
use Pyncer\Snyppet\Content\Table\Content\DataValidator;
use Pyncer\Snyppet\Utility\Data\AbstractDataManager;
use Pyncer\Snyppet\Utility\Data\PreloadInterface;
use Pyncer\Snyppet\Utility\Data\TypeInterface;
use Pyncer\Snyppet\Utility\Data\TypeTrait;
use Pyncer\Utility\Params;

class DataManager extends AbstractDataManager implements TypeInterface
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
        $dataMapper = new DataMapper($this->connection);
        $result = $dataMapper->selectAllByKeys($this->contentId, $keys);

        foreach ($result as $dataModel) {
            $this->set($dataModel->getKey(), $dataModel->getValue());
            $this->setType($dataModel->getKey(), $dataModel->getType());
        }

        return $this;
    }

    public function validate(string ...$keys): array
    {
        $errors = [];

        foreach ($keys as $key) {
            $value = $this->getString($key, null);

            if ($value === null) {
                continue;
            }

            $type = $this->getType($key) ?? 'text/plain';

            $validator = new DataValidator($connection);
            [$data, $itemErrors] = $validator->validateData([
                'key' => $key,
                'type' => $type,
                'value' => $value,
            ]);

            if ($itemErrors) {
                $errors[$key] = $itemErrors;
            }
        }

        return $errors;
    }

    public function save(string ...$keys): static
    {
        $dataMapper = new DataMapper($this->connection);

        foreach ($keys as $key) {
            $dataModel = $dataMapper->selectByKey($this->contentId, $key);

            $value = $this->getString($key, null);

            if ($value === null) {
                if ($dataModel) {
                    $dataMapper->delete($dataModel);
                }

                continue;
            }

            if (!$dataModel) {
                $dataModel = new DataModel();
                $dataModel->setContentId($this->contentId);
                $dataModel->setKey($key);
            }

            $type = $this->getType($key) ?? 'text/plain';
            $dataModel->setType($type);

            $dataModel->setValue($value);

            $dataMapper->replace($dataModel);
        }

        return $this;
    }
}
