<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\Snyppet\Content\Component\Forge\DirTrait;
use Pyncer\Snyppet\Content\Component\Forge\FileTrait;
use Pyncer\Snyppet\Content\Component\Forge\InsertContentFileTrait;
use Pyncer\Snyppet\Content\Component\Forge\LogVolumeExceptionTrait;
use Pyncer\Snyppet\Content\Component\Forge\UploadTrait;
use Pyncer\Snyppet\Content\Component\Forge\VolumeTrait;
use Pyncer\Snyppet\Content\Exception\UploadException;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeException;

use function Pyncer\uid as pyncer_uid;

trait ContentFilesTrait
{
    use DirTrait;
    use FileTrait;
    use InsertContentFileTrait;
    use LogVolumeExceptionTrait;
    use UploadTrait;
    use VolumeTrait;

    public function uploadContentFile(
        string|array $key,
        string $path,
        ?int $existingFileId = null,
        array $params = [],
    ): array
    {
        $errors = [];

        if (is_array($key)) {
            $errorKey = $key[array_key_last($key)];
        } else {
            $errorKey = $key;
        }

        if ($this->hasUploadFromRequest($key, $existingFileId)) {
            $newFileId = null;
            $volumeFile = null;

            try {
                $volumeFile = $this->uploadFromRequest(
                    key: $key,
                    params: $params,
                );
            } catch (UploadException $e) {
                $errors = [$errorKey => $e->getError()];
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
                $errors = [$errorKey => 'unknown'];
            }

            if (!$errors && $volumeFile !== null) {
                $contentModel = $this->insertContentFile(
                    $volumeFile,
                    $path,
                );

                $newFileId = $contentModel->getId();
            }
        } else {
            if ($this->clearUploadFromRequest($key, $existingFileId)) {
                $newFileId = null;
            } else {
                $newFileId = $existingFileId;
            }
        }

        // If image updated with no errors then delete old
        if (!$errors && $existingFileId && $existingFileId !== $newFileId) {
            try {
                $this->deleteContentFile($existingFileId);
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
            }
        }

        return [$newFileId, $errors];
    }

    public function uploadContentFileFromValue(
        string|array $key,
        string $path,
        ?array $file,
        ?int $existingFileId = null,
        array $params = [],
    ): array
    {
        $errors = [];

        if (is_array($key)) {
            $errorKey = $key[array_key_last($key)];
        } else {
            $errorKey = $key;
        }

        if ($this->hasUploadFromValue($file, $existingFileId)) {
            $newFileId = null;
            $volumeFile = null;

            try {
                $volumeFile = $this->uploadFromValue(
                    file: $file,
                    params: $params,
                );
            } catch (UploadException $e) {
                $errors = [$errorKey => $e->getError()];
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
                $errors = [$errorKey => 'unknown'];
            }

            if (!$errors && $volumeFile !== null) {
                $contentModel = $this->insertContentFile(
                    $volumeFile,
                    $path,
                );

                $newFileId = $contentModel->getId();
            }
        } elseif ($this->clearUploadFromValue($file, $existingFileId)) {
            $newFileId = null;
        } else {
            $newFileId = $existingFileId;
        }

        // If image updated with no errors then delete old
        if (!$errors && $existingFileId && $existingFileId !== $newFileId) {
            try {
                $this->deleteContentFile($existingFileId);
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
            }
        }

        return [$newFileId, $errors];
    }

    public function uploadContentFileFromUri(
        string|array $key,
        string $path,
        ?string $uri,
        ?int $existingFileId = null,
        array $params = [],
    ): array
    {
        $errors = [];

        if (is_array($key)) {
            $errorKey = $key[array_key_last($key)];
        } else {
            $errorKey = $key;
        }

        $newFileId = null;

        if ($uri !== null) {
            $volumeFile = null;

            try {
                $volume = $this->getVolume(DirType::FILE);

                $volumeFile = $volume->writeFromUri(
                    pyncer_uid(),
                    $uri,
                    DirType::FILE,
                    $params,
                );
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
                $errors = [$errorKey => 'unknown'];
            }

            if (!$errors && $volumeFile !== null) {
                $contentModel = $this->insertContentFile(
                    $volumeFile,
                    $path,
                );

                $newFileId = $contentModel->getId();
            }
        } elseif ($this->clearUploadFromRequest($key, $existingFileId)) {
            $newFileId = null;
        } else {
            $newFileId = $existingFileId;
        }

        // If image updated with no errors then delete old
        if (!$errors && $existingFileId && $existingFileId !== $newFileId) {
            try {
                $this->deleteContentFile($existingFileId);
            } catch (VolumeException $e) {
                $this->logVolumeException($e);
            }
        }

        return [$newFileId, $errors];
    }
}
