<?php
namespace Pyncer\Snyppet\Content\Component\Forge;

use Pyncer\App\Identifier as ID;
use Pyncer\Snyppet\Content\Component\Forge\DirTrait;
use Pyncer\Snyppet\Content\Table\Content\ContentModel;
use Pyncer\Snyppet\Content\Table\Content\ContentMapper;
use Pyncer\Snyppet\Content\Volume\VolumeFile;
use Pyncer\Validation\Rule\AliasRule;

use const Pyncer\Snyppet\Content\ALIAS_ALLOW_NUMERIC_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_LOWER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UPPER_CASE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_ALLOW_UNICODE_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_SEPARATOR_CHARACTERS as PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS;
use const Pyncer\Snyppet\Content\ALIAS_REPLACEMENT_CHARACTER as PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER;

trait InsertContentFileTrait
{
    use DirTrait;

    protected function insertContentFile(
        VolumeFile $volumeFile,
        ?ContentModel $parentContentModel
    ): ContentModel
    {
        $connection = $this->get(ID::DATABASE);

        $alias = $volumeFile->getFilename(true);

        $aliasRule = new AliasRule(
            allowNumericCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_NUMERIC_CHARACTERS,
            allowLowerCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_LOWER_CASE_CHARACTERS,
            allowUpperCaseCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UPPER_CASE_CHARACTERS,
            allowUnicodeCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_ALLOW_UNICODE_CHARACTERS,
            separatorCharacters: PYNCER_SNYPPET_CONTENT_ALIAS_SEPARATOR_CHARACTERS,
            replacementCharacter: PYNCER_SNYPPET_CONTENT_ALIAS_REPLACEMENT_CHARACTER,
        );

        if ($aliasRule->isValid($alias)) {
            $alias = $aliasRule->clean($alias);
        } else {
            $alias = pyncer_uid();
        }

        $contentMapper = new ContentMapper($connection);

        $contentModel = new ContentModel([
            'parent_id' => $parentContentModel?->getId(),
            'volume_id' => $volumeFile->getVolume()->getId(),
            'type' => 'file',
            'alias' => $alias,
            'uri' => $volumeFile->getUri(),
            'filename' => $volumeFile->getFilename(true),
            'extension' => $volumeFile->getExtension(),
            'name' => $volumeFile->getName(),
            'enabled' => true,
        ]);

        $contentMapper->insert($contentModel);

        return $contentModel;
    }
}
