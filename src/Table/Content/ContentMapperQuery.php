<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Data\MapperQuery\AbstractRequestMapperQuery;
use Pyncer\Data\Model\ModelInterface;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Database\Record\SelectQueryInterface;

class ContentMapperQuery extends AbstractRequestMapperQuery
{
    protected bool $includeData = false;
    protected bool $includeValues = false;

    public function overrideModel(
        ConnectionInterface $connection,
        ModelInterface $model,
        array $data
    ): ModelInterface
    {
        if ($this->includeData) {

        }

        if ($this->includeValues) {

        }

        return $model;
    }

    public function overrideQuery(
        SelectQueryInterface $query
    ): SelectQueryInterface
    {
        $this->includeData = false;
        $this->includeValues = false;

        return parent::overrideQuery($query);
    }

    protected function isValidFilter(
        string $left,
        mixed $right,
        string $operator
    ): bool
    {
        if ($left === 'enabled' && is_bool($right) && $operator === '=') {
            return true;
        }

        if ($left === 'deleted' && is_bool($right) && $operator === '=') {
            return true;
        }

        if ($left === 'type' && is_string($right) && ($operator === '=' || $operator === '!=')) {
            return true;
        }

        return parent::isValidFilter($left, $right, $operator);
    }

    protected function applyFilter(
        SelectQueryInterface $query,
        string $left,
        mixed $right,
        string $operator
    ): SelectQueryInterface
    {

        return parent::isValidFilter($query, $left, $right, $operator);
    }

    protected function isValidOption(string $option): bool
    {
        switch ($option) {
            case 'include-data':
            case 'include-values':
                return true;
        }

        return parent::isValidOption($option);
    }

    protected function applyOption(
        SelectQueryInterface $query,
        string $option
    ): SelectQueryInterface
    {
        if ($option === 'include-data') {
            $this->includeData = true;
            return $query;
        } elseif ($option === 'include-values') {
            $this->includeValues = true;
            return $query;
        }

        return parent::applyOption($query, $option);
    }

    protected function isValidOrderBy(string $key, string $direction): bool
    {
        switch ($key) {
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
        if ($key === 'random') {
            $connection = $query->getDatabase();
            return ['@', $connection->functions($query->getTable(), 'Rand'), $direction];
        }

        return parent::getOrderByColumn($query, $key, $direction);
    }
}
