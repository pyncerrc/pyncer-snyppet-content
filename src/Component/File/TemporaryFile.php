<?php
namespace Pyncer\Snyppet\Content\Component\File;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\AbstractComponent;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Content\Component\Forge\FileResponseTrait;
use Pyncer\Snyppet\Content\Component\Forge\VolumeTrait;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\extension as pyncer_io_extension;
use function Pyncer\IO\filename as pyncer_io_filename;

class TemporaryFile extends AbstractComponent
{
    use FileResponseTrait;
    use VolumeTrait;

    protected ?string $dir;
    protected array $paths;
    protected ?string $filename;
    protected ?string $temporaryVolume = null;

    public function __construct(
        PsrServerRequestInterface $request,
        ?string $dir,
        array $paths,
    ) {
        parent::__construct($request);

        $this->dir = ($dir !== null ? pyncer_io_clean_dir($dir) : $dir);
        $this->paths = array_values($paths);
    }

    public function getTemporaryVolume(): ?string
    {
        return $this->temporaryVolume;
    }
    public function setTemporaryVolume(?string $value): static
    {
        $this->temporaryVolume = $value;
        return $this;
    }

    public function getDir(): ?string
    {
        return $this->dir;
    }

    public function getPaths(): array
    {
        return $this->paths;
    }

    protected function isValidRequest(): bool
    {
        if (!$this->isValidPath()) {
            return false;
        }

        return parent::isValidRequest();
    }

    protected function isValidPath(): bool
    {
        $paths = $this->getPaths();

        if (!$paths || count($paths) > 1) {
            return false;
        }

        $this->filename = $paths[0];

        return true;
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $temporaryVolume = $this->getVolume(DirType::TEMPORARY, [
            'temporary_volume' => $this->getTemporaryVolume()
        ]);

        $temporaryVolumeFile = new VolumeFile(
            $temporaryVolume,
            pyncer_io_filename($this->filename, true),
            '/' . $this->filename,
            DirType::TEMPORARY,
        );

        $uri = $temporaryVolume->getUri($temporaryVolumeFile);

        return $this->getFileResponse($this->filename, $uri);
    }
}
