<?php
namespace Pyncer\Snyppet\Content\Volume;

use Psr\Http\Message\UploadedFileInterface;
use PyncerDatabase\ConnectionInterface;
use Pyncer\Snyppet\Content\Volume\VolumeInterface;
use Pyncer\Snyppet\Content\Volume\Driver;
use Pyncer\Snyppet\Content\MediaType;

use function Pyncer\IO\extension as pyncer_io_extension;

abstract class AbstractVolume implements VolumeInterface
{
    public function __construct(
        protected int $id,
        protected string $alias,
        protected Driver $driver
    ) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function initialize(): void
    {}

    protected function cleanExtension(
        string $filename,
        string $mediaType = null,
    ): ?string
    {
        if ($mediaType !== null) {
            $mediaType = MediaType::tryFrom($mediaType);

            if ($mediaType !== null) {
                return $mediaType->getExtension();
            }
        }

        $extension = pyncer_io_extension($filename);

        if ($extension === null) {
            return null;
        }

        $extension = strtolower($extension);

        if ($extension === 'jpeg') {
            return 'jpg';
        }

        return $extension;
    }
}
