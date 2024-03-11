<?php
namespace Pyncer\Snyppet\Content;

use Pyncer\Snyppet\Content\FileMethod;

use const DIRECTORY_SEPARATOR as DS;

defined('Pyncer\Snyppet\Content\FILE_URI') or define('Pyncer\Snyppet\Content\FILE_URI', null);
defined('Pyncer\Snyppet\Content\FILE_URI_PATH') or define('Pyncer\Snyppet\Content\FILE_URI_PATH', '/content');

defined('Pyncer\Snyppet\Content\FILE_DIR') or define('Pyncer\Snyppet\Content\FILE_DIR', null);
defined('Pyncer\Snyppet\Content\FILE_DIR_PATH') or define('Pyncer\Snyppet\Content\FILE_DIR_PATH', DS . 'content' . DS . 'files');

defined('Pyncer\Snyppet\Content\CACHE_DIR') or define('Pyncer\Snyppet\Content\CACHE_PATH', null);
defined('Pyncer\Snyppet\Content\CACHE_DIR_PATH') or define('Pyncer\Snyppet\Content\CACHE_DIR_PATH', DS . 'content' . DS . 'cache');

defined('Pyncer\Snyppet\Content\TEMPORARY_DIR') or define('Pyncer\Snyppet\Content\TEMPORARY_DIR', null);
defined('Pyncer\Snyppet\Content\TEMPORARY_DIR_PATH') or define('Pyncer\Snyppet\Content\TEMPORARY_DIR_PATH', DS . 'content' . DS . 'temp');

defined('Pyncer\Snyppet\Content\FILE_METHOD') or define('Pyncer\Snyppet\Content\FILE_METHOD', FileMethod::READFILE);
defined('Pyncer\Snyppet\Content\IMAGE_DRIVER') or define('Pyncer\Snyppet\Content\IMAGE_DRIVER', 'GD');
defined('Pyncer\Snyppet\Content\VOLUMES') or define('Pyncer\Snyppet\Content\VOLUMES', null);

defined('Pyncer\Snyppet\Content\DEFAULT_FILE_VOLUME') or define('Pyncer\Snyppet\Content\DEFAULT_FILE_VOLUME', 'local');
defined('Pyncer\Snyppet\Content\DEFAULT_CACHE_VOLUME') or define('Pyncer\Snyppet\Content\DEFAULT_CACHE_VOLUME', DEFAULT_FILE_VOLUME);
defined('Pyncer\Snyppet\Content\DEFAULT_TEMPORARY_VOLUME') or define('Pyncer\Snyppet\Content\DEFAULT_TEMPORARY_VOLUME', DEFAULT_FILE_VOLUME);

defined('Pyncer\Snyppet\Content\MAPPER_QUERY_FILTERS') or define('Pyncer\Snyppet\Content\MAPPER_QUERY_FILTERS', 'enabled eq true and deleted eq false');
defined('Pyncer\Snyppet\Content\MAPPER_QUERY_OPTIONS') or define('Pyncer\Snyppet\Content\MAPPER_QUERY_OPTIONS', null);
defined('Pyncer\Snyppet\Content\MAPPER_QUERY_ORDER_BY') or define('Pyncer\Snyppet\Content\MAPPER_QUERY_ORDER_BY', 'order desc, name asc');

defined('Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS') or define('Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS', true);
defined('Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS') or define('Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS', true);
defined('Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS') or define('Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS', true);
defined('Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS') or define('Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS', true);
defined('Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS') or define('Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS', '-');
defined('Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER') or define('Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER', '');
