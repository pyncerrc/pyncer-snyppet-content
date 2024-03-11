<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use DateTime;
use DateTimeInterface;
use Pyncer\Data\Model\AbstractModel;

use function Pyncer\date_time as pyncer_date_time;

use const Pyncer\DATE_TIME_FORMAT as PYNCER_DATE_TIME_FORMAT;

class ContentModel extends AbstractModel
{
    public function getParentId(): ?int
    {
        return $this->get('parent_id');
    }
    public function setParentId(?int $value): static
    {
        $this->set('parent_id', $this->nullify($value));
        return $this;
    }

    public function getVolumeId(): ?int
    {
        return $this->get('volume_id');
    }
    public function setVolumeId(?int $value): static
    {
        $this->set('volume_id', $this->nullify($value));
        return $this;
    }

    public function getMark(): ?string
    {
        return $this->get('mark');
    }
    public function setMark(?string $value): static
    {
        $this->set('mark', $this->nullify($value));
        return $this;
    }

    public function getInsertDateTime(): DateTime
    {
        $value = $this->get('insert_date_time');
        return pyncer_date_time($value);
    }
    public function setInsertDateTime(string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('insert_date_time', $value);
        return $this;
    }

    public function getUpdateDateTime(): ?DateTime
    {
        $value = $this->get('update_date_time');
        return pyncer_date_time($value);
    }
    public function setUpdateDateTime(null|string|DateTimeInterface $value): static
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->format(PYNCER_DATE_TIME_FORMAT);
        }
        $this->set('update_date_time', $this->nullify($value));
        return $this;
    }

    public function getOrder(): ?int
    {
        return $this->get('order');
    }
    public function setOrder(?int $value): static
    {
        $this->set('order', $this->nullify($value));
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

    public function getAlias(): ?string
    {
        return $this->get('alias');
    }
    public function setAlias(?string $value): static
    {
        $this->set('alias', $this->nullify($value));
        return $this;
    }

    public function getUri(): ?string
    {
        return $this->get('uri');
    }
    public function setUri(?string $value): static
    {
        $this->set('uri', $this->nullify($value));
        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->get('filename');
    }
    public function setFilename(?string $value): static
    {
        $this->set('filename', $this->nullify($value));
        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->get('extension');
    }
    public function setExtension(?string $value): static
    {
        $this->set('extension', $this->nullify($value));
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

    public function getIteration(): ?int
    {
        return $this->get('iteration');
    }
    public function setIteration(?int $value): static
    {
        $this->set('iteration', $this->nullify($value));
        return $this;
    }

    public function getEnabled(): bool
    {
        return $this->get('enabled');
    }
    public function setEnabled(bool $value): static
    {
        $this->set('enabled', $value);
        return $this;
    }

    public function getDeleted(): bool
    {
        return $this->get('deleted');
    }
    public function setDeleted(bool $value): static
    {
        $this->set('deleted', $value);
        return $this;
    }

    public static function getDefaultData(): array
    {
        $dateTime = pyncer_date_time()->format(PYNCER_DATE_TIME_FORMAT);

        return [
            'id' => 0,
            'parent_id' => null,
            'volume_id' => null,
            'mark' => null,
            'insert_date_time' => $dateTime,
            'update_date_time' => null,
            'order' => null,
            'type' => '',
            'alias' => null,
            'uri' => null,
            'filename' => null,
            'extension' => null,
            'name' => '',
            'iteration' => null,
            'enabled' => false,
            'deleted' => false,
        ];
    }
}
