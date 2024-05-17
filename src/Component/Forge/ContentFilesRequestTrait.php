<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\Snyppet\Content\Component\Forge\ContentFilesTrait;

use function Pyncer\Array\get_recursive as pyncer_array_get_recursive;

trait ContentFilesRequestTrait
{
    use ContentFilesTrait;

    protected array $temporaryFiles = [];
    protected array $replacedFileIds = [];
    protected array $insertedFileIds = [];

    protected array $keyPaths = [];

    public function validateContentFile(
        string|array $key,
        string $path,
        ?int $existingFileId = null,
        array $params = []
    ): array
    {
        [$fileId, $errors] = $this->uploadContentFile(
            key: $key,
            path: $this->path,
            existingFileId: $existingFileId,
            params: $params,
        );

        $this->trackContentFileChanges($key, $fileId, $existingFileId);

        return [$fileId, $errors];
    }

    public function validateContentFileFromValue(
        string|array $key,
        string $path,
        ?array $file,
        ?int $existingFileId = null,
        array $params = []
    ): array
    {
        [$fileId, $errors] = $this->uploadContentFileFromValue(
            key: $key,
            path: $this->path,
            file: $file,
            existingFileId: $existingFileId,
            params: $params,
        );

        $this->trackContentFileChanges($key, $fileId, $existingFileId);

        return [$fileId, $errors];
    }

    public function validateContentFileFromUri(
        string|array $key,
        string $path,
        ?string $uri,
        ?int $existingFileId = null,
        array $params = []
    ): array
    {
        [$fileId, $errors] = $this->uploadContentFileFromValue(
            key: $key,
            path: $this->path,
            uri: $uri,
            existingFileId: $existingFileId,
            params: $params,
        );

        $this->trackContentFileChanges($key, $fileId, $existingFileId);

        return [$fileId, $errors];
    }

    private function trackContentFileChanges(
        string|array $key,
        ?int $fileId,
        ?int $existingFileId,
    ) {

        if ($existingFileId !== $fileId) {
            if ($fileId !== null) {
                $file = $this->getFileValueFromRequest($key, $existingFileId);

                if ($file !== null) {
                    $this->temporaryFiles[] = $file;
                }

                $this->insertedFileIds[] = $fileId;
            }

            if ($existingFileId !== null) {
                $this->replacedFileIds[] = $existingFileId;
            }
        }
    }

    public function commitContentFiles(): void
    {
        foreach ($this->temporaryFiles as $file) {
            $filename = $file['filename'] ?? null;
            $uri = $file['uri'] ?? null;

            $this->deleteTemporaryFile($filename, $uri);
        }

        foreach ($this->replacedFileIds as $fileId) {
            $this->deleteContentFile($fileId);
        }
    }

    public function rollbackContentFiles(): void
    {
        foreach ($this->insertedFileIds as $fileId) {
            $this->deleteContentFile($fileId);
        }
    }
}
