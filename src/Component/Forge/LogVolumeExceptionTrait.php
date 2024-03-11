<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Content\Exception\UploadException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeFileExistsException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeFileNotFOundException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeNotFoundException;

trait LogVolumeExceptionTrait
{
    protected function logVolumeException(VolumeException $e): void
    {
        if (!$this->has(ID::LOGGER)) {
            return;
        }

        $logger = $this->get(ID::LOGGER);

        if ($e instanceof VolumeFileExistsException) {
            $logger?->error(
                $e->getMessage(),
                [
                    'volume' => $e->getVolume(),
                    'uri' => $e->getUri(),
                ]
            );
        } elseif ($e instanceof VolumeFileNotFOundException) {
            $logger?->error(
                $e->getMessage(),
                [
                    'volume' => $e->getVolume(),
                    'uri' => $e->getUri(),
                ]
            );
        } elseif ($e instanceof VolumeNotFoundException) {
            $logger?->error(
                $e->getMessage(),
                [
                    'volume' => $e->getVolume(),
                ]
            );
        } else {
            $logger?->error($e->getMessage());
        }
    }
}
