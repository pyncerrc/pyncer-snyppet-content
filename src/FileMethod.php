<?php
namespace Pyncer\Snyppet\Content;

enum FileMethod: string
{
    case READFILE = 'readfile';
    case SENDFILE = 'sendfile';
    case ACCEL_REDIRECT = 'accel-redirect';
}
