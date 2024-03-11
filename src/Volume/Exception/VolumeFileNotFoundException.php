<?php
namespace Pyncer\Snyppet\Content\Volume\Exception;

use Pyncer\Snyppet\Content\Volume\Exception\VolumeException;
use Throwable;

class VolumeFileNotFoundException extends VolumeException
{
    protected string $volume;

    public function __construct(
        int|string $volume,
        string $uri,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            'The file, ' . $uri . ', was not found in volume, ' . $volume . '.',
            $code,
            $previous
        );

        $this->volume = $volume;
        $this->uri = $uri;
    }

    public function getVolume(): int|string
    {
        return $this->volume;
    }

    public function getUri(): string
    {
        return $this->uri;
    }
}
