<?php
namespace Pyncer\Snyppet\Content\Exception;

use Pyncer\Exception\RuntimeException;
use Throwable;

class UploadException extends RuntimeException
{
    protected string $error;

    public function __construct(
        string $error,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            'File could not be uploaded. (' . $error . ')',
            $code,
            $previous
        );

        $this->error = $error;
    }

    public function getError(): int|string
    {
        return $this->error;
    }
}
