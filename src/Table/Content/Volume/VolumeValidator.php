<?php
namespace Pyncer\Snyppet\Content\Table\Content\Volume;

use Pyncer\Snyppet\Content\Table\Content\VolumeMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\EnumRule;
use Pyncer\Validation\Rule\IdRule;
use Pyncer\Validation\Rule\IntRule;
use Pyncer\Validation\Rule\StringRule;

class VolumeValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'cache_id',
            new IntRule(
                minValue: 0,
                allowNull: true,
            ),
            new IdRule(
                mapper: new VolumeMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'name',
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'type',
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'path',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );
    }
}
