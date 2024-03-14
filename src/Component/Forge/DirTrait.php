<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Validation\Rule\AliasRule;

use const DIRECTORY_SEPARATOR as DS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER as PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER;

trait DirTrait
{
    protected function getParentContentDir(string $path): ContentModel
    {
        $connection = $this->get(ID::DATABASE);
        $contentDataTree = $this->get(ID::content());

        if (DS !== '/') {
            $path = str_replace(DS, '/', $path);
        }

        $path = trim($path, '/');

        $path = explode('/', $path);

        $parentId = null;

        $mapper = new ContentMapper($connection);

        foreach ($path as $value) {
            if (!$contentDataTree->hasItemFromDirPath($value, $parentId)) {
                $aliasRule = new AliasRule(
                    allowNumericCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS,
                    allowLowerCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS,
                    allowUpperCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS,
                    allowUnicodeCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS,
                    separatorCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS,
                    replacementCharacter: PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER,
                );

                if (!$aliasRule->isValid($value) || $value !== $aliasRule->clean($value)) {
                    throw new InvalidArgumentException(
                        'Path is invalid. ('. $path . ')'
                    );
                }

                $contentModel = new ContentModel([
                    'parent_id' => $parentId,
                    'type' => 'dir',
                    'alias' => $value,
                    'name' => $value,
                    'enabled' => true,
                ]);
                $mapper->insert($contentModel);

                $parentId = $contentModel->getId();
            } else {
                $contentModel = $contentDataTree->getItemFromDirPath(
                    $value,
                    $parentId
                );
                $parentId = $contentModel->getId();
            }
        }

        return $contentModel;
    }
}
