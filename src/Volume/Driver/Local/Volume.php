<?php
namespace Pyncer\Snyppet\Content\Volume\Driver\Local;

use Psr\Http\Message\UploadedFileInterface;
use Pyncer\Http\Message\Uri;
use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Snyppet\Content\Volume\AbstractVolume;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeFileNotFoundException;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeFileExistsException;
use Pyncer\Snyppet\Content\Volume\VolumeFile;
use Throwable;

use function Pyncer\IO\clean_filename as pyncer_io_clean_filename;
use function Pyncer\IO\clean_dir as pyncer_io_clean_dir;
use function Pyncer\IO\clean_path as pyncer_io_clean_path;
use function Pyncer\IO\copy as pyncer_io_copy;
use function Pyncer\IO\chmod as pyncer_io_chmod;
use function Pyncer\IO\delete as pyncer_io_delete;
use function Pyncer\IO\extension as pyncer_io_extension;
use function Pyncer\IO\filename as pyncer_io_filename;
use function Pyncer\IO\make_dir as pyncer_io_make_dir;
use function Pyncer\uid as pyncer_uid;

use const DIRECTORY_SEPARATOR as DS;
use const Pyncer\Snyppet\Content\FILE_DIR as PYNCER_SNYPPET_CONTENT_FILE_DIR;
use const Pyncer\Snyppet\Content\FILE_DIR_PATH as PYNCER_SNYPPET_CONTENT_FILE_DIR_PATH;
use const Pyncer\Snyppet\Content\TEMPORARY_DIR as PYNCER_SNYPPET_CONTENT_CACHE_DIR;
use const Pyncer\Snyppet\Content\TEMPORARY_DIR_PATH as PYNCER_SNYPPET_CONTENT_CACHE_DIR_PATH;
use const Pyncer\Snyppet\Content\TEMPORARY_DIR as PYNCER_SNYPPET_CONTENT_TEMPORARY_DIR;
use const Pyncer\Snyppet\Content\TEMPORARY_DIR_PATH as PYNCER_SNYPPET_CONTENT_TEMPORARY_DIR_PATH;

class Volume extends AbstractVolume
{
    public function getUri(VolumeFile $volumeFile): string
    {
        if (!$this->validateUri($volumeFile->getUri())) {
            throw new VolumeException(
                'Invlaid volume file URI. (' . $volumeFile->getUri() . ')'
            );
        }

        return $this->getBaseDir($volumeFile->getDirType()) .
            pyncer_io_clean_path($volumeFile->getUri());
    }

    public function read(VolumeFile $volumeFile): string
    {
        if (!$this->validateUri($volumeFile->getUri())) {
            throw new VolumeException(
                'Invlaid volume file URI. (' . $volumeFile->getUri() . ')'
            );
        }

        $file = $this->getBaseDir($volumeFile->getDirType()) .
            pyncer_io_clean_path($volumeFile->getUri());

        if (!file_exists($file)) {
            throw new VolumeFileNotFoundException(
                $this->getAlias(),
                $volumeFile->getUri(),
            );
        }

        $contents = @file_get_contents($file);

        if ($contents === false) {
            throw new VolumeException(
                'The volume file could not be read. (' . $file . ')'
            );
        }

        return $contents;
    }

    public function write(
        string $filename,
        string $contents,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile
    {
        $stream = tmpfile();
        fwrite($stream, $contents);

        $volumeFile = $this->writeFromStream(
            $filename,
            $stream,
            $dirType,
            $params
        );

        fclose($stream);

        return $volumeFile;
    }

    public function writeFromUploadedFile(
        UploadedFileInterface $uploadedFile,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile
    {
        $name = $params['name'] ?? $uploadedFile->getClientFilename();
        $name = pyncer_io_filename($name, true);

        $extension = $this->cleanExtension(
            $uploadedFile->getClientFilename(),
            $uploadedFile->getClientMediaType(),
        );

        $filename = $params['filename'] ?? $uploadedFile->getClientFilename();
        $filename = pyncer_io_filename($filename);
        $filename .= ($extension !== null ? '.' . $extension : '');

        [$file, $uri] = $this->getFileAndUri($filename, $dirType, $params);

        try {
            $uploadedFile->moveTo($file);
        } catch (Throwable $e) {
            throw new VolumeException(
                message: 'The volume file could not be written. (' . $file . ')',
                previous: $e,
            );
        }

        try {
            pyncer_io_chmod($file);
        } catch (Throwable $e) {
            // Do nothing
        }

        return new VolumeFile(
            $this,
            $name,
            $uri,
            $dirType,
            $filename,
        );
    }

    public function writeFromUri(
        string $filename,
        string $uri,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile
    {
        $name = $params['name'] ?? $filename;
        $name = pyncer_io_filename($name, true);

        $extension = $this->cleanExtension($filename);
        if ($extension === null) {
            // If its a URL, test against path only
            if (str_contains($uri, '://')) {
                $extension = $this->cleanExtension((new Uri($uri))->getPath());
            } else {
                $extension = $this->cleanExtension($uri);
            }
        }

        $filename = $params['filename'] ?? $filename;
        $filename = pyncer_io_filename($filename, true);
        $filename .= ($extension !== null ? '.' . $extension : '');

        if (!file_exists($uri)) {
            $contents = @file_get_contents($uri);
            if ($contents === false) {
                throw new VolumeException(
                    message: 'The URI could not be read. (' . $uri . ')',
                );
            }

            return $this->write(
                $filename,
                $contents,
                $dirType,
                $params,
            );
        }

        [$file, $volumeFileUri] = $this->getFileAndUri($filename, $dirType, $params);

        try {
            pyncer_io_copy($uri, $file);
        } catch (Throwable $e) {
            throw new VolumeException(
                message: 'The volume file could not be written. (' . $file . ')',
                previous: $e,
            );
        }

        try {
            pyncer_io_chmod($file);
        } catch (Throwable $e) {
            // Do nothing
        }

        return new VolumeFile(
            $this,
            $name,
            $volumeFileUri,
            $dirType,
            $filename,
        );
    }

    public function writeFromStream(
        string $filename,
        $stream,
        DirType $dirType = DirType::FILE,
        array $params = []
    ): VolumeFile
    {
        $name = $params['name'] ?? $filename;
        $name = pyncer_io_filename($name, true);

        $extension = $this->cleanExtension($filename);

        $filename = $params['filename'] ?? $filename;
        $filename = pyncer_io_filename($filename, true);
        $filename .= ($extension !== null ? '.' . $extension : '');

        [$file, $uri] = $this->getFileAndUri($filename, $dirType, $params);

        $position = ftell($stream);

        rewind($stream);

        if (file_put_contents($file, $stream) === false) {
            fseek($stream, $position);
            throw new VolumeException(
                message: 'The volume file could not be written. (' . $file . ')',
            );
        }

        fseek($stream, $position);

        try {
            pyncer_io_chmod($file);
        } catch (Throwable $e) {
            // Do nothing
        }

        return new VolumeFile(
            $this,
            $name,
            $uri,
            $dirType,
            $filename,
        );
    }

    public function writeFromVolumeFile(
        VolumeFile $volumeFile,
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): VolumeFile
    {
        $uri = $volumeFile->getVolume()->getUri($volumeFile);

        return $this->writeFromUri(
            $volumeFile->getFilename(),
            $uri,
            $dirType,
            $params,
        )->withName($volumeFile->getName());
    }

    public function delete(VolumeFile $volumeFile): void
    {
        if (!$this->validateUri($volumeFile->getUri())) {
            throw new VolumeException(
                'Invlaid volume file URI. (' . $volumeFile->getUri() . ')'
            );
        }

        $dir = $this->getBaseDir($volumeFile->getDirType());
        $file = $dir . pyncer_io_clean_path($volumeFile->getUri());

        if (!file_exists($file)) {
            throw new VolumeFileNotFoundException(
                $this->getAlias(),
                $volumeFile->getUri(),
            );
        }

        try {
            pyncer_io_delete($file);
        } catch (Throwable $e) {
            throw new VolumeException(
                message: 'The volume file could not be deleted . (' . $file . ')',
                previous: $e,
            );
        }
    }

    private function validateUri(string $uri): bool
    {
        $uri = pyncer_io_clean_path($uri);

        if ($uri === '') {
            return false;
        }

        // Prevent going up to parent directories.
        if (str_contains($uri, DS . '..' . DS)) {
            return false;
        }

        return true;
    }

    protected function getBaseDir(DirType $dirType): string
    {
        switch ($dirType) {
            case DirType::CACHE:
                $dir = PYNCER_SNYPPET_CONTENT_CACHE_DIR ??
                    PYNCER_SNYPPET_CONTENT_FILE_DIR ??
                    getcwd();
                $dir = pyncer_io_clean_dir($dir);

                $path = PYNCER_SNYPPET_CONTENT_CACHE_DIR_PATH;
                $path = pyncer_io_clean_path($path);
                break;
            case DirType::TEMPORARY:
                $dir = PYNCER_SNYPPET_CONTENT_TEMPORARY_DIR ??
                    PYNCER_SNYPPET_CONTENT_FILE_DIR ??
                    getcwd();
                $dir = pyncer_io_clean_dir($dir);

                $path = PYNCER_SNYPPET_CONTENT_TEMPORARY_DIR_PATH;
                $path = pyncer_io_clean_path($path);
                break;
            case DirType::FILE:
            default:
                $dir = PYNCER_SNYPPET_CONTENT_FILE_DIR ??
                    getcwd();
                $dir = pyncer_io_clean_dir($dir);

                $path = PYNCER_SNYPPET_CONTENT_FILE_DIR_PATH;
                $path = pyncer_io_clean_path($path);
                break;
        }

        return $dir . $path;
    }

    protected function getUploadPath(DirType $dirType, ?string $path): string
    {
        if ($dirType === DirType::TEMPORARY) {
            return '';
        }

        $dir = $this->getBaseDir($dirType);
        $path = pyncer_io_clean_path($path ?? date('Y-m-d'));

        if (!is_dir($dir . $path)) {
            pyncer_io_make_dir($dir . $path);
        }

        return $path;
    }

    protected function getFileAndUri(
        string $filename,
        DirType $dirType,
        array $params = []
    ): array
    {
        $dir = $this->getBaseDir($dirType);
        $path = $this->getUploadPath($dirType, $params['path'] ?? null);

        switch ($dirType) {
            case DirType::TEMPORARY:
                $extension = pyncer_io_extension($filename);
                $filename = pyncer_uid();
                break;
            case DirType::CACHE:
            case DirType::FILE:
            default:
                $extension = pyncer_io_extension($filename);
                $filename = pyncer_io_clean_filename($filename);
                $filename = pyncer_io_filename($filename, true);
                break;
        }

        $file = $dir . $path . DS . $filename .
            ($extension !== null ? '.' . $extension : '');

        if ($params['overwrite'] ?? null) {
            if (file_exists($file)) {
                pyncer_io_delete($file);
            }
        } else {
            $suffix = 0;
            do {
                $file = $dir . $path . DS . $filename .
                    ($suffix ? '-' . $suffix : '') .
                    ($extension !== null ? '.' . $extension : '');

                ++$suffix;
            } while (file_exists($file));
        }

        $filename = pyncer_io_filename($file);

        $uri = str_replace(DS, '/', $path . '/' . $filename);

        return [$file, $uri];
    }
}
