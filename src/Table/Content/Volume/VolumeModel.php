<?php
namespace Pyncer\Snyppet\Content\Table\Content\Volume;

use Pyncer\Data\Model\AbstractModel;

class VolumeModel extends AbstractModel
{
    public function getCacheId(): ?int
    {
        return $this->get('cache_id');
    }
    public function setCacheId(?int $value): static
    {
        $this->set('cache_id', $this->nullify($value));
        return $this;
    }

    public function getName(): string
    {
        return $this->get('name');
    }
    public function setName(string $value): static
    {
        $this->set('name', $value);
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

    public function getPath(): ?string
    {
        return $this->get('path');
    }
    public function setPath(?string $value): static
    {
        $this->set('path', $this->nullify($value));
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'cache_id' => null,
            'name' => '',
            'type' => '',
            'path' => null,
        ];
    }
}
