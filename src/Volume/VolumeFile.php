<?php
namespace Pyncer\Snyppet\Content\Volume;

use Pyncer\Snyppet\Content\Volume\DirType;

use function Pyncer\IO\clean_path as pyncer_io_clean_path;
use function Pyncer\IO\filename as pyncer_io_filename;
use function Pyncer\IO\extension as pyncer_io_extension;

final readonly class VolumeFile
{
    public function __construct(
        protected VolumeInterface $volume,
        protected string $name,
        protected string $uri,
        protected DirType $dirType = DirType::FILE,
    ) {}

    public function getVolume(): VolumeInterface
    {
        return $this->volume;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getFilename(bool $removeExtension): string
    {
        return pyncer_io_filename($this->uri, $removeExtension);
    }

    public function getExtension(): ?string
    {
        return pyncer_io_extension($this->uri);
    }

    public function getDirType(): DirType
    {
        return $this->dirType;
    }
}
