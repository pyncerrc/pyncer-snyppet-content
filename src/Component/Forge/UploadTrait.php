<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use finfo;
use Psr\Http\Message\UploadedFileInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Content\Component\Forge\VolumeTrait;
use Pyncer\Snyppet\Content\Exception\UploadException;
use Pyncer\Snyppet\Content\MediaType;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

use function Pyncer\IO\filename as pyncer_io_filename;

use const FILEINFO_MIME_TYPE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

trait UploadTrait
{
    use VolumeTrait;

    protected function uploadFile(
        UploadedFileInterface $uploadedFile,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): ?VolumeFile
    {
        $volume = $this->getVolume($dirType, $params);

        return $volume->writeFromUploadedFile(
            $uploadedFile,
            $dirType,
            $params,
        );
    }

    protected function moveTemporaryFile(
        string $filename,
        string $uri,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): ?VolumeFile
    {
        $temporaryVolume = $this->getVolume(DirType::TEMPORARY, $params);

        $temporaryVolumeFile = new VolumeFile(
            $temporaryVolume,
            pyncer_io_filename($filename, true),
            $uri,
            DirType::TEMPORARY,
            $filename,
        );

        $volume = $this->getVolume($dirType, $params);

        $volumeFile = $volume->writeFromVolumeFile(
            $temporaryVolumeFile,
            $dirType,
            $params,
        );

        // Delete temporary file.
        $temporaryVolume->delete($temporaryVolumeFile);

        return $volumeFile;
    }

    protected function hasUploadFromRequest(
        string $key,
        ?int $existingFileId,
    ): bool
    {
        $files = $this->getRequest()->getUploadedFiles();

        $file = $files[$key] ?? null;
        if ($file !== null) {
            return true;
        }

        $data = $this->parsedBody->getData();
        $file = $data[$key] ?? null;;
        if (!is_array($file)) {
            $file = null;
        }

        if ($existingFileId !== null) {
            $fileUri = $this->getContentUri($existingFileId);
            if (($file['uri'] ?? null) === $fileUri) {
                return false;
            }
        }

        return true;
    }

    protected function uploadFromRequest(
        string $key,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): VolumeFile
    {
        $files = $this->getRequest()->getUploadedFiles();

        $file = $files[$key] ?? null;
        if ($file !== null) {
            $imageError = $this->validateUploadedFile(
                $file,
                $this->getAllowedMediaTypes()
            );

            if ($imageError !== null) {
                throw new UploadException($imageError, $file->getError());
            }

            return $this->uploadFile(
                $file,
                $dirType,
                $params,
            );
        }

        if ($dirType !== DirType::TEMPORARY) {
            $data = $this->parsedBody->getData();

            $file = $data[$key] ?? null;
            if ($file !== null) {
                $filename = $file['filename'] ?? null;
                $uri = $file['uri'] ?? null;

                if ($filename === null || $uri === null) {
                    throw new UploadException('invalid');
                }

                return $this->moveTemporaryFile(
                    $filename,
                    $uri,
                    $dirType,
                    $params,
                );
            }
        }

        throw new UploadException('required');
    }

    protected function validateUploadedFile(
        ?UploadedFileInterface $file,
        ?array $allowedMediaTypes = null,
    ): ?string
    {
        if ($file === null ||
            !$file instanceof UploadedFileInterface ||
            $file->getError() == UPLOAD_ERR_NO_FILE
        ) {
            return 'required';
        }

        if ($file->getError() === UPLOAD_ERR_FORM_SIZE ||
            $file->getError() === UPLOAD_ERR_INI_SIZE
        ) {
            return 'size';
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            return 'unknown';
        }

        if (!$this->isValidMediaType($file, $allowedMediaTypes)) {
            return 'invalid';
        }

        return null;
    }

    protected function isValidMediaType(
        UploadedFileInterface $file,
        ?array $allowedMediaTypes = null,
    ): bool
    {
        $allowedMediaTypes = $allowedMediaTypes;
        if ($allowedMediaTypes !== null) {
            if (!in_array($file->getClientMediaType(), $allowedMediaTypes)) {
                return false;
            }
        } elseif (MediaType::tryFrom($file->getClientMediaType()) === null) {
            return false;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);

        if ($file->getFile()) {
            $mediaType = $finfo->file($file->getFile());
        } else {
            $mediaType = $finfo->buffer($file->getStream()->getContents());
        }

        if ($mediaType !== $file->getClientMediaType()) {
            return false;
        }

        return true;
    }
}