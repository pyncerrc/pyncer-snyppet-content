<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use finfo;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Pyncer\App\Identifier as ID;
use Pyncer\Http\Message\FileStream;
use Pyncer\Http\Message\Response;
use Pyncer\Http\Message\Status;
use Pyncer\Snyppet\Content\FileMethod;

use const Pyncer\Snyppet\Content\FILE_METHOD as PYNCER_SNYPPET_CONTENT_FILE_METHOD;
use const FILEINFO_MIME_TYPE;
use const FILTER_VALIDATE_URL;

trait FileResponseTrait
{

    protected function getFileResponse(
        string $filename,
        string $uri,
    ): PsrResponseInterface
    {
        if (filter_var($uri, FILTER_VALIDATE_URL) !== false) {
            return (new Response(Status::REDIRECTION_302_FOUND))
                ->withHeader('Location', $uri);
        }

        if (PYNCER_SNYPPET_CONTENT_FILE_METHOD === FileMethod::ACCEL_REDIRECT) {
            return (new Response(Status::SUCCESS_200_OK))
                ->withHeader('X-Accel-Redirect', $uri);
        }

        if (PYNCER_SNYPPET_CONTENT_FILE_METHOD === FileMethod::SENDFILE) {
            return (new Response(Status::SUCCESS_200_OK))
                ->withHeader('X-Sendfile', $uri);
        }

        $fileStream = new FileStream($uri);
        $fileStream->setUseReadFile(true);

        $response = new Response(
            status: Status::SUCCESS_200_OK,
            body: $fileStream,
        );

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($uri);

        $response = $response->withHeader('Content-Type', $mimeType);
        $response = $response->withHeader('Content-Disposition', 'filename="' . $filename . '"');
        $response = $response->withHeader('Content-Length', filesize($uri));

        return $response;
    }
}
