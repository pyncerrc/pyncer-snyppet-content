<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Data\Model\AbstractModel;

class ValueModel extends AbstractModel
{
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
            'content_id' => 0,
            'key' => '',
            'value' => '',
        ];
    }
}