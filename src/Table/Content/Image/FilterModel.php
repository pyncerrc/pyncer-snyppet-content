<?php
namespace Pyncer\Snyppet\Content\Table\Content\Image;

use Pyncer\Data\Model\AbstractModel;

class FilterModel extends AbstractModel
{
    public function getAlias(): string
    {
        return $this->get('alias');
    }
    public function setAlias(string $value): static
    {
        $this->set('alias', $value);
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

    public function getFit(): string
    {
        return $this->get('fit');
    }
    public function setFit(string $value): static
    {
        $this->set('fit', $value);
        return $this;
    }

    public function getScale(): string
    {
        return $this->get('scale');
    }
    public function setScale(string $value): static
    {
        $this->set('scale', $value);
        return $this;
    }

    public function getCropX(): string
    {
        return $this->get('crop_x');
    }
    public function setCropX(string $value): static
    {
        $this->set('crop_x', $value);
        return $this;
    }

    public function getCropY(): string
    {
        return $this->get('crop_y');
    }
    public function setCropY(string $value): static
    {
        $this->set('crop_y', $value);
        return $this;
    }

    public function getPadding(): bool
    {
        return $this->get('padding');
    }
    public function setPadding(bool $value): static
    {
        $this->set('padding', $value);
        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->get('background_color');
    }
    public function setBackgroundColor(?string $value): static
    {
        $this->set('background_color', $this->nullify($value));
        return $this;
    }

    public static function getDefaultData(): array
    {
        return [
            'id' => 0,
            'alias' => '',
            'name' => '',
            'fit' => 'inside',
            'scale' => 'down',
            'crop_x' => 'center',
            'crop_y' => 'center',
            'padding' => false,
            'background_color' => null,
        ];
    }
}
