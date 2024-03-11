<?php
namespace Pyncer\Snyppet\Content\Volume\Exception;

use Pyncer\Snyppet\Content\Volume\Exception\VolumeException;
use Throwable;

class VolumeNotFoundException extends VolumeException
{
    protected string $volume;

    public function __construct(
        int|string $volume,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            'The volume, ' . $volume . ', was not found.',
            $code,
            $previous
        );

        $this->volume = $volume;
    }

    public function getVolume(): int|string
    {
        return $this->volume;
    }
}
