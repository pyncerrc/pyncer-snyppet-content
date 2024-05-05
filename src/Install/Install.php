<?php
namespace Pyncer\Snyppet\Content\Install;

use Pyncer\Database\Table\Column\IntSize;
use Pyncer\Database\Table\Column\TextSize;
use Pyncer\Database\Table\ReferentialAction;
use Pyncer\Database\Value;
use Pyncer\Snyppet\AbstractInstall;
use Pyncer\Snyppet\Config\ConfigManager;

class Install extends AbstractInstall
{
    /**
     * @inheritdoc
     */
    protected function safeInstall(): bool
    {
        $this->connection->createTable('content')
            ->serial('id')
            ->int('parent_id', IntSize::BIG)->null()->index()
            ->int('volume_id', IntSize::BIG)->null()->index()
            ->string('mark', 250)->null()->index()
            ->dateTime('insert_date_time')->default(Value::NOW)->index()
            ->dateTime('update_date_time')->null()->index()
            ->int('order')->null()->index()
            ->string('type', 125)->index()
            ->string('alias', 125)->null()->index()
            ->string('uri', 250)->null()
            ->string('filename', 125)->null()->index()
            ->string('extension', 50)->null()->index()
            ->string('name', 250)->index()
            ->int('iteration')->null()
            ->bool('enabled')->default(false)->index()
            ->bool('deleted')->default(false)->index()
            ->foreignKey(null, 'parent_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('content__data')
            ->serial('id')
            ->int('content_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('type', 125)->index()
            ->text('value', TextSize::MEDIUM)
            ->index('#unique', 'content_id', 'key')->unique()
            ->foreignKey(null, 'content_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('content__value')
            ->serial('id')
            ->int('content_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('value', 250)
            ->index('#unique', 'content_id', 'key')->unique()
            ->foreignKey(null, 'content_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('content__file__table')
            ->serial('id')
            ->string('table', 250)->index()
            ->string('column', 250)->index()
            ->execute();

        $this->connection->createTable('content__history')
            ->serial('id')
            ->int('content_id', IntSize::BIG)->index()
            ->int('parent_id', IntSize::BIG)->null()->index()
            ->int('volume_id', IntSize::BIG)->null()->index()
            ->string('mark', 250)->null()->index()
            ->dateTime('insert_date_time')->default(Value::NOW)->index()
            ->dateTime('update_date_time')->null()->index()
            ->int('order')->null()->index()
            ->string('type', 125)->index()
            ->string('alias', 125)->null()->index()
            ->string('uri', 250)->null()
            ->string('filename', 125)->null()->index()
            ->string('extension', 50)->null()->index()
            ->string('name', 250)->index()
            ->int('iteration')->null()
            ->bool('enabled')->default(false)->index()
            ->bool('deleted')->default(false)->index()
            ->foreignKey(null, 'content_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::NO_ACTION)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'parent_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::NO_ACTION)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('content__history__data')
            ->serial('id')
            ->int('history_id', IntSize::BIG)->index()
            ->int('content_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('type', 125)->index()
            ->text('value', TextSize::MEDIUM)
            ->index('#unique', 'history_id', 'content_id', 'key')->unique()
            ->foreignKey(null, 'history_id')
                ->references('content__history', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'content_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::NO_ACTION)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->createTable('content__history__value')
            ->serial('id')
            ->int('history_id', IntSize::BIG)->index()
            ->int('content_id', IntSize::BIG)->index()
            ->string('key', 50)->index()
            ->string('value', 250)
            ->index('#unique', 'history_id', 'content_id', 'key')->unique()
            ->foreignKey(null, 'history_id')
                ->references('content__history', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'content_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::NO_ACTION)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $query = $this->connection->createTable('content__image__filter')
            ->serial('id')
            ->string('alias', 50)->index()
            ->string('name', 50)
            ->enum('fit', ['inside', 'outside', 'fill'])->default('inside')
            ->enum('scale', ['down', 'up', 'any'])->default('down')
            ->enum('crop_x', ['left', 'center', 'right'])->default('center')
            ->enum('crop_y', ['top', 'center', 'bottom'])->default('center')
            ->bool('padding')->default(false)
            ->string('background_color', 50)->null()
            ->execute();

        $query = $this->connection->createTable('content__image__filter__override')
            ->serial('id')
            ->int('image_id', IntSize::BIG)->index()
            ->int('filter_id', IntSize::BIG)->index()
            ->enum('fit', ['inside', 'outside', 'fill'])->null()
            ->enum('scale', ['down', 'up', 'any'])->null()
            ->enum('crop_x', ['left', 'center', 'right', 'exact'])->null()
            ->enum('crop_y', ['top', 'center', 'bottom', 'exact'])->null()
            ->int('crop_x_offset')->null()
            ->int('crop_y_offset')->null()
            ->int('crop_x_width')->null()
            ->int('crop_y_width')->null()
            ->bool('padding')->null()
            ->string('background_color', 50)->null()
            ->foreignKey(null, 'image_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'filter_id')
                ->references('content__image__filter', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $query = $this->connection->createTable('content__image__filter__watermark')
            ->serial('id')
            ->int('image_id', IntSize::BIG)->index()
            ->int('filter_id', IntSize::BIG)->index()
            ->int('filter_size_min_width')
            ->int('filter_size_min_height')
            ->enum('position_x', ['left', 'center', 'right'])->default('center')
            ->enum('position_y', ['top', 'center', 'bottom'])->default('center')
            ->int('offset_x')->default(0)
            ->int('offset_y')->default(0)
            ->enum('size_unit', ['pixel', 'percent'])->default('pixel')
            ->int('size_max_width')->null()
            ->int('size_max_height')->null()
            ->int('opacity')->null()
            ->foreignKey(null, 'image_id')
                ->references('content', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->foreignKey(null, 'filter_id')
                ->references('content__image__filter', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $query = $this->connection->createTable('content__image__size')
            ->serial('id')
            ->int('filter_id', IntSize::BIG)->index()
            ->string('alias', 50)->index()
            ->string('name', 50)
            ->int('width')
            ->int('height')
            ->foreignKey(null, 'filter_id')
                ->references('content__image__filter', 'id')
                ->deleteAction(ReferentialAction::CASCADE)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $query = $this->connection->createTable('content__image__set')
            ->serial('id')
            ->string('alias', 50)->index()
            ->string('name', 50)
            ->text('sizes')
            ->bool('upscale')->default(false)
            ->execute();

        $query = $this->connection->createTable('content__volume')
            ->serial('id')
            ->int('cache_id', IntSize::BIG)->null()->index()
            ->string('alias', 50)->index()
            ->string('name', 50)->index()
            ->string('volume', 50)->index()
            ->string('path', 250)->null()
            ->text('params')->null()
            ->bool('enabled')->default(false)->index()
            ->foreignKey(null, 'cache_id')
                ->references('content__volume', 'id')
                ->deleteAction(ReferentialAction::SET_NULL)
                ->updateAction(ReferentialAction::CASCADE)
            ->execute();

        $this->connection->insert('content__volume')
            ->values([
                'alias' => 'local',
                'name' => 'Local',
                'volume' => 'Local',
                'path' => null,
                'params' => json_encode([
                    'file_path' => '${Pyncer__Snyppet__Content__FILE_PATH}',
                    'cache_path' => '${Pyncer__Snyppet__Content__CACHE_PATH}',
                ]),
                'enabled' => true,
            ])
            ->execute();

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function safeUninstall(): bool
    {
        if ($this->connection->hasTable('content__volume')) {
            $this->connection->dropTable('content__volume');
        }

        if ($this->connection->hasTable('content__image__set')) {
            $this->connection->dropTable('content__image__set');
        }

        if ($this->connection->hasTable('content__image__size')) {
            $this->connection->dropTable('content__image__size');
        }

        if ($this->connection->hasTable('content__image__set')) {
            $this->connection->dropTable('content__image__set');
        }

        if ($this->connection->hasTable('content__image__filter__watermark')) {
            $this->connection->dropTable('content__image__filter__watermark');
        }

        if ($this->connection->hasTable('content__image__filter__override')) {
            $this->connection->dropTable('content__image__filter__override');
        }

        if ($this->connection->hasTable('content__history__value')) {
            $this->connection->dropTable('content__history__value');
        }

        if ($this->connection->hasTable('content__history__data')) {
            $this->connection->dropTable('content__history__data');
        }

        if ($this->connection->hasTable('content__history')) {
            $this->connection->dropTable('content__history');
        }

        if ($this->connection->hasTable('content__history__data')) {
            $this->connection->dropTable('content__history__data');
        }

        if ($this->connection->hasTable('content__file__table')) {
            $this->connection->dropTable('content__file__table');
        }

        if ($this->connection->hasTable('content__value')) {
            $this->connection->dropTable('content__value');
        }

        if ($this->connection->hasTable('content__data')) {
            $this->connection->dropTable('content__data');
        }

        if ($this->connection->hasTable('content')) {
            $this->connection->dropTable('content');
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function hasRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function installRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return $this->installConfig();
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function uninstallRelated(string $snyppetAlias): bool
    {
        switch ($snyppetAlias) {
            case 'config':
                return $this->installConfig();
        }

        return false;
    }

    protected function installConfig(): bool
    {
        $config = new ConfigManager($this->connection);

        if (!$config->has('content_placeholder_image_id')) {
            $config->set('content_placeholder_image_id', 0);
            $config->setPreload('content_placeholder_image_id', true);
            $config->save('content_placeholder_image_id');
        }

        return true;
    }

    protected function uninstallConfig(): bool
    {
        $config = new ConfigManager($this->connection);

        if (!$config->has('content_placeholder_image_id')) {
            $config->set('content_placeholder_image_id', null);
            $config->save('content_placeholder_image_id');
        }

        return true;
    }
}
