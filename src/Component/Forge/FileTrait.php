<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\App\Identifier as ID;
use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeNotFoundException;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

use function Pyncer\Http\clean_path as pyncer_http_clean_path;
use function Pyncer\Http\clean_uri as pyncer_http_clean_uri;
use function Pyncer\Http\encode_uri_path as pyncer_http_encode_uri_path;

use const Pyncer\Snyppet\Content\FILE_URI as PYNCER_SNYPPET_CONTENT_FILE_URI;
use const Pyncer\Snyppet\Content\FILE_URI_PATH as PYNCER_SNYPPET_CONTENT_FILE_URI_PATH;

trait FileTrait
{
    protected function getContentUri(?int $fileId): ?string
    {
        if ($fileId === null || $fileId === 0) {
            return null;
        }

        $contentDataTree = $this->get(ID::content());

        if (!$contentDataTree->hasItem($fileId)) {
            return null;
        }

        $contentModel = $contentDataTree->getItem($fileId);

        if ($contentModel->getType() !== 'file') {
            throw new InvalidArgumentException(
                'The specified id does not belong to a file.'
            );
        }

        $path = $contentDataTree->getAliasPath($contentModel->getParentId());
        $filename = pyncer_http_encode_uri_path($contentModel->getAlias());
        $extension = $contentModel->getExtension();

        return $this->getBaseContentUri() . $path . '/' . $filename .
            ($extension ? '.' . $extension : '');
    }

    protected function getContentFile(?int $fileId): ?array
    {
        if ($fileId === null || $fileId === 0) {
            return null;
        }

        $contentDataTree = $this->get(ID::content());

        if (!$contentDataTree->hasItem($fileId)) {
            return null;
        }

        $contentModel = $contentDataTree->getItem($fileId);

        if ($contentModel->getType() !== 'file') {
            throw new InvalidArgumentException(
                'The specified id does not belong to a file.'
            );
        }

        $path = $contentDataTree->getAliasPath($contentModel->getParentId());
        $filename = pyncer_http_encode_uri_path($contentModel->getAlias());
        $extension = $contentModel->getExtension();

        $uri = $this->getBaseContentUri() . $path . '/' . $filename .
            ($extension ? '.' . $extension : '');

        return [
            'name' => $contentModel->getName(),
            'uri' => $uri,
            'filename' => $contentModel->getFilename() . (
                $contentModel->getExtension() !== null ?
                '.' . $contentModel->getExtension() :
                ''
            ),
        ];
    }

    protected function getVolumeUri(?int $fileId): ?string
    {
        if ($fileId === null || $fileId === 0) {
            return null;
        }

        $contentDataTree = $this->get(ID::content());

        if (!$contentDataTree->hasItem($fileId)) {
            return null;
        }

        $contentModel = $contentDataTree->getItem($fileId);

        if ($contentModel->getType() !== 'file') {
            throw new InvalidArgumentException(
                'The specified id does not belong to a file.'
            );
        }

        $volumes = $this->get(ID::content('volumes'));

        $volume = $volumes->getFromId($contentModel->getVolumeId());

        $volumeFile = new VolumeFile(
            $volume,
            $contentModel->getName(),
            $contentModel->getUri(),
            DirType::FILE,
            $contentModel->getFilename() . (
                $contentModel->getExtension() !== null ?
                '.' . $contentModel->getExtension() :
                ''
            )
        );

        return $volume->getUri($volumeFile);
    }

    protected function deleteContentFile(?int $fileId): void
    {
        if ($fileId === null || $fileId === 0) {
            return;
        }

        $contentDataTree = $this->get(ID::content());

        if (!$contentDataTree->hasItem($fileId)) {
            return;
        }

        $contentModel = $contentDataTree->getItem($fileId);

        if ($contentModel->getType() !== 'file') {
            throw new InvalidArgumentException(
                'The specified id does not belong to a file.'
            );
        }

        $volumes = $this->get(ID::content('volumes'));

        $volume = $volumes->getFromId($contentModel->getVolumeId());

        $volumeFile = new VolumeFile(
            $volume,
            $contentModel->getName(),
            $contentModel->getUri(),
            DirType::FILE,
            $contentModel->getFilename() . (
                $contentModel->getExtension() !== null ?
                '.' . $contentModel->getExtension() :
                ''
            )
        );

        $volume->delete($volumeFile);

        $connection = $this->get(ID::DATABASE);
        $contentMapper = new ContentMapper($connection);
        $contentMapper->delete($contentModel);
    }

    protected function getBaseContentUri(): string
    {
        $uri = PYNCER_SNYPPET_CONTENT_FILE_URI;

        if ($uri === null) {
            $uri = $this->getRequest()->getUri();
            $uri = $uri->withPath(PYNCER_SNYPPET_CONTENT_FILE_URI_PATH ?? '')->withQuery('');
            $uri = strval($uri);
        } else {
            $uri = pyncer_http_clean_uri($uri);
            $path = PYNCER_SNYPPET_CONTENT_FILE_URI_PATH;
            $path = pyncer_http_clean_path($path);
            $uri .= $path;
        }

        return $uri;
    }
}
