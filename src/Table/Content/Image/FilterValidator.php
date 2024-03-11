<?php
namespace Pyncer\Snyppet\Content\Table\Content\Image;

use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\EnumRule;
use Pyncer\Validation\Rule\StringRule;

class FilterValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'alias',
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRules(
            'name',
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRules(
            'fit',
            new EnumRule(
                values: ['inside', 'outside', 'fill'],
            ),
        );

        $this->addRules(
            'scale',
            new EnumRule(
                values: ['down', 'up', 'any'],
            ),
        );

        $this->addRules(
            'crop_x',
            new EnumRule(
                values: ['left', 'center', 'right'],
            ),
        );

        $this->addRules(
            'crop_y',
            new EnumRule(
                values: ['top', 'center', 'bottom'],
            ),
        );

        $this->addRules(
            'padding',
            new BoolRule(),
        );

        $this->addRules(
            'background_color',
            new StringRule(
                maxLength: 7,
                allowNull: true,
            ),
        );
    }
}
