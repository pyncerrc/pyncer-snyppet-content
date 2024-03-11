<?php
use Pyncer\Snyppet\SnyppetManager;
use Pyncer\Snyppet\Content\Snyppet;

SnyppetManager::register(new Snyppet(
    'content',
    dirname(__DIR__),
    [
        'initialize' => ['Initialize'],
    ]
));
