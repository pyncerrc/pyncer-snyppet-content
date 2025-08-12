<?php
namespace Pyncer\Snyppet\Content;

use Pyncer\Initializer;
use Pyncer\Snyppet\Content\FileMethod;

use const DIRECTORY_SEPARATOR as DS;

Initializer::define('Pyncer\Snyppet\Content\FILE_URI', null);
Initializer::define('Pyncer\Snyppet\Content\FILE_URI_PATH', '/content');

Initializer::define('Pyncer\Snyppet\Content\FILE_DIR', null);
Initializer::define('Pyncer\Snyppet\Content\FILE_DIR_PATH', DS . 'content' . DS . 'files');

Initializer::define('Pyncer\Snyppet\Content\CACHE_DIR', null);
Initializer::define('Pyncer\Snyppet\Content\CACHE_DIR_PATH', DS . 'content' . DS . 'cache');

Initializer::define('Pyncer\Snyppet\Content\TEMPORARY_DIR', null);
Initializer::define('Pyncer\Snyppet\Content\TEMPORARY_DIR_PATH', DS . 'content' . DS . 'temp');

Initializer::define('Pyncer\Snyppet\Content\FILE_METHOD', FileMethod::READFILE);
Initializer::define('Pyncer\Snyppet\Content\IMAGE_DRIVER', 'GD');
Initializer::define('Pyncer\Snyppet\Content\VOLUMES', null);

Initializer::define('Pyncer\Snyppet\Content\DEFAULT_FILE_VOLUME', 'local');
Initializer::define('Pyncer\Snyppet\Content\DEFAULT_CACHE_VOLUME', DEFAULT_FILE_VOLUME);
Initializer::define('Pyncer\Snyppet\Content\DEFAULT_TEMPORARY_VOLUME', DEFAULT_FILE_VOLUME);

Initializer::define('Pyncer\Snyppet\Content\MAPPER_QUERY_FILTERS', 'enabled eq true and deleted eq false');
Initializer::define('Pyncer\Snyppet\Content\MAPPER_QUERY_OPTIONS', null);
Initializer::define('Pyncer\Snyppet\Content\MAPPER_QUERY_ORDER_BY', 'order desc, name asc');

Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS', 'Pyncer\Validation\ALIAS_ALLOW_NUMERIC_CHARACTERS', true);
Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS', 'Pyncer\Validation\ALIAS_ALLOW_LOWER_CASE_CHARACTERS', true);
Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS', 'Pyncer\Validation\ALIAS_ALLOW_UPPER_CASE_CHARACTERS', true);
Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS', 'Pyncer\Validation\ALIAS_ALLOW_UNICODE_CHARACTERS', true);
Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS', 'Pyncer\Validation\ALIAS_SEPARATOR_CHARACTERS', '-');
Initializer::defineFrom('Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER', 'Pyncer\Validation\ALIAS_REPLACEMENT_CHARACTER', '');
