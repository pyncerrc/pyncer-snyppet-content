<?php
namespace Pyncer\Snyppet\Content;

enum MediaType: string
{
    // Images
    case JPG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case WEBP = 'image/webp';
    case AVIF = 'image/avif';
    case SVG = 'image/svg+xml';

    // Videos
    case MP4 = 'video/mp4';
    case MKV = 'video/x-matroska';
    case OGV = 'video/ogg';
    case WEBM = 'video/webm';

    // Audio
    case MP3 = 'audio/mpeg';
    case WAV = 'audio/wav';
    case AAC = 'audio/aac';
    case OGG = 'audio/ogg';
    case FLAC = 'audio/flac';

    // Documents
    case PDF = 'application/pdf';
    case DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    case XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    case PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
    case ODT = 'application/vnd.oasis.opendocument.text';
    case ODS = 'application/vnd.oasis.opendocument.spreadsheet';
    case ODP = 'application/vnd.oasis.opendocument.presentation';
    case PAGES = 'application/vnd.apple.pages';
    case NUMBERS = 'application/vnd.apple.numbers';
    case KEYNOTE = 'application/vnd.apple.keynote';
    case TXT = 'text/plain';

    // Archives
    case ZIP = 'application/zip';
    case RAR = 'application/x-rar-compressed';
    case _7Z = 'application/x-7z-compressed';
    case TAR = 'application/x-tar';
    case GZIP = 'application/gzip';

    // Other
    case JSON = 'application/json';
    case XML = 'application/xml';

    public const EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
        'image/svg+xml' => 'svg',

        'video/mp4' => 'mp4',
        'video/x-matroska' => 'mkv',
        'video/ogg' => 'ogv',
        'video/webm' => 'webm',

        // Audio
        'audio/mpeg' => 'mp3',
        'audio/wav' => 'wav',
        'audio/aac' => 'aac',
        'audio/ogg' => 'ogg',
        'audio/flac' => 'flac',

        // Documents
        'application/pdf' => 'pdf',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
        'application/vnd.oasis.opendocument.text' => 'odt',
        'application/vnd.oasis.opendocument.spreadsheet' => 'ods',
        'application/vnd.oasis.opendocument.presentation' => 'odp',
        'application/vnd.apple.pages' => 'pages',
        'application/vnd.apple.numbers' => 'numbers',
        'application/vnd.apple.keynote' => 'keynote',
        'text/plain' => 'txt',

        // Archives
        'application/zip' => 'zip',
        'application/x-rar-compressed' => 'rar',
        'application/x-7z-compressed' => '7z',
        'application/x-tar' => 'tar',
        'application/gzip' => 'gzip',

        // Other
        'application/json' => 'json',
        'application/xml' => 'xml',
    ];

    public function getMediaType(): string
    {
        return $this->value;
    }

    public function isImageMediaType(): bool
    {
        return in_array($tihs->value, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/avif',
            'image/svg+xml',
        ]);
    }

    public function isVideoMediaType(): bool
    {
        return in_array($tihs->value, [
            'video/mp4',
            'video/x-matroska',
            'video/ogg',
            'video/webm',
        ]);
    }

    public function isAudioMediaType(): bool
    {
        return in_array($tihs->value, [
            'audio/mpeg',
            'audio/wav',
            'audio/aac',
            'audio/ogg',
            'audio/flac',
        ]);
    }

    public function isDocumentMediaType(): bool
    {
        return in_array($tihs->value, [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.oasis.opendocument.text',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.apple.pages',
            'application/vnd.apple.numbers',
            'application/vnd.apple.keynote',
            'text/plain',
        ]);
    }

    public function isArchiveMediaType(): bool
    {
        return in_array($tihs->value, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/x-tar',
            'application/gzip',
        ]);
    }

    public function getExtension(): string
    {
        return self::EXTENSIONS[$this->value];
    }
}
