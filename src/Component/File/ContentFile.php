<?php
namespace Pyncer\Snyppet\Content\Component\File;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\AbstractComponent;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Content\Component\Forge\FileResponseTrait;
use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\extension as pyncer_io_extension;
use function Pyncer\IO\filename as pyncer_io_filename;

class ContentFile extends AbstractComponent
{
    use FileResponseTrait;

    protected ?string $dir;
    protected array $paths;

    private ?ContentModel $contentModel = null;

    public function __construct(
        PsrServerRequestInterface $request,
        ?string $dir,
        array $paths,
    ) {
        parent::__construct($request);

        $this->dir = ($dir !== null ? pyncer_io_clean_dir($dir) : $dir);
        $this->paths = array_values($paths);
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
        if (!$this->getPaths()) {
            return false;
        }

        $contentDataTree = $this->get(ID::content());

        $paths = $this->getPaths();

        $filename = array_pop($paths);
        $extension = pyncer_io_extension($filename);
        $filename = pyncer_io_filename($filename, true);

        $path = implode('/', $paths);

        if (!$contentDataTree->hasItemFromDirPath($path)) {
            return false;
        }

        $parentContentModel = $contentDataTree->getItemFromDirPath($path);

        if (!$contentDataTree->hasItemFromAlias(
            $filename,
            $parentContentModel->getId(),
        )) {
            return false;
        }

        $contentModel = $contentDataTree->getItemFromAlias(
            $filename,
            $parentContentModel->getId(),
        );

        if ($contentModel->getExtension() !== $extension) {
            return false;
        }

        $this->contentModel = $contentModel;

        return true;
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        $volumes = $this->get(ID::content('volumes'));
        $volume = $volumes->getFromId($this->contentModel->getVolumeId());

        $volumeFile = new VolumeFile(
            $volume,
            $this->contentModel->getName(),
            $this->contentModel->getUri(),
            DirType::FILE,
            $this->contentModel->getFilename() . (
                $this->contentModel->getExtension() !== null ?
                '.' . $this->contentModel->getExtension() :
                ''
            )
        );

        $uri = $volume->getUri($volumeFile);

        $filename = $this->contentModel->getFilename();
        $extension = $this->contentModel->getExtension();
        $filename .= ($extension !== null ? '.' . $extension : '');

        return $this->getFileResponse($filename, $uri);
    }
}
