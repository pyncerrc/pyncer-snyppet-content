<?php
namespace Pyncer\Snyppet\Content\Component\Module;

use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Component\Module\AbstractModule;
use Pyncer\Http\Message\JsonResponse;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Content\Component\Forge\LogVolumeExceptionTrait;
use Pyncer\Snyppet\Content\Component\Forge\VolumeTrait;
use Pyncer\Snyppet\Content\Component\Forge\UploadTrait;
use Pyncer\Snyppet\Content\Volume\DirType;

class PostTemporaryFileModule extends AbstractModule
{
    use UploadTrait;
    use VolumeTrait;
    use LogVolumeExceptionTrait;

    protected bool $multiple = false;
    protected ?array $allowedMediaTypes = null;
    protected null|int|string $volumeIdentifier = null;

    public function getMultiple(): bool
    {
        return $this->multiple;
    }
    public function setMultiple(bool $value): static
    {
        $this->multiple = $value;
        return $this;
    }

    public function getAllowedMediaTypes(): ?array
    {
        return $this->allowedMediaTypes;
    }
    public function setAllowedMediaTypes(?array $value): static
    {
        $this->allowedMediaTypes = $value;
        return $this;
    }

    public function getVolumeIdentifier(): null|int|string
    {
        return $this->volumeIdentifier;
    }
    public function setVolumeIdentifier(null|int|string $value): static
    {
        $this->volumeIdentifier = $value;
        return $this;
    }

    protected function getPrimaryResponse(): PsrResponseInterface
    {
        if ($this->getMultiple()) {
            return $this->getMultipleUploadResponse();
        }

        return $this->getSingleUploadResponse();
    }

    protected function getMultipleUploadResponse(): PsrResponseInterface
    {
        return new Response(
            Status::SERVER_ERROR_501_NOT_IMPLEMENTED,
        );
    }

    protected function getSingleUploadResponse(): PsrResponseInterface
    {
        $error = null;

        try {
            $volumeFile = $this->uploadFromRequest(
                'file',
                DirType::TEMPORARY,
                ['volume' => $this->getVolumeIdentifier()],
            );
        } catch (UploadException $e) {
            $error = $e->getError();
        } catch (VolumeException $e) {
            $this->logVolumeException($e);
            $error = 'unknown';
        }

        if ($error !== null) {
            return new JsonResponse(
                Status::CLIENT_ERROR_422_UNPROCESSABLE_ENTITY,
                ['errors' => ['file' => $error]]
            );
        }

        return new JsonResponse(
            Status::SUCCESS_201_CREATED,
            [
                'uri' => $volumeFile->getUri(),
            ]
        );
    }
}
