<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Content\Volume\DirType;
use Pyncer\Snyppet\Content\Volume\VolumeInterface;

trait VolumeTrait
{
    protected function getVolume(
        DirType $dirType = DirType::FILE,
        array $params = [],
    ): VolumeInterface
    {
        switch ($dirType) {
            case DirType::TEMPORARY:
                $volume = $params['temporary_volume'] ?? $params['volume'] ?? null;
                break;
            case DirType::CACHE:
                $volume = $params['cache_volume'] ?? $params['volume'] ?? null;
                break;
            case DirType::FILE:
            default:
                $volume = $params['file_volume'] ?? $params['volume'] ?? null;
                break;
        }

        if ($volume === null) {
            switch ($dirType) {
                case DirType::TEMPORARY:
                    return $this->get(ID::content('temporary_volume'));
                case DirType::CACHE:
                    return $this->get(ID::content('cache_volume'));
                case DirType::FILE:
                default:
                    return $this->get(ID::content('file_volume'));
            }
        }

        $volumes = $this->get(ID::content('volumes'));

        if (is_int($volume)) {
            return $volumes->getFromId($volume);
        }

        return $volumes->get($volume);
    }
}
