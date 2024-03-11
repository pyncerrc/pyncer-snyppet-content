<?php
namespace Pyncer\Snyppet\Content\Volume;

use Psr\Http\Message\UploadedFileInterface;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

interface VolumeInterface
{
    public function initialize(): void;

    public function getUri(VolumeFile $file): string;

    public function read(VolumeFile $file): string;

    public function write(
        string $filename,
        string $contents,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile;

    public function writeFromUpload(
        UploadedFileInterface $uploadedFile,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile;

    public function writeFromUri(
        string $filename,
        string $uri,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile;

    public function writeFromStream(
        string $filename,
        $stream,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile;

    public function writeFromVolumeFile(
        VolumeFile $volumeFile,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile;

    public function delete(VolumeFile $file): void;
}
