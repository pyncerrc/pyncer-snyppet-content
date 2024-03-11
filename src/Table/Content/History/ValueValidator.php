<?php
namespace Pyncer\Snyppet\Content\Table\Content\History;

use Pyncer\Snyppet\Content\Table\Content\History\HistoryMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Data\Validation\Rule\IdRule;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\StringRule;

class ValueValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'history_id',
            new IdRule(
                mapper: new HistoryMapper($this->getConnection()),
            ),
        );

        $this->addRules(
            'content_id',
            new IntRule(
                minValue: 1,
            ),
        );

        $this->addRules(
            'group',
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'key',
            new StringRule(
                maxLength: 50,
            ),
        );

        $this->addRules(
            'value',
            new StringRule(
                maxLength: 250,
            ),
        );
    }
}
