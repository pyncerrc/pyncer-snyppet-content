<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\Record\SelectQueryInterface;

use function Pyncer\Array\unset_keys as pyncer_array_unset_keys;

class ContentMapperQuery extends AbstractRequestMapperQuery
{
    public function overrideModel(
        ModelInterface $model,
        array $data,
    ): ModelInterface
    {
        if (!$this->getOptions()) {
            return $model;
        }

        if ($this->getOptions()->hasOption('include-content-values')) {
            $result = $this->getConnection()->select('content__value')
                ->columns('key', 'value')
                ->where(['content_id' => $model->getId()])
                ->result();

            $extraData = [];
            foreach ($result as $row) {
                $extraData[$row['key']] = $row['value'];
            }

            $extraData = pyncer_array_unset_keys($extraData, $model->getKeys());
            $model->addExtraData($extraData);
        }

        if ($this->getOptions()->hasOption('include-content-data')) {
            $result = $this->getConnection()->select('content__data')
                ->columns('key', 'type', 'value')
                ->where(['content_id' => $model->getId()])
                ->result();

            $extraData = [];
            foreach ($result as $row) {
                $extraData[$row['key']] = [
                    'type' => $row['type'],
                    'value' => $row['value'],
                ];
            }

            $extraData = pyncer_array_unset_keys($extraData, $model->getKeys());
            $model->addExtraData($extraData);
        }

        return $model;
    }

    protected function isValidFilter(
        string $left,
        mixed $right,
        string $operator,
    ): bool
    {
        if ($left === 'uid' &&
            is_string($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'enabled' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'deleted' &&
            is_bool($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        if ($left === 'type' &&
            is_string($right) &&
            ($operator === '=' || $operator === '!=')
        ) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }

    /* protected function applyFilter(
        SelectQueryInterface $query,
        string $left,
        mixed $right,
        string $operator
    ): SelectQueryInterface
    {
        return parent::applyFilter($query, $left, $right, $operator);
    } */

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-content-data':
            case 'include-content-values':
                return true;
        }

        return parent::isValidOption($option);
    }

    protected function applyOption(
        SelectQueryInterface $query,
        string $option
    ): SelectQueryInterface
    {
        if ($option === 'include-content-data') {
            $this->includeData = true;
            return $query;
        } elseif ($option === 'include-content-values') {
            $this->includeValues = true;
            return $query;
        }

        return parent::applyOption($query, $option);
    }

    protected function isValidOrderBy(string $key, string $direction): bool
    {
        switch ($key) {
            case 'insert_date_time':
            case 'update_date_time':
            case 'order':
            case 'alias':
            case 'filename':
            case 'type':
            case 'name':
            case 'enabled':
            case 'random':
                return true;
        }

       return parent::isValidOrderBy($key, $direction);
    }

    protected function getOrderByColumn(
        SelectQueryInterface $query,
        $key,
        $direction
    ): array
    {
        switch ($key) {
            case 'update_date_time':
                $function = $this->getConnection()->functions(
                    'contact',
                    'Coalesce'
                )->arguments('update_date_time', 'insert_date_time');
                return [$function, $direction];
            case 'random':
                $connection = $query->getDatabase();
                return ['@', $connection->functions($query->getTable(), 'Rand'), $direction];
        }

        return parent::getOrderByColumn($query, $key, $direction);
    }
}
