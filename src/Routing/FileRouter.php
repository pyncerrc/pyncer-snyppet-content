<?php
namespace Pyncer\Snyppet\Content\Routing;

use Psr\Http\Message\ServerRequestInterface as PsrServerRequestInterface;
use Pyncer\Routing\AbstractComponentRouter;
use Pyncer\Source\SourceMap;

class FileRouter extends AbstractComponentRouter
{
    public function __construct(
        SourceMap $sourceMap,
        PsrServerRequestInterface $request
    ) {
        parent::__construct($sourceMap, $request);

        $this->setPathQueryName('file');
    }
}
