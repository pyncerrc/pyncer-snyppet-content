<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Data\Validation\Rule\IdRule;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\StringRule;

class DataValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'content_id',
            new IdRule(
                mapper: new ContentMapper($this->getConnection()),
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
            'type',
            new StringRule(
                maxLength: 125,
            ),
        );

        $this->addRules(
            'value',
            new StringRule(
                maxLength: 400000,
            ),
        );
    }
}
