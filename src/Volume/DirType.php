<?php
namespace Pyncer\Snyppet\Content\Volume;

enum DirType: string
{
    case FILE = 'file';
    case CACHE = 'cache';
    case TEMPORARY = 'temporary';
}
