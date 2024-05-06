<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Snyppet\Content\Table\Content\ValueMapper;
use Pyncer\Snyppet\Content\Table\Content\ValueModel;
use Pyncer\Snyppet\Content\Table\Content\ValueValidator;
use Pyncer\Snyppet\Utility\Data\AbstractDataManager;
use Pyncer\Utility\Params;

use function Pyncer\String\len as pyncer_str_len;

class ValueManager extends AbstractDataManager
{
    public function __construct(
        ConnectionInterface $connection,
        protected int $contentId
    ) {
        parent::__construct($connection, 250);
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

    public function validate(string ...$keys): array
    {
        $errors = [];

        foreach ($keys as $key) {
            $value = $this->getString($key, null);

            if ($value === null) {
                continue;
            }

            $type = $this->getType($key) ?? 'text/plain';

            $validator = new ValueValidator($connection);
            [$data, $itemErrors] = $validator->validateData([
                'key' => $key,
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
        $valueMapper = new ValueMapper($this->connection);

        foreach ($keys as $key) {
            $valueModel = $valueMapper->selectByKey($this->contentId, $key);

            $value = $this->getString($key, null);

            if ($value === null) {
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

            $valueModel->setValue($value);

            $valueMapper->replace($valueModel);
        }

        return $this;
    }
}
