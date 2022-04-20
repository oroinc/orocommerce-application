<?php

namespace Training\Bundle\UserNamingBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\EntityBundle\EntityConfig\DatagridScope;
use Oro\Bundle\EntityExtendBundle\EntityConfig\ExtendScope;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtension;
use Oro\Bundle\EntityExtendBundle\Migration\Extension\ExtendExtensionAwareInterface;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use Training\Bundle\UserNamingBundle\Form\Type\UserNamingCreateOrSelectType;

class UserNamingBundleInstaller implements
    Installation,
    ExtendExtensionAwareInterface
{
    /** @var ExtendExtension */
    protected $extendExtension;

    public function setExtendExtension(ExtendExtension $extendExtension)
    {
        $this->extendExtension = $extendExtension;
    }
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createUserNamingTypeTable($schema);

        /** Foreign keys generation **/
        $this->addUserNamingTypeKeys($schema);
    }

    /**
     * @param Schema $schema
     * @return void
     */
    public function createUserNamingTypeTable(Schema $schema): void
    {
        $table = $schema->createTable('training_user_naming_type');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('user_owner_id', 'integer', ['notnull' => false]);
        $table->addColumn('organization_id', 'integer', ['notnull' => false]);
        $table->addColumn('title', 'string', ['length' => 64, 'notnull' => true]);
        $table->addColumn('format', 'string', ['length' => 255, 'notnull' => true]);
        $table->addColumn('created_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->addColumn('updated_at', 'datetime', ['comment' => '(DC2Type:datetime)']);
        $table->setPrimaryKey(['id']);

        $this->extendExtension->addManyToOneRelation(
            $schema,
            'oro_user',
            'naming_type',
            $table,
            'title',
            [
                'extend' => [
                    'owner' => ExtendScope::OWNER_CUSTOM,
                    'nullable' => true,
                    'on_delete' => 'SET NULL'
                ],
                'datagrid' => ['is_visible' => DatagridScope::IS_VISIBLE_TRUE],
                'form' => [
                    'is_enabled' => true,
                    'form_type' => UserNamingCreateOrSelectType::class,
                    'form_options' => ['required' => false]
                ],
                'view' => ['is_displayable' => true],
                'dataaudit' => ['auditable' => true],
            ]
        );
    }

    protected function addUserNamingTypeKeys(Schema $schema)
    {
        $table = $schema->getTable('training_user_naming_type');
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_user'),
            ['user_owner_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('oro_organization'),
            ['organization_id'],
            ['id'],
            ['onUpdate' => null, 'onDelete' => 'SET NULL']
        );
    }
}
