<?php
namespace Pyncer\Snyppet\Content\Volume;

use Pyncer\Snyppet\Content\Volume\VolumeInterface;
use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Utility\AbstractDriver;
use Stringable;

final class Driver extends AbstractDriver
{
    public function __construct(
        string $name,
        ?string $path,
        array $params = []
    ) {
        parent::__construct($name, $params);

        $this->setPath($path);
    }

    protected function getType(): string
    {
        return 'volume';
    }

    protected function getClass(): string
    {
        return '\Pyncer\Snyppet\Content\Volume\Driver\\' . $this->getName() . '\Volume';
    }

    public function getVolume(int $id, string $alias): VolumeInterface
    {
        $class = $this->getClass();

        /** @var ConnectionInterface **/
        return new $class($id, $alias, $this);
    }

    public function getPath(): ?string
    {
        return $this->getString('path', null);
    }
    public function setPath(?string $value): static
    {
        return $this->setString('path', $value);
    }

    public function set(string $key, mixed $value): static
    {
        if ($key === 'path') {
            if ($value instanceof Stringable) {
                $value = strval($value);
            }

            if ($value !== null && !is_string($value)) {
                throw new InvalidArgumentException('The ' . $key . ' param must be a string or null.');
            }
        }

        return parent::set($key, $value);
    }
}
