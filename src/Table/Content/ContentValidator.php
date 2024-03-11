<?php
namespace Pyncer\Snyppet\Content\Table\Content;

use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Data\Validation\AbstractValidator;
use Pyncer\Data\Validation\Rule\IdRule;
use Pyncer\Database\ConnectionInterface;
use Pyncer\Validation\Rule\AliasRule;
use Pyncer\Validation\Rule\BoolRule;
use Pyncer\Validation\Rule\DateTimeRule;
use Pyncer\Validation\Rule\IntRule;
use Pyncer\Validation\Rule\StringRule;

use const Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER as PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER;

class ContentValidator extends AbstractValidator
{
    public function __construct(ConnectionInterface $connection)
    {
        parent::__construct($connection);

        $this->addRules(
            'parent_id',
            new IdRule(
                mapper: new ContentMapper($this->getConnection()),
                allowNull: true,
            ),
        );

        $this->addRules(
            'mark',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );

        $this->addRules(
            'insert_date_time',
            new DateTimeRule(),
        );

        $this->addRules(
            'update_date_time',
            new DateTimeRule(
                allowNull: true,
            ),
        );

        $this->addRules(
            'order',
            new IntRule(
                allowNull: true,
            ),
        );

        $this->addRules(
            'type',
            new StringRule(
                maxLength: 125,
            ),
        );

        $this->addRules(
            'alias',
            new AliasRule(
                allowNumericCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS,
                allowLowerCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS,
                allowUpperCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS,
                allowUnicodeCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS,
                separatorCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS,
                replacementCharacter: PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER,
            ),
            new StringRule(
                maxLength: 125,
                allowNull: true,
            ),
        );

        $this->addRules(
            'file',
            new StringRule(
                maxLength: 250,
                allowNull: true,
            ),
        );

        $this->addRules(
            'filename',
            new StringRule(
                maxLength: 125,
                allowNull: true,
            ),
        );

        $this->addRules(
            'extension',
            new StringRule(
                maxLength: 50,
                allowNull: true,
            ),
        );

        $this->addRules(
            'name',
            new StringRule(
                maxLength: 250,
            ),
        );

        $this->addRules(
            'iteration',
            new IntRule(
                allowNull: true,
            ),
        );

        $this->addRules(
            'enabled',
            new BoolRule(),
        );

        $this->addRules(
            'deleted',
            new BoolRule(),
        );
    }
}
