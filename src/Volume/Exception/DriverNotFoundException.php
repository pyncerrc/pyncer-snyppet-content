<?php
namespace Pyncer\Snyppet\Content\Volume\Exception;

use Pyncer\Image\Exception\Exception;
use Pyncer\Exception\RuntimeException;
use Throwable;

class DriverNotFoundException extends RuntimeException implements
    Exception
{
    protected string $driver;

    public function __construct(
        string $driver,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        $this->driver = $driver;

        parent::__construct(
            'The specified volume driver, ' . $driver . ', was not found.',
            $code,
            $previous
        );
    }

    public function getDriver(): string
    {
        return $this->driver;
    }
}
