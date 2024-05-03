<?php
namespace Pyncer\Snyppet\Content\Table\Content\History;

use Pyncer\Snyppet\Content\Table\Content\History\HistoryMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Data\Validation\Rule\IdRule;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\RequiedRule;
use Pyncer\Validation\Rule\StringRule;

class DataValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'history_id',
            new RequiedRule(),
            new IdRule(
                mapper: new HistoryMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'content_id',
            new RequiedRule(),
            new IntRule(
                minValue: 1,
            ),
        );

        $this->addRules(
            'key',
            new RequiedRule(),
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'type',
            new RequiedRule(),
            new StringRule(
                maxLength: 125,
            ),
        );

        $this->addRules(
            'value',
            new RequiedRule(),
            new StringRule(
                maxLength: 400000,
            ),
        );
    }
}
