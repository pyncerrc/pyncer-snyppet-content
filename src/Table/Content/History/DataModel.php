<?php
namespace Pyncer\Snyppet\Content\Table\Content\History;

use Pyncer\Data\Model\AbstractModel;

class DataModel extends AbstractModel
{
    public function getHistoryId(): int
    {
        return $this->get('history_id');
    }
    public function setHistoryId(int $value): static
    {
        $this->set('history_id', $value);
        return $this;
    }

    public function getContentId(): int
    {
        return $this->get('content_id');
    }
    public function setContentId(int $value): static
    {
        $this->set('content_id', $value);
        return $this;
    }

    public function getKey(): string
    {
        return $this->get('key');
    }
    public function setKey(string $value): static
    {
        $this->set('key', $value);
        return $this;
    }

    public function getType(): string
    {
        return $this->get('type');
    }
    public function setType(string $value): static
    {
        $this->set('type', $value);
        return $this;
    }

    public function getValue(): string
    {
        return $this->get('value');
    }
    public function setValue(string $value): static
    {
        $this->set('value', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'history_id' => 0,
            'content_id' => 0,
            'key' => '',
            'type' => '',
            'value' => '',
        ];
    }
}
