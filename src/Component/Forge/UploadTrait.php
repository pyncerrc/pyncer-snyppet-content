<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use finfo;
use Psr\Http\Message\UploadedFileInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Exception\InvalidArgumentException;
use Pyncer\Snyppet\Content\Exception\UploadException;
use Pyncer\Snyppet\Content\MediaType;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeFile;

use function Pyncer\Array\get_recursive as pyncer_array_get_recursive;
use function Pyncer\IO\filename as pyncer_io_filename;

use const FILEINFO_MIME_TYPE;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_OK;

trait UploadTrait
{
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
        if ($params['delete_temporary_file'] ?? null) {
            $temporaryVolume->delete($temporaryVolumeFile);
        }

        return $volumeFile;
    }

    protected function deleteTemporaryFile(
        string $filename,
        string $uri,
        array $params = [],
    ): void
    {
        $temporaryVolume = $this->getVolume(DirType::TEMPORARY, $params);

        $temporaryVolumeFile = new VolumeFile(
            $temporaryVolume,
            pyncer_io_filename($filename, true),
            $uri,
            DirType::TEMPORARY,
            $filename,
        );

        $temporaryVolume->delete($temporaryVolumeFile);
    }

    protected function hasUploadFromRequest(
        string|array $key,
        ?int $existingFileId = null,
    ): bool
    {
        if ($key === '' || $key === []) {
            throw new InvalidArgumentException('Key cannot be empty.');
        }

        if (is_string($key)) {
            $key = [$key];
        }

        $files = $this->getRequest()->getUploadedFiles();
        $file = pyncer_array_get_recursive($files, $key);
        if ($file !== null) {
            return true;
        }

        $firstKey = array_shift($key);
        $file = $this->parsedBody->get($firstKey);
        if (!is_array($file)) {
            $file = null;
        } else {
            $file = pyncer_array_get_recursive($file, $key);
        }

        if ($file === null || !is_array($file)) {
            return false;
        }

        if ($existingFileId !== null && $existingFileId !== 0) {
            $fileUri = $this->getContentUri($existingFileId);
            if (($file['uri'] ?? null) === $fileUri) {
                return false;
            }
        }

        return true;
    }

    protected function clearUploadFromRequest(
        string|array $key,
        ?int $existingFileId,
    ): bool
    {
        if ($key === '' || $key === []) {
            throw new InvalidArgumentException('Key cannot be empty.');
        }

        if (is_string($key)) {
            $key = [$key];
        }

        $files = $this->getRequest()->getUploadedFiles();
        $file = pyncer_array_get_recursive($files, $key);
        if ($file !== null) {
            return false;
        }

        $firstKey = array_shift($key);
        $file = $this->parsedBody->get($firstKey);
        if (!is_array($file)) {
            $file = null;
        } else {
            $file = pyncer_array_get_recursive($file, $key);
        }

        if ($file !== null && is_array($file)) {
            return false;
        }

        if ($existingFileId === null || $existingFileId === 0) {
            return false;
        }

        return true;
    }

    protected function uploadFromRequest(
        string|array $key,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): VolumeFile
    {
        if ($key === '' || $key === []) {
            throw new InvalidArgumentException('Key cannot be empty.');
        }

        if (is_string($key)) {
            $key = [$key];
        }

        $files = $this->getRequest()->getUploadedFiles();
        $file = pyncer_array_get_recursive($files, $key);
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
            $firstKey = array_shift($key);
            $file = $this->parsedBody->get($firstKey);
            if (!is_array($file)) {
                $file = null;
            } else {
                $file = pyncer_array_get_recursive($file, $key);
            }

            if ($file !== null && is_array($file)) {
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

    protected function hasUploadFromValue(
        ?array $file,
        ?int $existingFileId = null,
    ): bool
    {
        if ($file === null || $file === []) {
            return false;
        }

        if ($existingFileId !== null && $existingFileId !== 0) {
            $fileUri = $this->getContentUri($existingFileId);
            if (($file['uri'] ?? null) === $fileUri) {
                return false;
            }
        }

        return true;
    }

    protected function clearUploadFromValue(
        ?array $file,
        ?int $existingFileId,
    ): bool
    {
        if ($file !== null && $file !== []) {
            return false;
        }

        if ($existingFileId === null || $existingFileId === 0) {
            return false;
        }

        return true;
    }

    protected function uploadFromValue(
        ?array $file,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): VolumeFile
    {
        if ($dirType !== DirType::TEMPORARY) {
            if ($file !== null && $file !== []) {
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
