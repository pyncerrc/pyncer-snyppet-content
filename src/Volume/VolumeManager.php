<?php
namespace Pyncer\Snyppet\Content\Volume;

use Pyncer\Database\ConnectionInterface;
use Pyncer\Iterable\Iterator;
use Pyncer\Snyppet\Content\Volume\Exception\VolumeNotFoundException;
use Pyncer\Snyppet\Content\Volume\VolumeInterface;

use function Pyncer\Utility\map_defines as pyncer_map_defines;

class VolumeManager extends Iterator
{
    /** @var array<int, string> Map volume id to its alias. **/
    protected array $idMap = [];

    /**
     * @param ConnectionInterface $connection A database connection.
     * @param null|array<string> $volumes An array of volume aliases to
     *  limit what volumes are available.
     */
    public function __construct(
        protected ConnectionInterface $connection,
        ?array $volumes = null,
    ) {
        $this->initialize($volumes);
    }

    protected function initialize(?array $volumes): void
    {
        $result = $this->connection->select('content__volume')
            ->where(['enabled' => true])
            ->result();

        foreach ($result as $row) {
            if ($volumes !== null &&
                !in_array($row['alias'], $volumes)
            ) {
                continue;
            }

            $namespace = '';
            if (defined('Pyncer\ENV_NAMESPACE')) {
                $namespace = \Pyncer\ENV_NAMESPACE;
            }

            $path = pyncer_map_defines($row['path'], $namespace);

            if ($row['params'] === null) {
                $params = [];
            } else {
                $params = pyncer_map_defines(json_decode($row['params'], true), $namespace),
            }

            $driver = new Driver($row['volume'], $path, $params);

            $this->values[$row['alias']] = $driver->getVolume($row['id'], $row['alias']);
            $this->idMap[$row['id']] = $row['alias'];
        }
    }

    public function get(string $alias): VolumeInterface
    {
        if (!array_key_exists($alias, $this->values)) {
            throw new VolumeNotFoundException($alias);
        }

        return $this->values[$alias];
    }

    public function has(string $alias): bool
    {
        if (!array_key_exists($alias, $this->values)) {
            return false;
        }

        return true;
    }

    public function getFromId(int $id): VolumeInterface
    {
        $alias = $this->idMap[$id] ?? null;

        if ($alias === null) {
            throw new VolumeNotFoundException($id);
        }

        return $this->values[$alias];
    }

    public function hasFromId(int $id): bool
    {
        $alias = $this->idMap[$id] ?? null;

        if ($alias === null) {
            return false;
        }

        return true;
    }
}
